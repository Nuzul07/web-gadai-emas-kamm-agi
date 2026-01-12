<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Bayar;
use App\Models\Golongan;
use App\Models\Lelang;
use App\Models\PerGadaiOffline;
use App\Models\Posko;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PDFController extends Controller
{
    public function generatePDF(Request $req)
    {
        $nosbg = $req->no_sbg;
        $produk_gadai = $req->produk_gadai;

        $data = null;

        if ($produk_gadai === 'EMAS') {
            $data = PerGadaiOffline::with(['nasabah', 'barangEmas', 'golongan'])
                ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOTOR') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMotor'])
                ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOBIL') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMobil'])
                ->where('No_sbg', $nosbg)
                ->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $cabang = Posko::select('ket_cabang')->where('kd_posko', $req->user()->POSKO)->first();
        // dd($data);

        $data->Tgl_transaksi = Carbon::parse($data->Tgl_transaksi)->format('d-m-Y');
        $data->Tgl_jtempo = Carbon::parse($data->Tgl_jtempo)->format('d-m-Y');

        $pdf = Pdf::loadView('pdf_template', ['data' => $data, 'cabang' => $cabang]);
        return $pdf->stream('SBG - ' . $req->no_sbg);
    }

    public function generatePDFDwi(Request $req)
    {
        $nosbg = $req->no_sbg;
        $produk_gadai = $req->produk_gadai;

        $data = null;

        if ($produk_gadai === 'EMAS') {
            $data = PerGadaiOffline::with(['nasabah', 'barangEmas', 'golongan'])
                ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOTOR') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMotor'])
                ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOBIL') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMobil'])
                ->where('No_sbg', $nosbg)
                ->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        $cabang = Posko::select('ket_cabang')->where('kd_posko', $req->user()->POSKO)->first();
        // dd($data);

        $data->Tgl_transaksi = Carbon::parse($data->Tgl_transaksi)->format('d-m-Y');
        $data->Tgl_jtempo = Carbon::parse($data->Tgl_jtempo)->format('d-m-Y');

        $pdf = Pdf::loadView('pdf_dwilipat', ['data' => $data, 'cabang' => $cabang]);
        return $pdf->stream('SBG - ' . $req->no_sbg);
    }

    public function generatePDFLunas(Request $req)
    {
        $nosbg = $req->no_sbg;
        $produk_gadai = $req->produk_gadai;

        $data = null;

        if ($produk_gadai === 'EMAS') {
            $data = PerGadaiOffline::with(['nasabah', 'barangEmas', 'bayar', 'golongan'])
            ->where('No_sbg', $nosbg)
            ->first();
        } else if ($produk_gadai === 'MOTOR') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMotor', 'bayar'])
            ->where('No_sbg', $nosbg)
            ->first();
        } else if ($produk_gadai === 'MOBIL') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMobil', 'bayar'])
            ->where('No_sbg', $nosbg)
            ->first();
        }
        
        $cabang = Posko::select('ket_cabang')->where('kd_posko', $req->user()->POSKO)->first();
        // dd($data);

        $data->Tgl_transaksi = Carbon::parse($data->Tgl_transaksi)->format('d-m-Y');
        $data->Tgl_jtempo = Carbon::parse($data->Tgl_jtempo)->format('d-m-Y');
        $data->bayar[0]->Tgl_pelunasan = Carbon::parse($data->Tgl_pelunasan)->format('d-m-Y');

        $pdf = Pdf::loadView('pdf_pelunasan', ['data' => $data, 'cabang' => $cabang]);
        return $pdf->stream('SBG - ' . $req->no_sbg);
    }

    public function generatePDFPerpanjang(Request $req)
    {
        $nosbg = session('no_sbg_pjg');
        $oldsbg = $req->no_sbg;
        $produk_gadai = $req->produk_gadai;

        $data = null;

        if ($produk_gadai === 'EMAS') {
            $data = PerGadaiOffline::with(['nasabah', 'barangEmas', 'golongan'])
            ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOTOR') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMotor'])
            ->where('No_sbg', $nosbg)
                ->first();
        } else if ($produk_gadai === 'MOBIL') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMobil'])
            ->where('No_sbg', $nosbg)
                ->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Data not found'], 404);
        }
        
        $bayar = Bayar::where('No_sbg', $oldsbg)->first();
        $cabang = Posko::select('ket_cabang')->where('kd_posko', $req->user()->POSKO)->first();
        // dd($bayar);

        $data->Tgl_transaksi = Carbon::parse($data->Tgl_transaksi)->format('d-m-Y');
        $data->Tgl_jtempo = Carbon::parse($data->Tgl_jtempo)->format('d-m-Y');
        $bayar->Tgl_pelunasan = Carbon::parse($data->Tgl_pelunasan)->format('d-m-Y');

        $pdf = Pdf::loadView('pdf_perpanjangan', ['data' => $data, 'bayar' => $bayar, 'cabang' => $cabang]);
        return $pdf->stream('SBG - ' . $req->no_sbg);
    }

    public function generatePDFMohonLelang(Request $req)
    {
        $noLelang = $req->nomor_lelang;
        $data1 = Lelang::with(['perGadaiOffline', 'nasabah'])
            ->where('NOMOR_LELANG', $noLelang)
            ->first();
        $data2 = Lelang::with(['perGadaiOffline', 'nasabah', 'barang'])
            ->where('NOMOR_LELANG', $noLelang)
            ->get();
        $posko = Posko::select('ket_posko')->where('kd_posko', $req->user()->POSKO)->first();

        $totalTaksiran = $data2->sum('barang.Taksiran');
        $totalDenda = $data2->sum('DENDA');
        $totalNilaiBuku = $data2->sum('NILAI_BUKU');
        $totalNilaiLelang = $data2->sum('NILAI_LELANG');
        // dd($data2);

        $pdf = Pdf::loadView('pdf_mohonLelang', [
            'data1' => $data1,
            'data2' => $data2,
            'totalTaksiran' => $totalTaksiran,
            'totalDenda' => $totalDenda,
            'totalNilaiBuku' => $totalNilaiBuku,
            'totalNilaiLelang' => $totalNilaiLelang,
            'posko' => $posko
        ]);
        return $pdf->stream('Permohonan Pembelian Barang Lelang - ' . $req->no_sbg);
    }

    public function generatePDFKwitansi(Request $req)
    {
        $noLelang = $req->nomor_lelang;
        $data1 = Lelang::where('NOMOR_LELANG', $noLelang)
            ->first();

        $data2 = Lelang::where('NOMOR_LELANG', $noLelang)
            ->whereNotNull('TXT_1')
            ->get();
        $posko = Posko::select('ket_posko')->where('kd_posko', $req->user()->POSKO)->first();

        $nama_anggota = $req->user()->USER_NAME;
        $totalNilaiLelang = $data2->sum('NILAI_LELANG');
        // dd($bayar);

        // $data->Tgl_transaksi = Carbon::parse($data->Tgl_transaksi)->format('d-m-Y');
        // $data->Tgl_jtempo = Carbon::parse($data->Tgl_jtempo)->format('d-m-Y');
        // $bayar->Tgl_pelunasan = Carbon::parse($data->Tgl_pelunasan)->format('d-m-Y');

        $pdf = Pdf::loadView('pdf_kwitansiLelang', [
            'data1' => $data1,
            'data2' => $data2,
            'nama_anggota' => $nama_anggota,
            'totalNilaiLelang' => $totalNilaiLelang,
            'posko' => $posko
        ]);
        return $pdf->stream('NOMOR_LELANG' . $noLelang);
    }

    public function generatePDFPiutang(Request $req)
    {
        $nosbg = $req->no_sbg;
        $produk_gadai = $req->produk_gadai;

        $data = null;
        
        if ($produk_gadai === 'EMAS') {
            $data = PerGadaiOffline::with(['nasabah', 'barangEmas', 'bayar'])
            ->where('No_sbg', $nosbg)
            ->first();
        } else if ($produk_gadai === 'MOTOR') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMotor', 'bayar'])
            ->where('No_sbg', $nosbg)
            ->first();
        } else if ($produk_gadai === 'MOBIL') {
            $data = PerGadaiOffline::with(['nasabah', 'barangMobil', 'bayar'])
            ->where('No_sbg', $nosbg)
            ->first();
        }

        $golongan = Golongan::all();

        // Parse dates as Carbon instances
        $tglTransaksi = Carbon::parse($data->Tgl_transaksi);
        $tglJtempo = Carbon::parse($data->Tgl_jtempo);

        $sewaModalRate = 0;
        $piutang = 0;
        $potonganBunga = 0;
        $denda = 0;
        $diffDaysBunga = 0;

        // diffdays late
        $simulPelunasan = Carbon::now();
        $diffDaysBunga = $simulPelunasan->diffInDays($tglTransaksi) + 1;
        if ($produk_gadai === 'EMAS') {
            $sewaModalGolongan = collect($golongan)->firstWhere('GOLONGAN', $data->Golongan);
            // dd($sewaModalGolongan);
            // Calculate `sewa_modal` and `sewaModalAmount` based on the `diffDays`
            if ($diffDaysBunga <= 15) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL_15HARI;
            } elseif ($diffDaysBunga > 15 && $diffDaysBunga <= 30) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL16_30HARI;
            } elseif ($diffDaysBunga > 30 && $diffDaysBunga <= 45) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL31_45HARI;
            } elseif ($diffDaysBunga > 45 && $diffDaysBunga <= 60) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL46_60HARI;
            } elseif ($diffDaysBunga > 60 && $diffDaysBunga <= 75) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL61_75HARI;
            } elseif ($diffDaysBunga > 75 && $diffDaysBunga <= 90) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL76_90HARI;
            } elseif ($diffDaysBunga > 90 && $diffDaysBunga <= 105) {
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL91_105HARI;
            } else { // $diffDays > 105
                $sewaModalRate = $sewaModalGolongan->SEWA_MODAL_105_ATAS_HARI;
            }
        }

        if (count($data->bayar) > 0) {
            $tglPelunasan = Carbon::parse($data->bayar[0]->Tgl_pelunasan);
            $denda = $data->bayar[0]->Denda_pinjaman;
            if ($data->Status == 1) {
                $piutang = $data->bayar[0]->Piutang;
            }
            $potonganBunga = $data->Bunga - $data->bayar[0]->Bunga;
            $data->bayar[0]->Tgl_pelunasan = $tglPelunasan->format('d-m-Y');
            $diffDaysBunga = $tglPelunasan->diffInDays($tglTransaksi) + 2;
        } else {
            // Calculate the `sewaModalAmount` and `sewa_modal`
            $pokokPinjaman = (int) $data->Pokok_pinjaman; // assuming you have this value in `$data`
            $bunga = (int) $data->Bunga;
            if ($data->Status = 1) {
                if ($produk_gadai === 'EMAS') {
                    $piutang = $pokokPinjaman + ($pokokPinjaman * $sewaModalRate);
                } else {
                    $piutang = $pokokPinjaman + $bunga;
                }
            }
        }

        // Format dates for display
        $data->Tgl_jtempo = $tglJtempo->format('d-m-Y');
        $data->Tgl_transaksi = $tglTransaksi->format('d-m-Y');

        //barang var

        $kadarBrg = 0;
        $beratK = 0;
        $beratB = 0;
        $merk = "";
        $tipe = "";
        $tahun = "";

        //barang
        if ($produk_gadai === 'EMAS') {
            $namaBrg = $data->barangEmas->pluck('Jenis')->implode(',');
            $kadarBrg = $this->formatBarang($data, 'Kadar', $produk_gadai);
            $beratK = $this->formatBarang($data, 'Berat_kotor', $produk_gadai);
            $beratB = $this->formatBarang($data, 'Berat_bersih', $produk_gadai);
        } else if ($produk_gadai === 'MOTOR') {
            $namaBrg = $data->barangMotor->map(function ($item) {
                return $item->Merk . ' ' . $item->Tipe;
            })->first();
            $merk = $this->formatBarang($data, 'Merk', $produk_gadai);
            $tipe = $this->formatBarang($data, 'Tipe', $produk_gadai);
            $tahun = $this->formatBarang($data, 'Tahun', $produk_gadai);
        } else if ($produk_gadai === 'MOBIL') {
            $namaBrg = $data->barangMobil->map(function ($item) {
                return $item->Merk . ' ' . $item->Tipe;
            })->first();
            $merk = $this->formatBarang($data, 'Merk', $produk_gadai);
            $tipe = $this->formatBarang($data, 'Tipe', $produk_gadai);
            $tahun = $this->formatBarang($data, 'Tahun', $produk_gadai);
        }

        $pdf = Pdf::loadView('pdf_kartuPiutang', [
            'data' => $data,
            'namaBrg' => $namaBrg,
            'kadarBrg' => $kadarBrg,
            'beratK' => $beratK,
            'beratB' => $beratB,
            'merk' => $merk,
            'tipe' => $tipe,
            'tahun' => $tahun,
            'diffDaysBunga' => $diffDaysBunga,
            'piutang' => $piutang,
            'potonganBunga' => $potonganBunga,
            'denda' => $denda
        ]);
        return $pdf->stream('SBG - ' . $nosbg);
    }

    // New method to format barang
    private function formatBarang($data, $field, $produk)
    {
        if ($produk === 'EMAS') {
            return $data->barangEmas->map(function ($item, $index) use ($field, $data) {
                $value = $item->{$field};

                // Check if the value has a decimal part
                $formattedValue = (floor($value) == $value) ? $value : number_format((float)$value, 2);

                if ($data->barangEmas->count() > 1) {
                    return $formattedValue . '(' . ($index + 1) . ')';
                } else {
                    return $formattedValue;
                }
            })->implode(', ');
        } else if ($produk === 'MOTOR') {
            $values = $data->barangMotor->pluck($field)->all();
            // Convert to a single string if there is only one value
            if (count($values) === 1) {
                return $values[0];
            }
            return $values;
        } else if ($produk === 'MOBIL') {
            $values = $data->barangMobil->pluck($field)->all();
            // Convert to a single string if there is only one value
            if (count($values) === 1) {
                return $values[0];
            }
            return $values;
        }
    }
}
