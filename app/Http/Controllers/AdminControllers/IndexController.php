<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\BarangJaminan;
use App\Models\Bayar;
use App\Models\Nasabah;
use App\Models\PerGadaiOffline;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nas = Nasabah::count();
        $gdOnEmas = PerGadaiOffline::where('Status', 1)->where('Produk_gadai', 'EMAS')->count();
        $gdOnMotor = PerGadaiOffline::where('Status', 1)->where('Produk_gadai', 'MOTOR')->count();
        $gdOnMobil = PerGadaiOffline::where('Status', 1)->where('Produk_gadai', 'MOBIL')->count();
        $gdOffEmas = PerGadaiOffline::where('Status', 0)->where('Produk_gadai', 'EMAS')->count();
        $gdOffMotor = PerGadaiOffline::where('Status', 0)->where('Produk_gadai', 'MOTOR')->count();
        $gdOffMobil = PerGadaiOffline::where('Status', 0)->where('Produk_gadai', 'MOBIL')->count();
        $brgJmnEmas = BarangJaminan::where('Status_barang', 1)->count();
        $brgJmnMotor = 0; 
        $brgJmnMobil = 0;
        $byrEmas = Bayar::whereHas('perGadaiOffline', function ($query) {
            $query->where('Produk_gadai', 'EMAS');
        })->with('perGadaiOffline')->orderBy('Tgl_pelunasan', 'desc')->get();
        $byrMotor = Bayar::whereHas('perGadaiOffline', function ($query) {
            $query->where('Produk_gadai', 'MOTOR');
        })->with('perGadaiOffline')->orderBy('Tgl_pelunasan', 'desc')->get();
        $byrMobil = Bayar::whereHas('perGadaiOffline', function ($query) {
            $query->where('Produk_gadai', 'MOBIL');
        })->with('perGadaiOffline')->orderBy('Tgl_pelunasan', 'desc')->get();
        // dd($gdOn);

        return Inertia::render('Admin/Index', [
            'nas' => $nas,
            'gdOnEmas' => $gdOnEmas,
            'gdOnMotor' => $gdOnMotor,
            'gdOnMobil' => $gdOnMobil,
            'gdOffEmas' => $gdOffEmas,
            'gdOffMotor' => $gdOffMotor,
            'gdOffMobil' => $gdOffMobil,
            'brgJmnEmas' => $brgJmnEmas,
            'brgJmnMotor' => $brgJmnMotor,
            'brgJmnMobil' => $brgJmnMobil,
            'byrEmas' => $byrEmas,
            'byrMotor' => $byrMotor,
            'byrMobil' => $byrMobil
        ]);
    }

    public function helperIndex()
    {
        return Inertia::render('Bantuan/Index');
    }

    // public function nasabahCount()
    // {
    //     $nas = Nasabah::count();
    //     return redirect()->route('gadaiDashboard')->with(['nas' => $nas]);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
}