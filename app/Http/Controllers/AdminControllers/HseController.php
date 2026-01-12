<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Hse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/MasterData/Hse');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Hse  $hse
     * @return \Illuminate\Http\Response
     */
    public function show(Hse $hse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Hse  $hse
     * @return \Illuminate\Http\Response
     */
    public function edit(Hse $hse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Hse  $hse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Hse $hse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Hse  $hse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Hse $hse)
    {
        //
    }
}
