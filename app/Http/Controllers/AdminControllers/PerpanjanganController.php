<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Bayar;
use App\Models\PerGadaiOffline;
use App\Helpers\CustomHelpers;
use App\Models\BarangJaminan;
use App\Models\BayarGadai;
use App\Models\HistorySetor;
use App\Models\HistoryTrans;
use App\Models\JaminanMotor;
use App\Models\Nasabah;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PerpanjanganController extends Controller
{

    public function index()
    {
        return Inertia::render('Admin/Transactions/Perpanjangan');
    }

    public function getGadaiEmasActive()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangEmas'])->where('Produk_gadai', 'EMAS')->whereIn('Status', [1, 2])->get();
        return $gd;
    }

    public function getGadaiMotorActive()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangMotor'])->where('Produk_gadai', 'MOTOR')->whereIn('Status', [1, 2])->get();
        return $gd;
    }

    public function getGadaiMobilActive()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangMobil'])->where('Produk_gadai', 'MOBIL')->whereIn('Status', [1, 2])->get();
        return $gd;
    }

    public function storeExtGadai(Request $request)
    {
        $alert = [
            'tenor' => 'Tenor harus diisi !',
            'cara_bayar' => 'Cara Bayar harus diisi !',
            'jenis_pembayaran' => 'Jenis Pembayaran harus diisi !',
            'foto_barang.required' => 'Foto Barang Jaminan Sisi Pertama harus diupload !',
            'foto_barang.*.max' => 'Foto Barang Jaminan Sisi Pertama maksimal berukuran 2MB !',
            'foto_barang2.required' => 'Foto Barang Jaminan Sisi Kedua harus diupload !',
            'foto_barang2.*.max' => 'Foto Barang Jaminan Sisi Kedua maksimal berukuran 2MB !',
            'lain_lain.required' => 'Foto Tambahan harus diupload !',
            'lain_lain.*.*.max' => 'Foto Tambahan maksimal berukuran 2MB !',
        ];

        $request->validate([
            'tenor' => 'required|numeric',
            'cara_bayar' => 'required|string',
            'jenis_pembayaran' => 'required|string',
            'foto_barang' => 'required|array', // Validate that `foto_barang` is an array and required
            'foto_barang.*' => [
                'required',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {  // Check if value is an array and flatten it
                        $value = collect($value)->flatten()->first();
                    }
                    $originalExtension = strtolower($value->getClientOriginalExtension());
                    if ($originalExtension === 'jfif') {
                        $fail('Foto Barang Jaminan Sisi Pertama harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                    }
                }
            ],
            'foto_barang2' => 'required|array', // Validate that `foto_barang2` is an array and required
            'foto_barang2.*' => [
                'required',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        $value = collect($value)->flatten()->first();
                    }
                    $originalExtension = strtolower($value->getClientOriginalExtension());
                    if ($originalExtension === 'jfif') {
                        $fail('Foto Barang Jaminan Sisi Kedua harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                    }
                }
            ],
            'lain_lain' => 'required|array', // Ensure `lain_lain` is an array
            'lain_lain.*.*' => [
                'required',
                'max:2048', // Max file size is 2MB
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $originalExtension = strtolower($value->getClientOriginalExtension());
                        if ($originalExtension === 'jfif') {
                            $fail('Foto Tambahan harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                        }
                    } else {
                        $fail('Invalid file upload.');
                    }
                }
            ],

        ], $alert);

        try {
            DB::transaction(function () use ($request) {
                $nasabah = Nasabah::where('K_Kons', $request->no_kons)->first();
                $newSbg = CustomHelpers::getNoSbgPjg($request);
                session(['no_sbg_pjg' => $newSbg]);

                // Save PerGadaiOffline()
                $tr = new PerGadaiOffline();
                $tr->fill([
                    'No_sbg' => $newSbg,
                    'No_kons' => $request->no_kons,
                    'Posko' => $request->user()->POSKO,
                    'Cabang' => $request->user()->CABANG,
                    'Tgl_transaksi' => Carbon::now(),
                    'Penaksir' => $request->penaksir,
                    'Type_transaksi' => $request->type_transaksi,
                    'Sewa_modal' => $request->sewa_modal / 100,
                    'Tgl_jtempo' => $request->new_tgl_jtempo,
                    'By_admin' => intval($request->by_admin),
                    'Tenor' => $request->tenor,
                    'Total_taksiran' => $request->total_taksiran,
                    'Pokok_pinjaman' => $request->pokok_pinjaman,
                    'Maks_pinjaman' => $request->maks_pinjaman,
                    'User_input' => $request->user()->USER_NAME,
                    'Status' => 2,
                    'Cara_pencairan' => $request->cara_pencairan,
                    'No_rek_nasabah' => $request->no_rek_nasabah,
                    'Referensi_nosbg' => $request->no_sbg,
                    'Asal_barang' => $request->asal_barang,
                    'Tujuan_transaksi' => $request->tujuan_transaksi,
                    'Instrumen_pembayaran' => $request->instrumen_pembayaran,
                    'Pengembalian_uang_lebih' => $request->pengembalian_uang_lebih,
                    'Fungsi_transaksi' => $request->fungsi_transaksi,
                    'Golongan' => $request->golongan,
                    'Bunga' => $request->bunga,
                    'Foto_identitas' => $request->foto_identitas,
                    'Terima_uang' => $request->terima_uang,
                    'Produk_gadai' => $request->produk_gadai,
                ]);
                $tr->save();

                $ppj = new Bayar();
                $noBayar = CustomHelpers::getNoBayar($request);
                $ppj->fill([
                    'No_bayaran' => $noBayar,
                    'Tgl_pelunasan' => Carbon::now(),
                    'No_sbg' => $request->no_sbg,
                    'Pokok_pinjaman' => $request->pokok_pinjaman,
                    'Sewa_modal' => $request->sewa_modal / 100,
                    'Denda_pinjaman' => $request->denda_pinjaman,
                    'Total_pembayaran' => $request->total_pembayaran,
                    'Cara_bayar' => $request->cara_bayar,
                    'Jenis_pembayaran' => $request->jenis_pembayaran,
                    'User_input' => $request->user()->USER_NAME,
                    'Jenis_transaksi' => "PERPANJANGAN",
                    'Piutang' => $request->bunga + $request->pokok_pinjaman,
                    'Bunga' => $request->bunga,
                ]);
                $ppj->save();

                $byrGd = new BayarGadai();
                $dateByr = DB::select(DB::raw("SELECT CAST(CAST(CAST(:date as date) as datetime) as int) + 693596 as numeric_date"), ['date' => Carbon::now()]);
                $dateJT = DB::select(DB::raw("SELECT cast(cast(cast(? as date) as datetime) as int)+693596 as numeric_date"), [$request->new_tgl_jtempo]);

                $byrGd->fill([
                    'No_bayar' => $noBayar,
                    'Tgl_byr' => $dateByr[0]->numeric_date,
                    'No_faktur' => $request->no_sbg,
                    'Jenis_trans' => "RAHN",
                    'Posko' => $request->user()->POSKO,
                    'Cabang' => $request->user()->CABANG,
                    'K_kons' => $request->no_kons,
                    'Tgl_due' => $dateJT[0]->numeric_date,
                    'Pokok' => $request->pokok_pinjaman,
                    'Bunga' => $request->bunga,
                    'Denda' => $request->denda_pinjaman ?? 0,
                    'Nilai_tebus' => $request->total_pembayaran,
                    'Hasil_diterima' => $request->total_pembayaran,
                    'TglTebus1' => $dateByr[0]->numeric_date,
                    'cad4' => $request->by_admin,
                ]);
                $byrGd->save();

                $his_setor = new HistorySetor();
                $date = DB::select(DB::raw("SELECT cast(cast(cast(getdate() as date) as datetime) as int)+693596 as numeric_date"));
                $dateOP = DB::select(DB::raw("SELECT cast(cast(cast(? as date) as datetime) as int)+693596 as numeric_date"), [Carbon::now()]);
                $his_setor->fill([
                    'No_setor' => "",
                    'No_trans' => $noBayar,
                    'Tgl_by' => $date[0]->numeric_date,
                    'Tgl_trans' => $dateOP[0]->numeric_date,
                    'Nama' => $nasabah->Nama,
                    'Nilai_rp' => $request->total_pembayaran,
                    'posko' => Auth::user()->POSKO,
                    'cabang' => Auth::user()->CABANG,
                    'Type_trans' => "LUNAS",
                    'Flag' => "",
                    'Tgl_setor' => null
                ]);
                $his_setor->save();

                $his_trans = new HistoryTrans();
                $his_trans->fill([
                    'NO_FAKTUR' => $request->no_sbg,
                    'NO_TRANSAKSI' => "",
                    'K_KONS' => $request->no_kons,
                    'NO_BAYAR' => $noBayar,
                    'USERID' => Auth::user()->RESERVED2_TXT,
                    'TGL_INPUT' => $date[0]->numeric_date,
                    'JAM_INPUT' => Carbon::now()->format("H:i:s"),
                    'SETOR' => "",
                    'POSKO' => Auth::user()->POSKO,
                    'CABANG' => Auth::user()->CABANG
                ]);
                $his_trans->save();

                // Check if kode_barang is an array
                // dd($request->all());
                if (is_array($request->barangJmn)) {
                    foreach ($request->barangJmn as $index => $item) {
                        $bj = new BarangJaminan();
                        $bj->No_sbg = $newSbg;
                        $bj->Kode_barang = $item['kode_barang'];
                        $maxSeq = BarangJaminan::max('Seq');
                        $bj->Seq = $maxSeq + 1;
                        $bj->Jenis = $item['jenis'];
                        $bj->Kadar = $item['kadar'];
                        $bj->Berat_kotor = $item['berat_kotor'];
                        $bj->Berat_bersih = $item['berat_bersih'];
                        $bj->Taksiran = $item['taksiran'];
                        $bj->Maks_pinjaman = $item['maks_pinjaman'];
                        $bj->Status_barang = 1;
                        $bj->Detail_barang = $item['detail_barang'];

                        // Handle the first image upload
                        if ($request->hasFile("foto_barang.$index")) {
                            $img = $request->file("foto_barang.$index");
                            $uniqueId = uniqid();
                            $sisi1 = "sisi-1";
                            $imgName = sprintf('%s-%s-%s.%s', $bj->No_sbg, $uniqueId, $sisi1, $img->getClientOriginalExtension());
                            $img->storeAs(sprintf('public/picture/BarangJaminan/Perpanjang/%s', $bj->No_sbg), $imgName);
                            $bj->Foto_barang = $imgName;
                        }

                        // Handle the second image upload
                        if ($request->hasFile("foto_barang2.$index")) {
                            $img2 = $request->file("foto_barang2.$index");
                            $sisi2 = "sisi-2";
                            $imgName2 = sprintf('%s-%s-%s.%s', $bj->No_sbg, $uniqueId, $sisi2, $img2->getClientOriginalExtension());
                            $img2->storeAs(sprintf('public/picture/BarangJaminan/Perpanjang/%s', $bj->No_sbg), $imgName2);
                            $bj->Foto_barang2 = $imgName2;
                        }

                        // Handle multiple additional images (lain_lain)
                        if ($request->hasFile("lain_lain.$index")) {
                            $seq = 1;
                            $imageNames = [];

                            // Ensure `lain_lain` is an array of files for the given index
                            $additionalImages = $request->file("lain_lain.$index");

                            if (is_array($additionalImages)) {
                                // Loop through each additional image file
                                foreach ($additionalImages as $img3) {
                                    $imgName3 = sprintf('%s-%s-%s-%s.%s', $bj->No_sbg, $bj->Kode_barang, $seq++, "Tambahan", $img3->getClientOriginalExtension());

                                    // Attempt to store the file
                                    $stored = $img3->storeAs(
                                        sprintf('public/picture/BarangJaminan/Perpanjang/%s/FotoTambahan/%s', $bj->No_sbg, $bj->Kode_barang),
                                        $imgName3
                                    );

                                    // Check if the file was stored successfully
                                    if ($stored) {
                                        $imageNames[] = $imgName3; // Add each stored image name to the array
                                    } else {
                                        Log::error('Failed to store Lain_lain image', ['image' => $imgName3]);
                                    }
                                }

                                // Store all additional image names as a JSON array in the `Lain_lain` field
                                $bj->Lain_lain = json_encode($imageNames);
                            } else {
                                Log::error('Lain_lain is not an array of files', ['index' => $index]);
                            }
                        }


                        // Save the record
                        $bj->save();
                    }
                } else {
                    return response()->json(['error' => 'Invalid input data.'], 422);
                }

                $gd = PerGadaiOffline::where('No_sbg', $request->no_sbg)->first();

                if ($gd) {
                    $gd->Status = 0;
                    $gd->save();
                } else {
                    throw new Exception('No_sbg tidak ditemukan untuk memperbarui status');
                }
            });
            
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'pesan' => $e->getMessage()
            ], 500);
        }
        // dd($request->all());
    }

    public function storeExtGadaiMotor(Request $request)
    {
        $alert = [
            'tenor' => 'Tenor harus diisi !',
            'cara_bayar' => 'Cara Bayar harus diisi !',
            'jenis_pembayaran' => 'Jenis Pembayaran harus diisi !',
            'foto_barang.required' => 'Foto Barang Jaminan Sisi Pertama harus diupload !',
            'foto_barang.*.max' => 'Foto Barang Jaminan Sisi Pertama maksimal berukuran 2MB !',
            'foto_barang2.required' => 'Foto Barang Jaminan Sisi Kedua harus diupload !',
            'foto_barang2.*.max' => 'Foto Barang Jaminan Sisi Kedua maksimal berukuran 2MB !',
            'lain_lain.required' => 'Foto Tambahan harus diupload !',
            'lain_lain.*.*.max' => 'Foto Tambahan maksimal berukuran 2MB !',
        ];

        $request->validate([
            'tenor' => 'required|numeric',
            'cara_bayar' => 'required|string',
            'jenis_pembayaran' => 'required|string',
            'foto_barang' => 'required|array', // Validate that `foto_barang` is an array and required
            'foto_barang.*' => [
                'required',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {  // Check if value is an array and flatten it
                        $value = collect($value)->flatten()->first();
                    }
                    $originalExtension = strtolower($value->getClientOriginalExtension());
                    if ($originalExtension === 'jfif') {
                        $fail('Foto Barang Jaminan Sisi Pertama harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                    }
                }
            ],
            'foto_barang2' => 'required|array', // Validate that `foto_barang2` is an array and required
            'foto_barang2.*' => [
                'required',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        $value = collect($value)->flatten()->first();
                    }
                    $originalExtension = strtolower($value->getClientOriginalExtension());
                    if ($originalExtension === 'jfif') {
                        $fail('Foto Barang Jaminan Sisi Kedua harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                    }
                }
            ],
            'lain_lain' => 'array', // Ensure `lain_lain` is an array
            'lain_lain.*.*' => [
                'max:2048', // Max file size is 2MB
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $originalExtension = strtolower($value->getClientOriginalExtension());
                        if ($originalExtension === 'jfif') {
                            $fail('Foto Tambahan harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                        }
                    } else {
                        $fail('Invalid file upload.');
                    }
                }
            ],

        ], $alert);

        try {
            DB::transaction(function () use ($request) {
                $newSbg = CustomHelpers::getNoSbgPjg($request);
                session(['no_sbg_pjg' => $newSbg]);

                // Save PerGadaiOffline()
                $tr = new PerGadaiOffline();
                $tr->fill([
                    'No_sbg' => $newSbg,
                    'No_kons' => $request->no_kons,
                    'Posko' => $request->user()->POSKO,
                    'Cabang' => $request->user()->CABANG,
                    'Tgl_transaksi' => Carbon::now(),
                    'Penaksir' => $request->penaksir,
                    'Type_transaksi' => $request->type_transaksi,
                    'Sewa_modal' => $request->sewa_modal / 100,
                    'Tgl_jtempo' => $request->new_tgl_jtempo,
                    'By_admin' => intval($request->by_admin),
                    'Tenor' => $request->tenor,
                    'Total_taksiran' => $request->total_taksiran,
                    'Pokok_pinjaman' => $request->pokok_pinjaman,
                    'Maks_pinjaman' => $request->maks_pinjaman,
                    'User_input' => $request->user()->USER_NAME,
                    'Status' => 2,
                    'Cara_pencairan' => $request->cara_pencairan,
                    'No_rek_nasabah' => $request->no_rek_nasabah,
                    'Referensi_nosbg' => $request->no_sbg,
                    'Asal_barang' => $request->asal_barang,
                    'Tujuan_transaksi' => $request->tujuan_transaksi,
                    'Instrumen_pembayaran' => $request->instrumen_pembayaran,
                    'Pengembalian_uang_lebih' => $request->pengembalian_uang_lebih,
                    'Fungsi_transaksi' => $request->fungsi_transaksi,
                    'Golongan' => $request->golongan,
                    'Bunga' => $request->bunga,
                    'Foto_identitas' => $request->foto_identitas,
                    'Terima_uang' => $request->terima_uang,
                    'Produk_gadai' => $request->produk_gadai,
                ]);
                $tr->save();

                // Save Bayar
                $ppj = new Bayar();
                $noBayar = CustomHelpers::getNoBayar($request);
                $ppj->fill([
                    'No_bayaran' => $noBayar,
                    'Tgl_pelunasan' => Carbon::now(),
                    'No_sbg' => $request->no_sbg,
                    'Pokok_pinjaman' => $request->pokok_pinjaman,
                    'Sewa_modal' => $request->sewa_modal / 100,
                    'Denda_pinjaman' => $request->denda_pinjaman,
                    'Total_pembayaran' => $request->total_pembayaran,
                    'Cara_bayar' => $request->cara_bayar,
                    'Jenis_pembayaran' => $request->jenis_pembayaran,
                    'User_input' => $request->user()->USER_NAME,
                    'Jenis_transaksi' => "PERPANJANGAN",
                    'Piutang' => $request->bunga + $request->pokok_pinjaman,
                    'Bunga' => $request->bunga,
                ]);
                $ppj->save();

                // Save BayarGadai
                $byrGd = new BayarGadai();
                $dateByr = DB::select(DB::raw("SELECT CAST(CAST(CAST(:date as date) as datetime) as int) + 693596 as numeric_date"), ['date' => Carbon::now()]);
                $dateJT = DB::select(DB::raw("SELECT cast(cast(cast(? as date) as datetime) as int)+693596 as numeric_date"), [$request->new_tgl_jtempo]);

                $byrGd->fill([
                    'No_bayar' => $noBayar,
                    'Tgl_byr' => $dateByr[0]->numeric_date,
                    'No_faktur' => $request->no_sbg,
                    'Jenis_trans' => "RAHN",
                    'Posko' => $request->user()->POSKO,
                    'Cabang' => $request->user()->CABANG,
                    'K_kons' => $request->no_kons,
                    'Tgl_due' => $dateJT[0]->numeric_date,
                    'Pokok' => $request->pokok_pinjaman,
                    'Bunga' => $request->bunga,
                    'Denda' => $request->denda_pinjaman ?? 0,
                    'Nilai_tebus' => $request->total_pembayaran,
                    'Hasil_diterima' => $request->total_pembayaran,
                    'TglTebus1' => $dateByr[0]->numeric_date,
                ]);
                $byrGd->save();

                // Check if kode_barang is an array
                // dd($request->all());
                if (is_array($request->barangJmn)) {
                    foreach ($request->barangJmn as $index => $item) {
                        $bj = new JaminanMotor();
                        $bj->No_sbg = $newSbg;
                        $bj->Kode_barang = $item['kode_barang'];
                        $bj->Merk = $item['merk'];
                        $bj->Tipe = $item['tipe'];
                        $bj->Tahun = $item['tahun'];
                        $bj->Nopol = $item['nopol'];
                        $bj->No_rangka = $item['no_rangka'];
                        $bj->No_mesin = $item['no_mesin'];
                        $bj->No_bpkb = $item['no_bpkb'];
                        $bj->Taksiran = $item['taksiran'];
                        $bj->Maks_pinjaman = $item['maks_pinjaman'];
                        $bj->Detail_barang = $item['detail_barang'];

                        // Handle the first image upload
                        if ($request->hasFile("foto_barang.$index")) {
                            $img = $request->file("foto_barang.$index");
                            $uniqueId = uniqid();
                            $sisi1 = "sisi-1";
                            $imgName = sprintf('%s-%s-%s.%s', $bj->No_sbg, $uniqueId, $sisi1, $img->getClientOriginalExtension());
                            $img->storeAs(sprintf('public/picture/BarangJaminan/Perpanjang/%s', $bj->No_sbg), $imgName);
                            $bj->Foto_barang = $imgName;
                        }

                        // Handle the second image upload
                        if ($request->hasFile("foto_barang2.$index")) {
                            $img2 = $request->file("foto_barang2.$index");
                            $sisi2 = "sisi-2";
                            $imgName2 = sprintf('%s-%s-%s.%s', $bj->No_sbg, $uniqueId, $sisi2, $img2->getClientOriginalExtension());
                            $img2->storeAs(sprintf('public/picture/BarangJaminan/Perpanjang/%s', $bj->No_sbg), $imgName2);
                            $bj->Foto_barang2 = $imgName2;
                        }

                        // Handle multiple additional images (lain_lain)
                        if ($request->hasFile("lain_lain.$index")) {
                            $seq = 1;
                            $imageNames = [];

                            // Ensure `lain_lain` is an array of files for the given index
                            $additionalImages = $request->file("lain_lain.$index");

                            if (is_array($additionalImages)) {
                                // Loop through each additional image file
                                foreach ($additionalImages as $img3) {
                                    $imgName3 = sprintf('%s-%s-%s-%s.%s', $bj->No_sbg, $bj->Kode_barang, $seq++, "Tambahan", $img3->getClientOriginalExtension());

                                    // Attempt to store the file
                                    $stored = $img3->storeAs(
                                        sprintf('public/picture/BarangJaminan/Perpanjang/%s/FotoTambahan/%s', $bj->No_sbg, $bj->Kode_barang),
                                        $imgName3
                                    );

                                    // Check if the file was stored successfully
                                    if ($stored) {
                                        $imageNames[] = $imgName3; // Add each stored image name to the array
                                    } else {
                                        Log::error('Failed to store Lain_lain image', ['image' => $imgName3]);
                                    }
                                }

                                // Store all additional image names as a JSON array in the `Lain_lain` field
                                $bj->Lain_lain = json_encode($imageNames);
                            } else {
                                Log::error('Lain_lain is not an array of files', ['index' => $index]);
                            }
                        }


                        // Save the record
                        $bj->save();
                    }
                } else {
                    return response()->json(['error' => 'Invalid input data.'], 422);
                }

                $gd = PerGadaiOffline::where('No_sbg', $request->no_sbg)->first();

                if ($gd) {
                    $gd->Status = 0;
                    $gd->save();
                } else {
                    throw new Exception('No_sbg tidak ditemukan untuk memperbarui status');
                }
            });

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'pesan' => $e->getMessage()
            ], 500);
        }

        // dd($request->all());
    }

    // public function storeBayar(Request $request)
    // {
    //     $alert = [
    //         'by_lain' => 'Input Menggunakan Angka'
    //     ];

    //     $request->validate([
    //         'no_sbg' => 'required|string',
    //         'denda_pinjaman' => 'required|numeric',
    //         'by_lain' => 'required|numeric',
    //     ], $alert);

    //     $ppj = new Bayar();
    //     $ppj->No_bayaran = $this->getNoBayar($request);
    //     $ppj->No_sbg = $request->no_sbg;
    //     $ppj->Denda_pinjaman = $request->denda_pinjaman;
    //     $ppj->By_lain = $request->by_lain;
    //     $ppj->save();
    // }
}
