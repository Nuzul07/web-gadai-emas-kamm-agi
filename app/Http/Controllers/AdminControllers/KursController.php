<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Kurs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/MasterData/Kurs');
    }

    public function getAll()
    {
        $kurs = Kurs::orderBy('tgl_input', 'desc')->get();
        return $kurs;
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
            'kurs.required' => 'STL harus diisi !',
            'kurs.numeric' => 'Input menggunakan angka !',
            'tgl_berlaku.required' => 'Tgl Berlaku harus diisi !',
        ];

        $request->validate([
            'kurs' => 'required|numeric',
            'tgl_berlaku' => 'required|date',
        ], $alert);

        $kurs = new Kurs();
        $kurs->Tgl_input = Carbon::now();
        $kurs->Kurs = $request->kurs;
        $kurs->Tgl_berlaku = $request->tgl_berlaku;
        $kurs->active = 0;
        $kurs->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kurs  $kurs
     * @return \Illuminate\Http\Response
     */
    public function show(Kurs $kurs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kurs  $kurs
     * @return \Illuminate\Http\Response
     */
    public function edit(Kurs $kurs)
    {
        //
    }

    public function updateStatusAktif(Request $request)
    {
        $kurs = Kurs::where('Tgl_input', $request->tgl_input)->first();
        $kurs->active = 1;
        $kurs->update();
    }

    public function updateStatusNonAktif(Request $request)
    {
        $kurs = Kurs::where('Tgl_input', $request->tgl_input)->first();
        $kurs->active = 0;
        $kurs->update();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kurs  $kurs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kurs $kurs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kurs  $kurs
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kurs $kurs)
    {
        //
    }
}
