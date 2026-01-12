<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Golongan;
use App\Models\SewaModal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BagianLainController extends Controller
{
    public function EmasIndex()
    {
        return Inertia::render('Admin/MasterData/Bagianlain');
    }

    public function SewaModalIndex()
    {
        $haha = "huhu";
        return Inertia::render('Admin/MasterData/SewaModal');haha
    }

    public function updateEmas(Request $request)
    {
        $alert = [
            'denda1_7.required' => 'Denda 1-7 hari tidak boleh kosong !',
            'denda1_7.numeric' => 'Input menggunakan angka !',
            'denda8_15.required' => 'Denda 8-15 hari tidak boleh kosong',
            'denda8_15.numeric' => 'Input menggunakan angka !',
            'denda16_30.required' => 'Denda 16-30 hari tidak boleh kosong',
            'denda16_30.numeric' => 'Input menggunakan angka !',
            'byadmin.required' => 'Biaya Admin tidak boleh kosong',
            'byadmin.numeric' => 'Input menggunakan angka !',
            'patok_taksiran.required' => 'Patok taksiran tidak boleh kosong',
            'patok_taksiran.numeric' => 'Input menggunakan angka !',
            'sewa_modal_15.required' => 'Sewa modal 1-15 hari tidak boleh kosong',
            'sewa_modal_15.numeric' => 'Input menggunakan angka !',
            'sewa_modal16_30.required' => 'Sewa modal 16-30 hari tidak boleh kosong',
            'sewa_modal16_30.numeric' => 'Input menggunakan angka !',
            'sewa_modal31_45.required' => 'Sewa modal 31-45 hari tidak boleh kosong',
            'sewa_modal31_45.numeric' => 'Input menggunakan angka !',
            'sewa_modal46_60.required' => 'Sewa modal 46-60 hari tidak boleh kosong',
            'sewa_modal46_60.numeric' => 'Input menggunakan angka !',
            'sewa_modal61_75.required' => 'Sewa modal 61-75 hari tidak boleh kosong',
            'sewa_modal61_75.numeric' => 'Input menggunakan angka !',
            'sewa_modal76_90.required' => 'Sewa modal 76-90 hari tidak boleh kosong',
            'sewa_modal76_90.numeric' => 'Input menggunakan angka !',
            'sewa_modal91_105.required' => 'Sewa modal 91_105 hari tidak boleh kosong',
            'sewa_modal91_105.numeric' => 'Input menggunakan angka !',
            'sewa_modal105_atas.required' => 'Sewa modal 105 hari keatas tidak boleh kosong',
            'sewa_modal105_atas.numeric' => 'Input menggunakan angka !',
        ];

        $request->validate([
            'denda1_7' => 'required|numeric',
            'denda8_15' => 'required|numeric',
            'denda16_30' => 'required|numeric',
            'byadmin' => 'required|numeric',
            'patok_taksiran' => 'required|numeric',
            'sewa_modal_15' => 'required|numeric',
            'sewa_modal16_30' => 'required|numeric',
            'sewa_modal31_45' => 'required|numeric',
            'sewa_modal46_60' => 'required|numeric',
            'sewa_modal61_75' => 'required|numeric',
            'sewa_modal76_90' => 'required|numeric',
            'sewa_modal91_105' => 'required|numeric',
            'sewa_modal105_atas' => 'required|numeric',
        ], $alert);

        $gl = Golongan::where('GOLONGAN', $request->golongan)->first();
        $gl->PERSEN_DENDA1_7HARI = $request->denda1_7 / 100;
        $gl->PERSEN_DENDA8_15HARI = $request->denda8_15 / 100;
        $gl->PERSEN_DENDA16_30HARI = $request->denda16_30 / 100;
        $gl->PERSEN_BYADMIN = $request->byadmin / 100;
        $gl->PERSEN_PATOK_TAKSIRAN = $request->patok_taksiran / 100;
        $gl->SEWA_MODAL_15HARI = $request->sewa_modal_15 / 100;
        $gl->SEWA_MODAL16_30HARI = $request->sewa_modal16_30 / 100;
        $gl->SEWA_MODAL31_45HARI = $request->sewa_modal31_45 / 100;
        $gl->SEWA_MODAL46_60HARI = $request->sewa_modal46_60 / 100;
        $gl->SEWA_MODAL61_75HARI = $request->sewa_modal61_75 / 100;
        $gl->SEWA_MODAL76_90HARI = $request->sewa_modal76_90 / 100;
        $gl->SEWA_MODAL91_105HARI = $request->sewa_modal91_105 / 100;
        $gl->SEWA_MODAL_105_ATAS_HARI = $request->sewa_modal105_atas / 100;
        $gl->update();
        return back();
    }

    public function updateSewaModal(Request $request)
    {
        $alert = [
            'bunga_15.required' => 'Bunga 15 hari tidak boleh kosong !',
            'bunga_15.numeric' => 'Input menggunakan angka !',
            'bunga_30.required' => 'Bunga 30 hari tidak boleh kosong',
            'bunga_30.numeric' => 'Input menggunakan angka !',
            'bunga_60.required' => 'Bunga 60 hari tidak boleh kosong',
            'bunga_60.numeric' => 'Input menggunakan angka !',
            'bunga_90.required' => 'Bunga 90 Hari tidak boleh kosong',
            'bunga_90.numeric' => 'Input menggunakan angka !',
            // 'bunga_120.required' => 'Bunga 120 Hari tidak boleh kosong',
            // 'bunga_120.numeric' => 'Input menggunakan angka !',
            'admin_15.required' => 'Admin 15 Hari tidak boleh kosong',
            'admin_15.numeric' => 'Input menggunakan angka !',
            'admin_30.required' => 'Admin 30 hari tidak boleh kosong',
            'admin_30.numeric' => 'Input menggunakan angka !',
            'admin_60.required' => 'Admin 60 hari tidak boleh kosong',
            'admin_60.numeric' => 'Input menggunakan angka !',
            'admin_90.required' => 'Admin 90 hari tidak boleh kosong',
            'admin_90.numeric' => 'Input menggunakan angka !',
            // 'admin_120.required' => 'Admin 120 hari tidak boleh kosong',
            // 'admin_120.numeric' => 'Input menggunakan angka !'
        ];

        $request->validate([
            'bunga_15' => 'required|numeric',
            'bunga_30' => 'required|numeric',
            'bunga_60' => 'required|numeric',
            'bunga_90' => 'required|numeric',
            // 'bunga_120' => 'required|numeric',
            'admin_15' => 'required|numeric',
            'admin_30' => 'required|numeric',
            'admin_60' => 'required|numeric',
            'admin_90' => 'required|numeric',
            // 'admin_120' => 'required|numeric'
        ], $alert);

        $sm = SewaModal::where('type_kendaraan', $request->type_kendaraan)->first();
        $sm->bunga_15hari = $request->bunga_15 / 100;
        $sm->bunga_30hari = $request->bunga_30 / 100;
        $sm->bunga_60hari = $request->bunga_60 / 100;
        $sm->bunga_90hari = $request->bunga_90 / 100;
        // $sm->bunga_120hari = $request->bunga_120 / 100;
        $sm->admin_15hari = $request->admin_15 / 100;
        $sm->admin_30hari = $request->admin_30 / 100;
        $sm->admin_60hari = $request->admin_60 / 100;
        $sm->admin_90hari = $request->admin_90 / 100;
        // $sm->admin_120hari = $request->admin_120 / 100;
        $sm->update();
        return back();
    }
}
