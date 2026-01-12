<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\BarangJaminan;
use App\Models\Golongan;
use App\Models\JaminanMobil;
use App\Models\JaminanMotor;
use App\Models\Kurs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangJaminanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getBarangJaminan(Request $req)
    {
        $nosbg = $req->no_sbg;
        $emas = BarangJaminan::where('No_sbg', $nosbg)->get();
        return $emas;
    }

    public function getJaminanMotor(Request $req)
    {
        $nosbg = $req->no_sbg;
        $motor = JaminanMotor::where('No_sbg', $nosbg)->get();
        return $motor;
    }

    public function getJaminanMobil(Request $req)
    {
        $nosbg = $req->no_sbg;
        $mobil = JaminanMobil::where('No_sbg', $nosbg)->get();
        return $mobil;
    }

    public function index()
    {
        //
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
            'jenis.required' => 'Jenis harus diisi !',
            'kadar.required' => 'Kadar harus diisi !',
            'berat_kotor.required' => 'Berat Kotor harus diisi !',
            'berat_kotor.numeric' => 'Input menggunakan angka !',
            'berat_bersih.required' => 'Berat Bersih harus diisi !',
            'berat_bersih.numeric' => 'Input menggunakan angka !',
            'foto_barang.required' => 'Foto Barang Jaminan Sisi Pertama harus diupload !',
            'foto_barang2.required' => 'Foto Barang Jaminan Sisi Kedua harus diupload !',
            'foto_barang.mimes' => 'Foto Barang Jaminan Sisi Pertama harus berupa JPG, JPEG, dan PNG !',
            'foto_barang2.mimes' => 'Foto Barang Jaminan Sisi Kedua harus berupa JPG, JPEG, dan PNG !',
            'foto_barang.max' => 'Foto Barang Jaminan Sisi Pertama maksimal berukuran 2MB !',
            'foto_barang2.max' => 'Foto Barang Jaminan Sisi Kedua maksimal berukuran 2MB !',
        ];

        $request->validate([
            'jenis' => 'required|string',
            'kadar' => 'required|string',
            'berat_kotor' => 'required|numeric',
            'berat_bersih' => 'required|numeric',
            'foto_barang' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Pertama";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'foto_barang2' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Kedua";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'detail_barang' => 'required|string'
        ], $alert);

        do {
            $randNum = str_pad(random_int(1, 10000000), 7, '0', STR_PAD_LEFT);
        } while (BarangJaminan::where('Kode_barang', $randNum)->exists());

        $kurs = Kurs::where('active', 1)->first();

        $bj = new BarangJaminan();
        $bj->No_sbg = $request->no_sbg;
        $bj->Kode_barang = $randNum;
        $maxSeq = BarangJaminan::max('Seq');
        $bj->Seq = $maxSeq + 1;
        if ($request->jenis == "Lain lain") {
            $bj->Jenis = $request->jenis_lain;
        } else {
            $bj->Jenis = $request->jenis;
        }
        $bj->Kadar = $request->kadar;
        $bj->Berat_kotor = $request->berat_kotor;
        $bj->Berat_bersih = $request->berat_bersih;
        $taksiran = $request->berat_bersih * ($request->kadar / 24 * intval($kurs->Kurs));
        $golonganChoice = Golongan::select('PERSEN_PATOK_TAKSIRAN')
            ->where('MIN_PINJAMAN', '<=', $taksiran)
            ->where('MAX_PINJAMAN', '>=', $taksiran)
            ->first();
        $roundedPatokTaksiran = round($golonganChoice->PERSEN_PATOK_TAKSIRAN, 2);
        $bj->Taksiran = intval($taksiran);
        $bj->Maks_pinjaman = intval($taksiran * $roundedPatokTaksiran);
        $uniqueId = uniqid();
        $sisi1 = "sisi-1";
        $sisi2 = "sisi-2";
        $img = $request->file('foto_barang');
        $imgName = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi1, $img->getClientOriginalExtension());
        $img->storeAs(sprintf('public/picture/BarangJaminan/%s', $request->no_sbg), $imgName);
        $bj->Foto_barang = $imgName;
        $img2 = $request->file('foto_barang2');
        $imgName2 = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi2, $img2->getClientOriginalExtension());
        $img2->storeAs(sprintf('public/picture/BarangJaminan/%s', $request->no_sbg), $imgName2);
        $bj->Foto_barang2 = $imgName2;
        $bj->Detail_barang = $request->detail_barang;
        $bj->Status_barang = 1;
        $bj->save();
        return back();
    }

    public function storeMotor(Request $request)
    {
        $alert = [
            'merk.required' => 'Merk harus diisi !',
            'tipe.required' => 'Tipe harus diisi !',
            'nopol.required' => 'Nopol harus diisi !',
            'no_rangka.required' => 'No Rangka harus diisi !',
            'no_mesin.required' => 'No Mesin harus diisi !',
            'no_bpkb.required' => 'No Bpkb harus diisi !',
            'foto_barang.required' => 'Foto Barang Jaminan Sisi Pertama harus diupload !',
            'foto_barang2.required' => 'Foto Barang Jaminan Sisi Kedua harus diupload !',
            'foto_barang.mimes' => 'Foto Barang Jaminan Sisi Pertama harus berupa JPG, JPEG, dan PNG !',
            'foto_barang2.mimes' => 'Foto Barang Jaminan Sisi Kedua harus berupa JPG, JPEG, dan PNG !',
            'foto_barang.max' => 'Foto Barang Jaminan Sisi Pertama maksimal berukuran 2MB !',
            'foto_barang2.max' => 'Foto Barang Jaminan Sisi Kedua maksimal berukuran 2MB !',
        ];

        $request->validate([
            'merk' => 'required|string',
            'tipe' => 'required|string',
            'nopol' => 'required|string',
            'no_rangka' => 'required|string',
            'no_mesin' => 'required|string',
            'no_bpkb' => 'required|string',
            'foto_barang' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Pertama";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'foto_barang2' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Kedua";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'detail_barang' => 'required|string'
        ], $alert);

        do {
            $randNum = str_pad(random_int(1, 10000000), 7, '0', STR_PAD_LEFT);
        } while (JaminanMotor::where('Kode_barang', $randNum)->exists());

        $bj = new JaminanMotor();
        $bj->No_sbg = $request->no_sbg;
        $bj->Kode_barang = $randNum;
        $bj->Merk = $request->merk;
        $bj->Tipe = $request->tipe;
        $bj->Tahun = $request->tahun;
        $bj->Nopol = $request->nopol;
        $bj->No_rangka = $request->no_rangka;
        $bj->No_mesin = $request->no_mesin;
        $bj->No_bpkb = $request->no_bpkb;
        $bj->Taksiran = $request->harga * 0.85;
        $bj->Maks_pinjaman = $bj->Taksiran * 0.90;
        $uniqueId = uniqid();
        $sisi1 = "sisi-1";
        $sisi2 = "sisi-2";
        $img = $request->file('foto_barang');
        $imgName = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi1, $img->getClientOriginalExtension());
        $img->storeAs(sprintf('public/picture/JaminanMotor/%s', $request->no_sbg), $imgName);
        $bj->Foto_barang = $imgName;
        $img2 = $request->file('foto_barang2');
        $imgName2 = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi2, $img2->getClientOriginalExtension());
        $img2->storeAs(sprintf('public/picture/JaminanMotor/%s', $request->no_sbg), $imgName2);
        $bj->Foto_barang2 = $imgName2;
        $bj->Detail_barang = $request->detail_barang;
        $bj->save();
        return back();
    }

    public function storeMobil(Request $request)
    {
        $alert = [
            'merk.required' => 'Merk harus diisi !',
            'tipe.required' => 'Tipe harus diisi !',
            'nopol.required' => 'Nopol harus diisi !',
            'no_rangka.required' => 'No Rangka harus diisi !',
            'no_mesin.required' => 'No Mesin harus diisi !',
            'no_bpkb.required' => 'No Bpkb harus diisi !',
            'foto_barang.required' => 'Foto Barang Jaminan Sisi Pertama harus diupload !',
            'foto_barang2.required' => 'Foto Barang Jaminan Sisi Kedua harus diupload !',
            'foto_barang.mimes' => 'Foto Barang Jaminan Sisi Pertama harus berupa JPG, JPEG, dan PNG !',
            'foto_barang2.mimes' => 'Foto Barang Jaminan Sisi Kedua harus berupa JPG, JPEG, dan PNG !',
            'foto_barang.max' => 'Foto Barang Jaminan Sisi Pertama maksimal berukuran 2MB !',
            'foto_barang2.max' => 'Foto Barang Jaminan Sisi Kedua maksimal berukuran 2MB !',
        ];

        $request->validate([
            'merk' => 'required|string',
            'tipe' => 'required|string',
            'nopol' => 'required|string',
            'no_rangka' => 'required|string',
            'no_mesin' => 'required|string',
            'no_bpkb' => 'required|string',
            'foto_barang' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Pertama";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'foto_barang2' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $attribute = "Foto Barang Jaminan Sisi Kedua";
                    $mimeType = $value->getMimeType();
                    $originalExtension = strtolower($value->getClientOriginalExtension());

                    if ($mimeType === 'image/jpeg' && $originalExtension === 'jfif') {
                        $fail($attribute . ' harus berupa JPG, JPEG, dan PNG.');
                    }
                }
            ],
            'detail_barang' => 'required|string'
        ], $alert);

        do {
            $randNum = str_pad(random_int(1, 10000000), 7, '0', STR_PAD_LEFT);
        } while (JaminanMobil::where('Kode_barang', $randNum)->exists());

        $bj = new JaminanMobil();
        $bj->No_sbg = $request->no_sbg;
        $bj->Kode_barang = $randNum;
        $bj->Merk = $request->merk;
        $bj->Tipe = $request->tipe;
        $bj->Tahun = $request->tahun;
        $bj->Nopol = $request->nopol;
        $bj->No_rangka = $request->no_rangka;
        $bj->No_mesin = $request->no_mesin;
        $bj->No_bpkb = $request->no_bpkb;
        $bj->Taksiran = $request->harga * 0.85;
        $bj->Maks_pinjaman = $bj->Taksiran * 0.90;
        $uniqueId = uniqid();
        $sisi1 = "sisi-1";
        $sisi2 = "sisi-2";
        $img = $request->file('foto_barang');
        $imgName = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi1, $img->getClientOriginalExtension());
        $img->storeAs(sprintf('public/picture/JaminanMobil/%s', $request->no_sbg), $imgName);
        $bj->Foto_barang = $imgName;
        $img2 = $request->file('foto_barang2');
        $imgName2 = sprintf('%s-%s-%s.%s', $request->no_sbg, $uniqueId, $sisi2, $img2->getClientOriginalExtension());
        $img2->storeAs(sprintf('public/picture/JaminanMobil/%s', $request->no_sbg), $imgName2);
        $bj->Foto_barang2 = $imgName2;
        $bj->Detail_barang = $request->detail_barang;
        $bj->save();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BarangJaminan  $barangJaminan
     * @return \Illuminate\Http\Response
     */
    public function show(BarangJaminan $barangJaminan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BarangJaminan  $barangJaminan
     * @return \Illuminate\Http\Response
     */
    public function edit(BarangJaminan $barangJaminan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BarangJaminan  $barangJaminan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BarangJaminan $barangJaminan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BarangJaminan  $barangJaminan
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $nosbg = $request->no_sbg;

        // Adjust this if no_sbg is supposed to be an array
        $bj = BarangJaminan::where('No_sbg', $nosbg)->get();

        if (!$bj->isEmpty()) {
            $directoryPath = sprintf('picture/BarangJaminan/%s', $nosbg);
            foreach ($bj as $b) {
                if ($b->Foto_barang) {
                    // Correct file path
                    $path = sprintf('picture/BarangJaminan/%s/%s', $nosbg, $b->Foto_barang);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                if ($b->Foto_barang2) {
                    // Correct file path
                    $path = sprintf('picture/BarangJaminan/%s/%s', $nosbg, $b->Foto_barang2);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                $b->delete();
            }

            if (Storage::disk('public')->exists($directoryPath) && count(Storage::disk('public')->files($directoryPath)) === 0) {
                Storage::disk('public')->deleteDirectory($directoryPath);
            }

            return response()->json(['message' => 'Records and associated files deleted successfully.']);
        } else {
            return response()->json(['message' => 'No records found for the provided no_sbg.']);
        }
    }

    public function deleteMotor(Request $request)
    {
        $nosbg = $request->no_sbg;

        // Adjust this if no_sbg is supposed to be an array
        $bj = JaminanMotor::where('No_sbg', $nosbg)->get();

        if (!$bj->isEmpty()) {
            $directoryPath = sprintf('picture/JaminanMotor/%s', $nosbg);
            foreach ($bj as $b) {
                if ($b->Foto_barang) {
                    // Correct file path
                    $path = sprintf('picture/JaminanMotor/%s/%s', $nosbg, $b->Foto_barang);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                if ($b->Foto_barang2) {
                    // Correct file path
                    $path = sprintf('picture/JaminanMotor/%s/%s', $nosbg, $b->Foto_barang2);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                $b->delete();
            }

            if (Storage::disk('public')->exists($directoryPath) && count(Storage::disk('public')->files($directoryPath)) === 0) {
                Storage::disk('public')->deleteDirectory($directoryPath);
            }

            return response()->json(['message' => 'Records and associated files deleted successfully.']);
        } else {
            return response()->json(['message' => 'No records found for the provided no_sbg.']);
        }
    }

    public function deleteMobil(Request $request)
    {
        $nosbg = $request->no_sbg;

        // Adjust this if no_sbg is supposed to be an array
        $bj = JaminanMobil::where('No_sbg', $nosbg)->get();

        if (!$bj->isEmpty()) {
            $directoryPath = sprintf('picture/JaminanMobil/%s', $nosbg);
            foreach ($bj as $b) {
                if ($b->Foto_barang) {
                    // Correct file path
                    $path = sprintf('picture/JaminanMobil/%s/%s', $nosbg, $b->Foto_barang);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }

                if ($b->Foto_barang2) {
                    // Correct file path
                    $path = sprintf('picture/JaminanMobil/%s/%s', $nosbg, $b->Foto_barang2);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                $b->delete();
            }

            if (Storage::disk('public')->exists($directoryPath) && count(Storage::disk('public')->files($directoryPath)) === 0) {
                Storage::disk('public')->deleteDirectory($directoryPath);
            }

            return response()->json(['message' => 'Records and associated files deleted successfully.']);
        } else {
            return response()->json(['message' => 'No records found for the provided no_sbg.']);
        }
    }
}