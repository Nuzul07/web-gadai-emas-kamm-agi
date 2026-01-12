<?php

namespace App\Http\Controllers\AdminControllers;

use App\Helpers\CustomHelpers;
use App\Http\Controllers\Controller;
use App\Models\BarangJaminan;
use App\Models\Lelang;
use App\Models\PerGadaiOffline;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LelangController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Transactions/Lelang');
    }

    public function indexValidasi()
    {
        return Inertia::render('Admin/Transactions/ValidasiLelang');
    }

    public function indexInput()
    {
        return Inertia::render('Admin/Transactions/InputLelang');
    }

    public function getLelang()
    {
        // Step 1: Perform the grouping and get distinct NOMOR_LELANG
        $distinctLelang = Lelang::select('NOMOR_LELANG', DB::raw('MAX(No_sbg) as No_sbg'), DB::raw('MAX(NAMA_KONS) as NAMA_KONS'), DB::raw('MAX(NAMA_BARANG) as NAMA_BARANG'), DB::raw('MAX(TANGGAL_INPUT) as TANGGAL_INPUT'))
            ->where('Status', "")
            ->groupBy('NOMOR_LELANG')
            ->orderBy('TANGGAL_INPUT', 'desc')
            ->get();

        return $distinctLelang;
    }

    public function getLelangApprove()
    {
        // Step 1: Perform the grouping and get distinct NOMOR_LELANG
        $distinctLelang = Lelang::select('NOMOR_LELANG', DB::raw('MAX(TANGGAL_INPUT) as TANGGAL_INPUT'))
            ->where('Status', "A")
            ->groupBy('NOMOR_LELANG')
            ->orderBy('TANGGAL_INPUT', 'desc')
            ->get();

        // dd($distinctLelang);

        return $distinctLelang;
    }

    public function getLelangByNomor(Request $request)
    {
        $nomorLelang = $request->nomorLelang;

        // Fetch all records with the same NOMOR_LELANG
        $records = Lelang::with(['barang'])
            ->where('NOMOR_LELANG', $nomorLelang)
            ->get();

        // dd($records);

        return response()->json($records);
    }

    public function getLelangByNomorApprove(Request $request)
    {
        $nomorLelang = $request->nomorLelang;

        // Fetch all records with the same NOMOR_LELANG
        $records = Lelang::with(['barang'])
            ->where('NOMOR_LELANG', $nomorLelang)
            ->where('Status', "A")
            ->get();

        // dd($records);

        return response()->json($records);
    }


    public function getNoLelang(Request $request)
    {
        $no_lelang = CustomHelpers::getNoLelang($request);
        return $no_lelang;
    }

    public function getNoKwitansi()
    {
        $no_kwitansi = CustomHelpers::getNoKwitansi();
        return $no_kwitansi;
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'lelangTemp.*.No_sbg' => 'required|string',
            'lelangTemp.*.nomor_lelang' => 'required|string',
            'lelangTemp.*.k_kons' => 'required|string',
            'lelangTemp.*.nama_kons' => 'required|string',
            'lelangTemp.*.pokok_pinjaman' => 'nullable|numeric',
            'lelangTemp.*.fines' => 'nullable|numeric',
            'lelangTemp.*.nilai_buku' => 'nullable|numeric',
            'lelangTemp.*.nilai_lelang' => 'nullable|numeric',
            'lelangTemp.*.untung_rugi' => 'nullable|numeric',
            'lelangTemp.*.tgl_pinjam' => 'nullable|date',
            'lelangTemp.*.tenor' => 'nullable|integer',
            'lelangTemp.*.terlambat' => 'nullable|integer',
            'lelangTemp.*.asal_barang' => 'nullable|string',
            'lelangTemp.*.Kode_barang' => 'nullable|string',
            'lelangTemp.*.nama_pembeli' => 'nullable|string',
            'lelangTemp.*.alamat_pembeli' => 'nullable|string',
            'lelangTemp.*.no_telp' => 'nullable|string',
            'lelangTemp.*.no_hp' => 'nullable|string',
            'lelangTemp.*.kecamatan' => 'nullable|string',
            'lelangTemp.*.kelurahan' => 'nullable|string',
            'lelangTemp.*.kodepos' => 'nullable|string',
            'lelangTemp.*.Jenis' => 'nullable|string',
            'lelangTemp.*.keterangan' => 'required|string',
        ]);


        // Iterate over each item in the array and store it in the database
        foreach ($validatedData['lelangTemp'] as $item) {
            Lelang::create([
                'No_sbg' => $item['No_sbg'],
                'NOMOR_LELANG' => $item['nomor_lelang'],
                'No_kons' => $item['k_kons'],
                'NAMA_KONS' => $item['nama_kons'],
                'CABANG' => $request->user()->CABANG,
                'POSKO' => $request->user()->POSKO,
                'USER_INPUT' => $request->user()->USER_NAME,
                'NILAI_PINJAM' => $item['pokok_pinjaman'],
                'DENDA' => $item['fines'],
                'NILAI_BUKU' => $item['nilai_buku'],
                'NILAI_LELANG' => $item['nilai_lelang'],
                'UNTUNG_RUGI' => $item['untung_rugi'],
                'TANGGAL_PINJAM' => $item['tgl_pinjam'],
                'TANGGAL_LELANG' => Carbon::now(),
                'LAMA_PINJAM' => $item['tenor'],
                'HARI_KETERLAMBATAN' => $item['terlambat'],
                'ASALBARANG' => $item['asal_barang'],
                'Kode_barang' => $item['Kode_barang'],
                'NAMA_PEMBELI' => $item['nama_pembeli'],
                'ALAMAT_PEMBELI' => $item['alamat_pembeli'],
                'NO_TELP' => $item['no_telp'],
                'TANGGAL_INPUT' => Carbon::now(),
                'JAM_INPUT' => Carbon::now()->format('H:i:s'),
                'KETERANGAN' => $item['keterangan'],
                'NAMA_BARANG' => $item['Jenis'],
                'TGL_PRINT' => Carbon::now(),
                'JAM_PRINT' => Carbon::now()->format('H:i:s'),
                'USER_PRINT' => $request->user()->USER_NAME,
                'NO_HP' => $item['no_hp'],
                'KECAMATAN' => $item['kecamatan'],
                'KELURAHAN' => $item['kelurahan'],
                'KODE_POS' => $item['kodepos']
                // add other necessary fields...
            ]);

            PerGadaiOffline::where('No_sbg', $item['No_sbg'])->update([
                'Status' => 0
            ]);

            // Update BarangJaminan status for the corresponding Kode_barang
            BarangJaminan::where('Kode_barang', $item['Kode_barang'])->update([
                'Status_barang' => 0
            ]);
        }

        // Return success response
        return response()->json(['message' => 'Data stored successfully']);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'lelangTemp.*.kode_barang' => 'required|numeric',
            'lelangTemp.*.int_1' => 'required|numeric'
        ]);

        foreach ($validatedData['lelangTemp'] as $item) {
            // Determine status and APV based on int_1 value
            $status = $item['int_1'] == 1 ? 'A' : 'B';
            $apv = $item['int_1'] == 1 ? 1 : 0;

            // Update Lelang with shared fields and conditional fields
            Lelang::where('Kode_barang', $item['kode_barang'])->update([
                'STATUS' => $status,
                'TGL_APPV' => Carbon::now(),
                'USER_APPV' => $request->user()->USER_NAME,
                'JAM_APPV' => Carbon::now()->format('H:i:s'),
                'APV' => $apv,
            ]);
        }

        // Return a success response
        return response()->json(['message' => 'Data updated successfully!']);
    }

    public function inputLelangApprove(Request $request)
    {
        $validatedData = $request->validate([
            'lelangTemp.*.No_sbg' => 'required|string',
            'lelangTemp.*.NOMOR_LELANG' => 'required|string',
            'lelangTemp.*.No_kons' => 'required|string',
            'lelangTemp.*.NAMA_KONS' => 'required|string',
            'lelangTemp.*.NILAI_PINJAM' => 'nullable|numeric',
            'lelangTemp.*.DENDA' => 'nullable|numeric',
            'lelangTemp.*.NILAI_BUKU' => 'nullable|numeric',
            'lelangTemp.*.NILAI_LELANG' => 'nullable|numeric',
            'lelangTemp.*.UNTUNG_RUGI' => 'nullable|numeric',
            'lelangTemp.*.TANGGAL_PINJAM' => 'nullable|date',
            'lelangTemp.*.TANGGAL_BAYAR' => 'nullable|date',
            'lelangTemp.*.TENOR' => 'nullable|integer',
            'lelangTemp.*.HARI_KETERLAMBATAN' => 'nullable|integer',
            'lelangTemp.*.ASALBARANG' => 'nullable|string',
            'lelangTemp.*.Kode_barang' => 'nullable|string',
            'lelangTemp.*.NAMA_PEMBELI' => 'nullable|string',
            'lelangTemp.*.ALAMAT_PEMBELI' => 'nullable|string',
            'lelangTemp.*.NO_TELP' => 'nullable|string',
            'lelangTemp.*.NO_HP' => 'nullable|string',
            'lelangTemp.*.KECAMATAN' => 'nullable|string',
            'lelangTemp.*.KELURAHAN' => 'nullable|string',
            'lelangTemp.*.KODE_POS' => 'nullable|string',
            'lelangTemp.*.NAMA_BARANG' => 'nullable|string',
            'lelangTemp.*.NO_KWITANSI' => 'nullable|string',
            'lelangTemp.*.KETERANGAN' => 'required|string',
            'lelangTemp.*.CABANG' => 'required|string',
            'lelangTemp.*.POSKO' => 'required|string',
            'lelangTemp.*.USER_INPUT' => 'required|string',
        ]);
        // dd($validatedData);

        $daftarKodeBrg = [];
        $daftarNamaBrg = [];
        $sumNilaiLelang = 0;

        foreach ($validatedData['lelangTemp'] as $item) {
            $daftarKodeBrg[] = $item['Kode_barang']; // Adjust formatting as needed
            $daftarNamaBrg[] = $item['NAMA_BARANG'];
            $sumNilaiLelang += $item['NILAI_LELANG'];
        }

        DB::beginTransaction(); // Start the transaction

        try {
            DB::beginTransaction(); // Start the transaction

            // Execute the update first
            foreach ($validatedData['lelangTemp'] as $lelang) {
                // Update operation
                Lelang::where('NOMOR_LELANG', $lelang['NOMOR_LELANG'])
                    ->where('STATUS', 'A')->update([
                        'TXT_1' => $lelang['NO_KWITANSI']
                    ]);
            }

            // Now execute the stored procedure after the updates are done
            DB::connection('second_sqlsrv')->statement(
                'EXEC SP_INSLELANG @No_sbg = ?, @Tgl_trans = ?, @No_Lelang = ?, @Tgl_byr = ?, @Nilai_buku = ?, @Nilai_trans = ?, @Asal_brg = ?, @Tujuan_brg = ?, @Nama_pembeli = ?, @Alamat = ?, @No_tlp_pembeli = ?, @No_HP_pembeli = ?, @Daftar_Barang = ?, @Ket_standar = ?, @Ket_tambahan = ?, @cabang = ?, @posko = ?, @User = ?',
                [
                    $lelang['No_sbg'],
                    $lelang['TANGGAL_PINJAM'],
                    $lelang['NOMOR_LELANG'],
                    $lelang['TANGGAL_BAYAR'],
                    $lelang['NILAI_BUKU'],
                    $sumNilaiLelang,
                    $lelang['ASALBARANG'],
                    implode(', ', $daftarKodeBrg),
                    $lelang['NAMA_PEMBELI'],
                    $lelang['ALAMAT_PEMBELI'],
                    $lelang['NO_TELP'],
                    $lelang['NO_HP'],
                    implode(', ', $daftarNamaBrg),
                    $lelang['NO_KWITANSI'],
                    $lelang['KELURAHAN'] . "," . $lelang['KECAMATAN'] . "," . $lelang['KODE_POS'],
                    $lelang['CABANG'],
                    $lelang['POSKO'],
                    $lelang['USER_INPUT'],
                ]
            );
            // foreach ($validatedData['lelangTemp'] as $lelang) {
            //     DB::connection('second_sqlsrv')->statement(
            //         'EXEC SP_INSLELANG @No_sbg = ?, @Tgl_trans = ?, @No_Lelang = ?, @Tgl_byr = ?, @Nilai_buku = ?, @Nilai_trans = ?, @Asal_brg = ?, @Tujuan_brg = ?, @Nama_pembeli = ?, @Alamat = ?, @No_tlp_pembeli = ?, @No_HP_pembeli = ?, @Daftar_Barang = ?, @Ket_standar = ?, @Ket_tambahan = ?, @cabang = ?, @posko = ?, @User = ?',
            //         [
            //             $lelang['No_sbg'],
            //             $lelang['TANGGAL_PINJAM'],
            //             $lelang['NOMOR_LELANG'],
            //             $lelang['TANGGAL_BAYAR'],
            //             $lelang['NILAI_BUKU'],
            //             $lelang['NILAI_LELANG'],
            //             $lelang['ASALBARANG'],
            //             $lelang['Kode_barang'],
            //             $lelang['NAMA_PEMBELI'],
            //             $lelang['ALAMAT_PEMBELI'],
            //             $lelang['NO_TELP'],
            //             $lelang['NO_HP'],
            //             $lelang['NAMA_BARANG'],
            //             $lelang['NO_KWITANSI'],
            //             $lelang['KELURAHAN'] . "," . $lelang['KECAMATAN'] . "," . $lelang['KODE_POS'],
            //             $lelang['CABANG'],
            //             $lelang['POSKO'],
            //             $lelang['USER_INPUT'],
            //         ]
            //     );
            // }

            DB::commit(); // Commit the transaction if all operations are successful

            return response()->json(['message' => 'Data inserted successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return response()->json(['error' => 'Failed to insert data. Please try again. ' . $e->getMessage()], 500);
        }
    }
}
