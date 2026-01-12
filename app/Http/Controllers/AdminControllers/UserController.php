<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\History;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Admin/MasterData/User');
    }

    public function riwayatIndex()
    {
        return Inertia::render('Admin/MasterData/Riwayat');
    }

    public function getAll()
    {
        $nas = User::where(function ($query) {
            $query->where('user_id', 'like', 'gdea_%')
                ->orWhere('user_id', 'like', 'GDEA_%');
        })->get();

        return $nas;
    }

    public function getHistoryLogin()
    {
        $his = History::orderBy('login_at', 'desc')
            ->get();

        return $his;
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
            'user_id.required' => 'User ID harus diisi !',
            'user_name.required' => 'Username harus diisi !',
            'user_password.required' => 'Password harus diisi !',
            'posko_sel.required' => 'Posko harus diisi !',
            'level_user' => 'Level User harus dipilih !',
        ];

        $request->validate([
            'user_id' => 'required|string',
            'user_name' => 'required|string',
            'user_password' => 'required|string',
            'posko_sel' => 'required|string',
            'level_user' => 'required|string',
        ], $alert);

        $exists = User::where('USER_ID', $request->user_id)
            ->where('USER_NAME', $request->user_name)
            ->first();

        if ($exists) {
            return response()->json([
                'available' => false
            ]);
        }

        $user = new User();
        $user->USER_ID = $request->user_id;
        $user->USER_NAME = $request->user_name;
        $user->USER_PASSWORD = $request->user_password;
        $user->LEVEL_USER = $request->level_user;
        $user->RESERVED2_TXT = 'USR' . $request->posko_sel;
        $user->POSKO = $request->posko_sel;
        $user->CABANG = $request->cabang_sel;
        $user->AKTIF = 1;
        $user->CREATE_DATE = Carbon::now();
        $user->PASSWORD_MD5 = md5($request->user_password);
        $user->save();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function updateStatusAktif(Request $request)
    {
        $user = User::where('user_id', $request->user_id)->first();
        $user->AKTIF = 1;
        $user->save();
    }

    public function updateStatusNonAktif(Request $request)
    {
        $user = User::where('user_id', $request->user_id)->first();
        $user->AKTIF = 0;
        $user->save();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'user_password' => 'required|string',
        ]);

        $user = User::where('user_id', $request->user_id)->first();
        $user->USER_NAME = $request->user_name;
        $user->USER_PASSWORD = $request->user_password;
        $user->update();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user_id = $request->user_id;

        // Ensure $user_id is always an array
        if (!is_array($user_id)) {
            $user_id = [$user_id]; // Convert single ID to array
        }

        // Perform the delete operation
        DB::connection('sqlsrv')->table('TBL_USER')->whereIn('USER_ID', $user_id)->delete();

        return response()->json(['message' => 'Selected items deleted successfully']);
    }
}