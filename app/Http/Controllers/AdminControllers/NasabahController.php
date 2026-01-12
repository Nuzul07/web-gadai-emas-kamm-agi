<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Nasabah;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/MasterData/Nasabah');
    }

    public function getAll()
    {
        $nas = Nasabah::all();
        return $nas;
    }

    public function getProvinsi()
    {
        $prov = Provinsi::all();
        return $prov;
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
            'no_ktp' => 'Input menggunakan angka !',
            'no_tlp_hp.numeric' => 'Input menggunakan angka !',
            'no_tlp_rm.numeric' => 'Input menggunakan angka !',
            'no_rekening' => 'Input menggunakan angka !',
            'cad1.required' => 'Foto Ktp harus diupload !',
            'cad1.mimes' => 'Foto Ktp harus berupa JPG, JPEG, dan PNG !',
            'cad1.max' => 'Foto Ktp maksimal berukuran 2MB !',
            'cad2.required' => 'Foto Nasabah harus diupload !',
            'cad2.mimes' => 'Foto Nasabah harus berupa JPG, JPEG, dan PNG !',
            'cad2.max' => 'Foto Nasabah maksimal berukuran 2MB !',
        ];

        $request->validate([
            'nama' => 'required|string',
            'alamat_dms' => 'required|string',
            'provinsi_dms' =>  'required|string',
            'kabupaten_dms' => 'required|string',
            'kecamatan_dms' => 'required|string',
            'kelurahan_dms' => 'required|string',
            'kodepos_dms' => 'required|numeric',
            'no_ktp' => 'required|numeric',
            'tempat_lahir' => 'required|string',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|string',
            'alamat_ktp' => 'required|string',
            'provinsi_ktp' => 'required|string',
            'kabupaten_ktp' => 'required|string',
            'kecamatan_ktp' => 'required|string',
            'kelurahan_ktp' => 'required|string',
            'kodepos_ktp' => 'required|numeric',
            'no_tlp_rm' => 'required|numeric',
            'no_tlp_hp' => 'required|numeric',
            'nama_ibu' => 'required|string',
            'nama_kerabat' => 'required|string',
            'hub_kerabat' => 'required|string',
            'alamat_kerabat' => 'required|string',
            'provinsi_kerabat' => 'required|string',
            'kabupaten_kerabat' => 'required|string',
            'kecamatan_kerabat' => 'required|string',
            'kelurahan_kerabat' => 'required|string',
            'kodepos_kerabat' => 'required|numeric',
            'email' => 'required|string',
            'no_rekening' => 'required|numeric',
            'nama_rekening' => 'required|string',
            'nama_bank' => 'required|string',
            'cad1' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Ktp";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'cad2' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Nasabah";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
        ], $alert);

        function getNextSequenceNumber($posko, $month, $year)
        {
            // Retrieve the highest existing sequence number for the current month and year from the secondary database
            $latestEntry = DB::connection('second_sqlsrv')->table('tb_kons')
                ->where(DB::raw('SUBSTRING(K_Kons, 1, 2)'), '=', $posko)
                ->where(DB::raw('SUBSTRING(K_Kons, 3, 2)'), '=', $month)
                ->where(DB::raw('SUBSTRING(K_Kons, 5, 2)'), '=', $year)
                ->orderBy(DB::raw('CAST(SUBSTRING(K_Kons, 7, 4) AS INT)'), 'desc')
                ->first();

            if ($latestEntry) {
                // Extract the sequence part from the K_Kons value
                $latestSequence = (int)substr($latestEntry->K_Kons, 6, 4);
                $nextSequence = $latestSequence + 1;
            } else {
                $nextSequence = 1;
            }

            // Ensure the sequence number is 4 digits
            return str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }

        $posko = substr($request->user()->POSKO, -2);
        $month = Carbon::now()->format('m');
        $year = Carbon::now()->format('y');
        $sequenceNumber = getNextSequenceNumber($posko, $month, $year);

        $nas = new Nasabah();
        $k_kons = $posko . $month . $year . $sequenceNumber;
        $nas->K_Kons = $k_kons;
        $nas->Nama = $request->nama;
        $nas->Alamat_Domisili = $request->alamat_dms;
        $nas->Propinsi_Domisili = $request->provinsi_dms;
        $nas->kabupaten_Domisili = $request->kabupaten_dms;
        $nas->Kecamatan_Domisili = $request->kecamatan_dms;
        $nas->Kelurahan_Domisili = $request->kelurahan_dms;
        $nas->Kodepos_Domisili = $request->kodepos_dms;
        $nas->No_ktp = $request->no_ktp;
        $nas->Tempat_Lahir = $request->tempat_lahir;
        $nas->Tgl_Lahir = $request->tgl_lahir;
        $nas->Jenis_Kelamin = $request->jenis_kelamin;
        $nas->Alamat_ktp = $request->alamat_ktp;
        $nas->Propinsi_ktp = $request->provinsi_ktp;
        $nas->kabupaten_ktp = $request->kabupaten_ktp;
        $nas->Kecamatan_ktp = $request->kecamatan_ktp;
        $nas->Kelurahan_ktp = $request->kelurahan_ktp;
        $nas->Kodepos_ktp = $request->kodepos_ktp;
        $nas->No_tlp_rm = "+62" . $request->no_tlp_rm;
        $nas->No_tlp_HP = "+62" . $request->no_tlp_hp;
        $nas->Nama_Ibu = $request->nama_ibu;
        $nas->Nama_Kerabat = $request->nama_kerabat;
        $nas->Hub_Kerabat = $request->hub_kerabat;
        $nas->Alamat_Kerabat = $request->alamat_kerabat;
        $nas->Propinsi_Kerabat = $request->provinsi_kerabat;
        $nas->kabupaten_Kerabat = $request->kabupaten_kerabat;
        $nas->Kecamatan_Kerabat = $request->kecamatan_kerabat;
        $nas->Kelurahan_Kerabat = $request->kelurahan_kerabat;
        $nas->Kodepos_Kerabat = $request->kodepos_kerabat;
        $nas->Trans_ke = 1;
        $nas->Posko = $request->user()->POSKO;
        $nas->Cabang = $request->user()->CABANG;
        $nas->email = $request->email;
        $nas->no_rekening = $request->no_rekening;
        $nas->nama_rekening = $request->nama_rekening;
        $nas->nama_bank = $request->nama_bank;
        $nas->tgl_regis = Carbon::now();

        $img = $request->file('cad1');
        $imgName = sprintf('%s-%s.%s', $k_kons, "FotoKTP", $img->getClientOriginalExtension());
        $img->storeAs('public/picture/KtpPhoto/', $imgName);
        $nas->cad1 = $imgName;

        $img1 = $request->file('cad2');
        $imgName1 = sprintf('%s-%s.%s', $k_kons, "FotoNasabah", $img->getClientOriginalExtension());
        $img1->storeAs('public/picture/NasabahPhoto/', $imgName1);
        $nas->cad2 = $imgName1;

        $nas->save();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function show(Nasabah $nasabah)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function edit(Nasabah $nasabah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requestuest  $rcad2st
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nasabah $nasabah)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nasabah $nasabah)
    {
        //
    }
}