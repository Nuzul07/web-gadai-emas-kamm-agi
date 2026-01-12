<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Penaksir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PenaksirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Admin/MasterData/Penaksir', [
            'posko' => $user->POSKO,
            'cabang' => $user->CABANG,
        ]);
    }

    public function getAll()
    {
        $pnk = Penaksir::all();
        return $pnk;
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
            'name.required' => 'Nama harus diisi !'
        ];

        $request->validate([
            'name' => 'required|string',
        ], $alert);

        do {
            $id = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (Penaksir::where('id', 'PNK' . $id)->exists());

        $pnk = new Penaksir();
        $pnk->id = 'PNK' . $id;
        $pnk->name = $request->name;
        $pnk->posko = $request->posko;
        $pnk->cabang = $request->cabang;
        $pnk->save();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Penaksir  $penaksir
     * @return \Illuminate\Http\Response
     */
    public function show(Penaksir $penaksir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Penaksir  $penaksir
     * @return \Illuminate\Http\Response
     */
    public function edit(Penaksir $penaksir)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Penaksir  $penaksir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $pnk = Penaksir::where('id', $request->id)->first();
        $pnk->name = $request->name;
        $pnk->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Penaksir  $penaksir
     * @return \Illuminate\Http\Response
     */
    public function destroy(Penaksir $penaksir)
    {
        //
    }
}
