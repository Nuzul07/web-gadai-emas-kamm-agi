<?php

namespace App\Http\Controllers\AdminControllers;

use App\Exports\MasterData\NasabahExport;
use App\Exports\MasterData\PenaksirExport;
use App\Exports\MasterData\StlExport;
use App\Exports\MasterData\UsersExport;
use App\Exports\Transactions\TransaksiExport;
use App\Exports\Transactions\BayarExport;
use App\Http\Controllers\Controller;
use App\Models\Bayar;
use App\Models\Kurs;
use App\Models\Nasabah;
use App\Models\Penaksir;
use App\Models\PerGadaiOffline;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/Laporan/Index');
    }

    public function nasabahExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $nas = Nasabah::whereBetween('tgl_regis', [$strDate, $endDate])->get();
            if ($nas->isNotEmpty()) {
                return Excel::download(new NasabahExport($nas, $strDate, $endDate), 'Nasabah Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $nas = Nasabah::all();
            if ($nas->isNotEmpty()) {
                return Excel::download(new NasabahExport($nas, $strDate, $endDate), 'Nasabah Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }

    public function stlExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $stl = Kurs::whereBetween('Tgl_input', [$strDate, $endDate])->get();
            if ($stl->isNotEmpty()) {
                return Excel::download(new StlExport($stl, $strDate, $endDate), 'Stl Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $stl = Kurs::all();
            if ($stl->isNotEmpty()) {
                return Excel::download(new StlExport($stl, $strDate, $endDate), 'Stl Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }

    public function penaksirExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $penaksir = Penaksir::whereBetween('tgl_input', [$strDate, $endDate])->get();
            if ($penaksir->isNotEmpty()) {
                return Excel::download(new PenaksirExport($penaksir, $strDate, $endDate), 'Penaksir Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $penaksir = Penaksir::all();
            if ($penaksir->isNotEmpty()) {
                return Excel::download(new PenaksirExport($penaksir, $strDate, $endDate), 'Penaksir Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }

    public function usersExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $users = User::where(function ($query) {
                $query->where('user_id', 'like', '%gde%')
                    ->orWhere('user_id', 'like', '%GDE%');
            })->whereBetween('CREATE_DATE', [$strDate, $endDate])->get();
            if ($users->isNotEmpty()) {
                return Excel::download(new UsersExport($users, $strDate, $endDate), 'Users Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $users = User::where(function ($query) {
                $query->where('user_id', 'like', '%gde%')
                    ->orWhere('user_id', 'like', '%GDE%');
            })->get();
            if ($users->isNotEmpty()) {
                return Excel::download(new UsersExport($users, $strDate, $endDate), 'Users Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }

    public function transExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $trans = PerGadaiOffline::whereBetween('Tgl_transaksi', [$strDate, $endDate])->get();
            if ($trans->isNotEmpty()) {
                return Excel::download(new TransaksiExport($trans, $strDate, $endDate), 'Trans Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $trans = PerGadaiOffline::all();
            if ($trans->isNotEmpty()) {
                return Excel::download(new TransaksiExport($trans, $strDate, $endDate), 'Trans Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }

    public function bayarExport(Request $request)
    {
        $strDate = $request->strDate;
        $endDate = $request->endDate;

        if ($strDate && $endDate) {
            $bayar = Bayar::whereBetween('Tgl_pelunasan', [$strDate, $endDate])->get();
            if ($bayar->isNotEmpty()) {
                return Excel::download(new BayarExport($bayar, $strDate, $endDate), 'Bayar Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        } else {
            $bayar = Bayar::all();
            if ($bayar->isNotEmpty()) {
                return Excel::download(new BayarExport($bayar, $strDate, $endDate), 'Bayar Recap.xlsx');
            } else {
                return response()->json(['statusText' => 'Tidak ada data'], 404);
            }
        }
    }
}
