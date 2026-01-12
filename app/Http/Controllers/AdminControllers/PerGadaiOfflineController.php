<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\BarangJaminan;
use App\Models\Cabang;
use App\Models\Golongan;
use App\Models\JaminanMobil;
use App\Models\JaminanMotor;
use App\Models\MasterCode;
use App\Models\Nasabah;
use App\Models\Penaksir;
use App\Models\PerGadaiOffline;
use App\Models\SewaModal;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PerGadaiOfflineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/Transactions/PerGadaiOffline');
    }

    public function piutangIndex()
    {
        return Inertia::render('Admin/KartuPiutang/Index');
    }

    public function getNasabahId(Request $req)
    {
        $nasabahId = $req->nasabahIdentitas;
        $nas = Nasabah::where('No_ktp', $nasabahId)->get();
        return $nas;
    }

    public function getMasterCode()
    {
        $mc = MasterCode::all();
        return $mc;
    }

    public function getPenaksirByPosko()
    {
        $user = Auth::user();
        $pnk = Penaksir::where('posko', $user->POSKO)->get();
        return $pnk;
    }

    public function getCabang()
    {
        $cbg = Cabang::all();
        return $cbg;
    }

    public function getGadaiEmas()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangEmas', 'bayar'])->where('Produk_gadai', 'EMAS')->get();
        return $gd;
    }

    public function getGadaiMotor()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangMotor', 'bayar'])->where('Produk_gadai', 'MOTOR')->get();
        return $gd;
    }

    public function getGadaiMobil()
    {
        $gd = PerGadaiOffline::with(['nasabah', 'barangMobil', 'bayar'])->where('Produk_gadai', 'MOBIL')->get();
        return $gd;
    }

    public function getGolongan()
    {
        $gl = Golongan::all();
        return $gl;
    }

    public function getSewaModal()
    {
        $sm = SewaModal::all();
        return $sm;
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

    public function getNoSbg(Request $request)
    {
        $year = Carbon::now()->format('y'); // Only get the last two digits of the current year
        $month = Carbon::now()->format('m');
        $posko = substr($request->user()->POSKO, -2); // Example posko value
        // Retrieve the highest existing sequence number for the current month and year from the secondary database
        $latestEntry = DB::connection('second_sqlsrv')->table('tb_transaksi')
            ->where(DB::raw('SUBSTRING(No_sbg, 1, 2)'), '=', $year)
            ->where(DB::raw('SUBSTRING(No_sbg, 3, 2)'), '=', $month)
            ->where(DB::raw('SUBSTRING(No_sbg, 5, 2)'), '=', $posko)
            ->orderBy(DB::raw('CAST(SUBSTRING(No_sbg, 7, 4) AS INT)'), 'desc')
            ->first();

        if ($latestEntry) {
            // Extract the sequence part from the No_sbg value
            $latestSequence = (int)substr($latestEntry->No_sbg, 6, 4);
            $nextSequence = $latestSequence + 1;
        } else {
            $nextSequence = 1;
        }

        // Ensure the sequence number is 4 digits
        $nosbg = $year . $month . $posko . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        return $nosbg;
    }

    public function store(Request $request)
    {
        $alert = [
            'pokok_pinjaman' => 'Input menggunakan angka !',
            'foto_identitas.required' => 'Foto Identitas harus diupload !',
            'foto_identitas.mimes' => 'Foto Identitas harus berupa JPG, JPEG, dan PNG !',
            'foto_identitas.max' => 'Foto Identitas maksimal berukuran 2MB !',
        ];

        $request->validate([
            'no_sbg' => 'required|string',
            'tgl_transaksi' => 'required|date',
            'no_kons' => 'required|string',
            'asal_barang' => 'required|string',
            'tujuan_transaksi' => 'required|string',
            'fungsi_transaksi' => 'required|string',
            'instrumen_pembayaran' => 'required|string',
            'pengembalian_uang_lebih' => 'required|string',
            'penaksir' => 'required|string',
            'type_transaksi' => 'required|string',
            'sewa_modal' => 'required|numeric',
            'tgl_jtempo' => 'required|date',
            'by_admin' => 'required|numeric',
            'tenor' => 'required|numeric',
            'total_taksiran' => 'required|numeric',
            'maks_pinjaman' => 'required|numeric',
            'pokok_pinjaman' => 'required|numeric',
            'cara_pencairan' => 'required|string',
            'no_rek_nasabah' => 'required|string',
            'foto_identitas' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Identitas";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
        ], $alert);


        $tr = new PerGadaiOffline();
        $tr->No_sbg = $request->no_sbg;
        $tr->Posko = $request->user()->POSKO;
        $tr->Cabang = $request->user()->CABANG;
        $tr->Tgl_transaksi = $request->tgl_transaksi;
        $tr->No_kons = $request->no_kons;
        $tr->Penaksir = $request->penaksir;
        $tr->Type_transaksi = $request->type_transaksi;
        $tr->Sewa_modal = $request->sewa_modal / 100;
        $tr->Tgl_jtempo = $request->tgl_jtempo;
        $tr->By_admin = intval($request->by_admin);
        $tr->Tenor = $request->tenor;
        $tr->Golongan = $request->golongan;
        $tr->Total_taksiran = $request->total_taksiran;
        $tr->Pokok_pinjaman = $request->produk_gadai === 'MOBIL' ? $request->pokok_pinjaman - $request->bunga : $request->pokok_pinjaman;
        $tr->Maks_pinjaman = $request->maks_pinjaman;
        $tr->User_input = $request->user()->USER_NAME;
        $tr->Status = 1;
        $tr->Cara_pencairan = $request->cara_pencairan;
        $tr->No_rek_nasabah = $request->no_rek_nasabah;
        $tr->Referensi_nosbg = $request->no_sbg;
        $img = $request->file('foto_identitas');
        $imgName = sprintf('%s-%s.%s', $request->no_sbg, "FotoKTP", $img->getClientOriginalExtension());
        $img->storeAs('public/picture/TransaksiGadai/KtpPhoto/', $imgName);
        $tr->Foto_identitas = $imgName;
        $tr->Asal_barang = $request->asal_barang;
        $tr->Tujuan_transaksi = $request->tujuan_transaksi;
        $tr->Instrumen_pembayaran = $request->instrumen_pembayaran;
        $tr->Pengembalian_uang_lebih = $request->pengembalian_uang_lebih;
        $tr->Fungsi_transaksi = $request->fungsi_transaksi;
        $tr->Bunga = $request->bunga;
        $tr->Produk_gadai = $request->produk_gadai;
        $tr->Keabsahan = $request->produk_gadai === 'MOBIL' ? 200000 : null;
        $tr->save();
        return back();
    }

    public function uploadTerimaUang(Request $request)
    {
        $alert = [
            'terima_uang.required' => 'Foto Kons Terima Uang harus diupload !',
            'terima_uang.mimes' => 'Foto Kons Terima Uang harus berupa JPG, JPEG, dan PNG !',
            'terima_uang.max' => 'Foto Kons Terima Uang maksimal berukuran 2MB !',
        ];

        $request->validate([
            'terima_uang' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Kons Terima Uang";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
        ], $alert);

        $tr = PerGadaiOffline::where('No_sbg', $request->no_sbg)->first();
        $img = $request->file('terima_uang');
        $imgName = $img->getClientOriginalName();
        $imgName = sprintf('%s-%s.%s', $request->no_sbg, "TerimaUang", $img->getClientOriginalExtension());
        $img->storeAs('public/picture/TransaksiGadai/TerimaUangPhoto/', $imgName);
        $tr->Terima_uang = $imgName;
        $tr->update();
    }

    public function uploadBuktiLain(Request $request)
    {
        $request->validate([
            'bukti_lain' => 'required|array', // Ensure `lain_lain` is an array
            'bukti_lain.*' => [
                'required',
                'max:2048', // Max file size is 2MB
                function ($attribute, $value, $fail) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $originalExtension = strtolower($value->getClientOriginalExtension());
                        if ($originalExtension === 'jfif') {
                            $fail('Foto Bukti Lain harus berupa JPG, JPEG, dan PNG, tidak bisa JFIF !');
                        }
                    } else {
                        $fail('Invalid file upload.');
                    }
                }
            ],
        ]);

        $no_sbg = $request->no_sbg;
        $imageNames = [];
        $seq = 1;

        if ($request->hasFile('bukti_lain')) {
            foreach ($request->file('bukti_lain') as $image) {
                $imageName = sprintf('%s-%s-%s.%s', $no_sbg, $seq++, "BuktiLain", $image->getClientOriginalExtension());
                $image->storeAs(sprintf('public/picture/TransaksiGadai/BuktiLainnya/%s/', $request->no_sbg), $imageName);
                $imageNames[] = $imageName;
            }
        }

        PerGadaiOffline::where('No_sbg', $no_sbg)->update([
            'Bukti_lain' => json_encode($imageNames)
        ]);
        return response()->json(['success' => 'Images uploaded successfully.']);
    }

    public function uploadLain(Request $request)
    {
        // Validate that files are an array of images
        $request->validate([
            'lain_lain' => 'required|array', // Ensure `lain_lain` is an array
            'lain_lain.*' => [
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
        ]);

        $no_sbg = $request->no_sbg;
        $kode_brg = $request->kode_barang;
        $produk_gadai = $request->produk_gadai;
        $imageNames = [];
        $seq = 1;

        if ($produk_gadai === 'EMAS') {
            if ($request->hasFile('lain_lain')) {
                foreach ($request->file('lain_lain') as $image) {
                    $imageName = sprintf('%s-%s-%s-%s.%s', $no_sbg, $kode_brg, $seq++, "Tambahan", $image->getClientOriginalExtension());
                    $image->storeAs(sprintf('public/picture/BarangJaminan/%s/FotoTambahan/%s/', $request->no_sbg, $request->kode_barang), $imageName);
                    $imageNames[] = $imageName;
                }
            }

            BarangJaminan::where('Kode_barang', $kode_brg)->update([
                'Lain_lain' => json_encode($imageNames)
            ]);
        } else if ($produk_gadai === 'MOTOR') {
            if ($request->hasFile('lain_lain')) {
                foreach ($request->file('lain_lain') as $image) {
                    $imageName = sprintf('%s-%s-%s-%s.%s', $no_sbg, $kode_brg, $seq++, "Tambahan", $image->getClientOriginalExtension());
                    $image->storeAs(sprintf('public/picture/JaminanMotor/%s/FotoTambahan/%s/', $request->no_sbg, $request->kode_barang), $imageName);
                    $imageNames[] = $imageName;
                }
            }

            JaminanMotor::where('Kode_barang', $kode_brg)->update([
                'Lain_lain' => json_encode($imageNames)
            ]);
        } else if ($produk_gadai === 'MOBIL') {
            if ($request->hasFile('lain_lain')) {
                foreach ($request->file('lain_lain') as $image) {
                    $imageName = sprintf('%s-%s-%s-%s.%s', $no_sbg, $kode_brg, $seq++, "Tambahan", $image->getClientOriginalExtension());
                    $image->storeAs(sprintf('public/picture/JaminanMobil/%s/FotoTambahan/%s/', $request->no_sbg, $request->kode_barang), $imageName);
                    $imageNames[] = $imageName;
                }
            }

            JaminanMobil::where('Kode_barang', $kode_brg)->update([
                'Lain_lain' => json_encode($imageNames)
            ]);
        }

        return response()->json(['success' => 'Images uploaded successfully.']);
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PerGadaiOffline  $perGadaiOffline
     * @return \Illuminate\Http\Response
     */
    public function show(PerGadaiOffline $perGadaiOffline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PerGadaiOffline  $perGadaiOffline
     * @return \Illuminate\Http\Response
     */
    public function edit(PerGadaiOffline $perGadaiOffline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerGadaiOffline  $perGadaiOffline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PerGadaiOffline $perGadaiOffline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PerGadaiOffline  $perGadaiOffline
     * @return \Illuminate\Http\Response
     */
    public function destroy(PerGadaiOffline $perGadaiOffline)
    {
        //
    }
}