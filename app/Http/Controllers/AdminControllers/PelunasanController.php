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
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PelunasanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Transactions/Pelunasan');
    }


    public function store(Request $request)
    {
        $alert = [
            'by_lain' => 'Input Menggunakan Angka !',
            'cara_bayar' => 'Cara Bayar harus diisi !',
            'jenis_pembayaran' => 'Jenis Pembayaran harus diisi !'
        ];

        $request->validate([
            'no_sbg' => 'required|string',
            'pokok_pinjaman' => 'required|numeric',
            'sewa_modal' => 'required|numeric',
            'denda_pinjaman' => 'required|string',
            'by_lain' => 'nullable|numeric',
            'total_pembayaran' => 'required|numeric',
            'cara_bayar' => 'required|string',
            'jenis_pembayaran' => 'required|string',
        ], $alert);

        try {
            DB::transaction(function () use ($request) {
                $gd = PerGadaiOffline::with(['nasabah'])->where('No_sbg', $request->no_sbg)->first();
                $bj = BarangJaminan::where('No_sbg', $request->no_sbg)->get();
                $noBayar = CustomHelpers::getNoBayar($request);

                // Save Bayar()
                $byr = new Bayar();
                $byr->fill([
                    'No_bayaran' => $noBayar,
                    'No_sbg' => $request->no_sbg,
                    'Tgl_pelunasan' => Carbon::now(),
                    'Pokok_pinjaman' => $request->pokok_pinjaman,
                    'Sewa_modal' => $request->sewa_modal / 100,
                    'Denda_pinjaman' => $request->denda_pinjaman,
                    'By_lain' => $request->by_lain,
                    'Total_pembayaran' => $request->total_pembayaran,
                    'Cara_bayar' => $request->cara_bayar,
                    'Jenis_pembayaran' => $request->jenis_pembayaran,
                    'User_input' => $request->user()->USER_NAME,
                    'Jenis_transaksi' => "PELUNASAN",
                    'Piutang' => $request->bunga + $request->pokok_pinjaman,
                    'Bunga' => $request->bunga
                ]);
                $byr->save();

                // Save Bayar Gadai
                $byrGd = new BayarGadai();
                $dateByr = DB::select(DB::raw("SELECT CAST(CAST(CAST(:date as date) as datetime) as int) + 693596 as numeric_date"), ['date' => Carbon::now()]);
                $dateJT = DB::select(DB::raw("SELECT cast(cast(cast(? as date) as datetime) as int)+693596 as numeric_date"), [$gd->Tgl_jtempo]);
                $byrGd->fill([
                    'No_bayar' => $noBayar,
                    'Tgl_byr' => $dateByr[0]->numeric_date,
                    'No_faktur' => $request->no_sbg,
                    'Jenis_trans' => "RAHN",
                    'Posko' => Auth::user()->POSKO,
                    'Cabang' => Auth::user()->CABANG,
                    'K_kons' => $gd->No_kons,
                    'Tgl_due' => $dateJT[0]->numeric_date,
                    'Pokok' => $gd->Pokok_pinjaman,
                    'Bunga' => $request->bunga,
                    'Denda' => $request->denda_pinjaman,
                    'Nilai_tebus' => $request->total_pembayaran,
                    'Hasil_diterima' => $request->total_pembayaran,
                    'TglTebus1' => $dateByr[0]->numeric_date
                ]);
                $byrGd->save();

                // Save His Setor
                $his_setor = new HistorySetor();
                $date = DB::select(DB::raw("SELECT cast(cast(cast(getdate() as date) as datetime) as int)+693596 as numeric_date"));
                $dateOP = DB::select(DB::raw("SELECT cast(cast(cast(? as date) as datetime) as int)+693596 as numeric_date"), [$gd->Tgl_transaksi]);
                $his_setor->fill([
                    'No_setor' => "",
                    'No_trans' => $noBayar,
                    'Tgl_by' => $date[0]->numeric_date,
                    'Tgl_trans' => $dateOP[0]->numeric_date,
                    'Nama' => $gd->nasabah->Nama,
                    'Nilai_rp' => $request->total_pembayaran,
                    'posko' => Auth::user()->POSKO,
                    'cabang' => Auth::user()->CABANG,
                    'Type_trans' => "LUNAS",
                    'Flag' => "",
                    'Tgl_setor' => null
                ]);
                $his_setor->save();

                // Save History Trans
                $his_trans = new HistoryTrans();
                $his_trans->fill([
                    'NO_FAKTUR' => $request->no_sbg,
                    'NO_TRANSAKSI' => "",
                    'K_KONS' => $gd->No_kons,
                    'NO_BAYAR' => $noBayar,
                    'USERID' => Auth::user()->RESERVED2_TXT,
                    'TGL_INPUT' => $date[0]->numeric_date,
                    'JAM_INPUT' => Carbon::now()->format("H:i:s"),
                    'SETOR' => "",
                    'POSKO' => Auth::user()->POSKO,
                    'CABANG' => Auth::user()->CABANG
                ]);
                $his_trans->save();

                if ($gd && $bj) {
                    $gd->Status = 0;
                    $gd->save();
                    foreach ($bj as $b) {
                        $b->Status_barang = 0;
                        $b->save();
                    };
                }
            });

            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'pesan' => $e->getMessage()
            ], 500);
        }
    }
}