<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Posko;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PoskoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getAll()
    {
        $pos = Posko::all();
        return $pos;
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
            'kd_posko.required' => 'Kode Posko harus diisi !',
            'kd_cabang.required' => 'Kode Cabang harus diisi !',
            'ket_posko.required' => 'Keterangan Posko harus diisi !',
            'ket_cabang.required' => 'Keterangan Cabang harus diisi !',
        ];

        $request->validate([
            'kd_posko' => 'required|string',
            'kd_cabang' => 'required|string',
            'ket_posko' => 'required|string',
            'ket_cabang' => 'required|string',
        ], $alert);

        $pos = new Posko();
        $pos->kd_posko = $request->kd_posko;
        $pos->kd_cabang = $request->kd_cabang;
        $pos->ket_posko = $request->ket_posko;
        $pos->ket_cabang = $request->ket_cabang;
        $pos->tgl_input = Carbon::now();
        $pos->save();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Posko  $posko
     * @return \Illuminate\Http\Response
     */
    public function show(Posko $posko)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Posko  $posko
     * @return \Illuminate\Http\Response
     */
    public function edit(Posko $posko)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Posko  $posko
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Posko $posko)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Posko  $posko
     * @return \Illuminate\Http\Response
     */
    public function destroy(Posko $posko)
    {
        //
    }
}
