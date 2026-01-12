<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Hsp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HspController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/MasterData/Hsp');
    }

    public function getAll()
    {
        $hsp = Hsp::whereNot(function ($query) {
            $query->where('kode_kendaraan', 'like', '%L%')
                ->orWhere('kode_kendaraan', 'like', '%-S%')
                ->orWhere('kode_kendaraan', 'like', '%_23%');
        })->get();
        return $hsp;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $alert = [
            'kode_stnk.required' => 'Kode Stnk harus diisi !',
            'kode_kendaraan.required' => 'Kode Kendaraan harus diisi !',
            'tahun.required' => 'Tahun harus diisi !',
            'nilai.required' => 'Nilai harus diisi !',
            'nama_barang.required' => 'Nama Barang harus diisi !',
        ];

        $request->validate([
+            'kode_stnk' => 'required|string',
            'kode_kendaraan' => 'required|string',
            'tahun' => 'required|numeric',
            'nilai' => 'required|string',
            'nama_barang' => 'required|string',
        ], $alert);

        $inputKodeStnk = preg_replace('/\s+/', '', $request->kode_stnk);
        $inputKodeKendaraan = preg_replace('/\s+/', '', $request->kode_kendaraan);
        $inputTahun = preg_replace('/\s+/', '', $request->tahun);
        $inputNamaBarang = preg_replace('/\s+/', '', $request->nama_barang);

        $exists = Hsp::whereRaw("REPLACE(kode_stnk, ' ', '') = ?", [$inputKodeStnk])
            ->whereRaw("REPLACE(kode_kendaraan, ' ', '') = ?", [$inputKodeKendaraan])
            ->whereRaw("REPLACE(tahun, ' ', '') = ?", [$inputTahun])
            ->whereRaw("REPLACE(nama_barang, ' ', '') = ?", [$inputNamaBarang])
            ->first();

        if ($exists) {
            return response()->json([
                'status' => false
            ]);
        }

        $hsp = new Hsp();
        $hsp->kode_stnk = $request->kode_stnk;
        $hsp->kode_kendaraan = $request->kode_kendaraan;
        $hsp->tahun = $request->tahun;
        $hsp->nilai = $request->nilai;
        $hsp->nama_barang = $request->nama_barang;
        $hsp->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hsp  $hsp
     * @return \Illuminate\Http\Response
     */
    public function show(Hsp $hsp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hsp  $hsp
     * @return \Illuminate\Http\Response
     */
    public function edit(Hsp $hsp)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hsp  $hsp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'nilai' => 'required|numeric',
            'nama_barang' => 'required|string',
        ]);

        $hsp = Hsp::where('kode_stnk', $request->kode_stnk)
            ->where('kode_kendaraan', $request->kode_kendaraan)
            ->where('tahun', $request->tahun)
            ->first();

        $hsp->nilai = $request->nilai;
        $hsp->nama_barang = $request->nama_barang;
        $hsp->update();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hsp  $hsp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $kode_stnk = $request->kode_stnk;
        $kode_kendaraan = $request->kode_kendaraan;
        $tahun = $request->tahun;

        // Validate input
        if (!$kode_stnk || !is_array($kode_stnk) || !$kode_kendaraan || !is_array($kode_kendaraan) || !$tahun || !is_array($tahun)) {
            return response()->json(['message' => 'Invalid request'], 400);
        }

        // Prepare array of data
        $deleteData = [];
        foreach ($kode_stnk as $index => $stnk) {
            $deleteData[] = [
                'kode_stnk' => $stnk,
                'kode_kendaraan' => $kode_kendaraan[$index],
                'tahun' => $tahun[$index]
            ];
        }

        DB::connection('second_sqlsrv')->table('tb_plafon')->whereExists(function ($query) use ($deleteData) {
            $query->select(DB::raw(1))
                ->from(DB::raw('(VALUES ' . implode(',', array_map(fn($data) => "('" . $data['kode_stnk'] . "','" . $data['kode_kendaraan'] . "'," . $data['tahun'] . ")", $deleteData)) . ') AS tempTable(kode_stnk, kode_kendaraan, tahun)'))
                ->whereColumn('tb_plafon.kode_stnk', 'tempTable.kode_stnk')
                ->whereColumn('tb_plafon.kode_kendaraan', 'tempTable.kode_kendaraan')
                ->whereColumn('tb_plafon.tahun', 'tempTable.tahun');
        })->delete();

        return response()->json(['message' => 'Selected items deleted successfully']);
    }
}