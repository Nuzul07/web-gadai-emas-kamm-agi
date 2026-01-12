<?php

use App\Http\Controllers\AdminControllers\BagianLainController;
use App\Http\Controllers\AdminControllers\BarangJaminanController;
use App\Http\Controllers\AdminControllers\ExportController;
use App\Http\Controllers\AdminControllers\HseController;
use App\Http\Controllers\AdminControllers\HspController;
use App\Http\Controllers\AdminControllers\IndexController;
use App\Http\Controllers\AdminControllers\KursController;
use App\Http\Controllers\AdminControllers\LelangController;
use App\Http\Controllers\AdminControllers\LocationController;
use App\Http\Controllers\AdminControllers\NasabahController;
use App\Http\Controllers\AdminControllers\PDFController;
use App\Http\Controllers\AdminControllers\PelunasanController;
use App\Http\Controllers\AdminControllers\PenaksirController;
use App\Http\Controllers\AdminControllers\PerGadaiOfflineController;
use App\Http\Controllers\AdminControllers\PerpanjanganController;
use App\Http\Controllers\AdminControllers\PoskoController;
use App\Http\Controllers\AdminControllers\ProfileController;
use App\Http\Controllers\AdminControllers\sbgDwilipat;
use App\Http\Controllers\AdminControllers\sbgDwilipatController;
use App\Http\Controllers\AdminControllers\SimulasiController;
use App\Http\Controllers\AdminControllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Maatwebsite\Excel\Row;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/pdf-preview', function () {
    return view('pdf_preview');
});

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Auth/Login');
    });
});

Route::group(['prefix' => 'gadaiemas-&-dashboard', 'middleware' => ['auth']], function () {
    Route::get('index', [IndexController::class, 'index'])->name('gadaiDashboard');
    Route::get('helper-index', [IndexController::class, 'helperIndex'])->name('helperIndex');

    Route::group(['middleware' => 'role:pusat'], function () {
        //Route user
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::get('riwayatUser', [UserController::class, 'riwayatIndex'])->name('riwayatUser');
        Route::get('getAddress', [LocationController::class, 'getAddress'])->name('getAddress');
        Route::get('getUser', [UserController::class, 'getAll'])->name('getUser');
        Route::get('getHistory', [UserController::class, 'getHistoryLogin'])->name('getHistory');
        Route::post('storeUser', [UserController::class, 'store'])->name('storeUser');
        Route::post('updateUser', [UserController::class, 'update'])->name('updateUser');
        Route::post('updateAktif', [UserController::class, 'updateStatusAktif'])->name('updateAktif');
        Route::post('deleteUser', [UserController::class, 'destroy'])->name('deleteUser');
        Route::post('updateNonAktif', [UserController::class, 'updateStatusNonAktif'])->name('updateNonAktif');
        Route::get('getPosko', [PoskoController::class, 'getAll'])->name('getPosko');
        Route::post('storePosko', [PoskoController::class, 'store'])->name('storePosko');
    });

    // Routes accessible by spv and pusat roles
    Route::group(['middleware' => 'role:pusat,super'], function () {
        //Route Master Data
        Route::get('hse', [HseController::class, 'index'])->name('hse');

        Route::get('stl', [KursController::class, 'index'])->name('kurs');
        Route::get('getKurs', [KursController::class, 'getAll'])->name('getKurs');
        Route::post('storeKurs', [KursController::class, 'store'])->name('storeKurs');
        Route::post('updateKursAktif', [KursController::class, 'updateStatusAktif'])->name('updateKursAktif');
        Route::post('updateKursNonAktif', [KursController::class, 'updateStatusNonAktif'])->name('updateKursNonAktif');

        Route::get('hps', [HspController::class, 'index'])->name('hps');
        Route::post('storeHsp', [HspController::class, 'store'])->name('storeHsp');
        Route::post('updateHsp', [HspController::class, 'update'])->name('updateHsp');
        Route::post('deleteHsp', [HspController::class, 'destroy'])->name('deleteHsp');

        Route::get('penaksir', [PenaksirController::class, 'index'])->name('penaksir');
        Route::get('getPenaksir', [PenaksirController::class, 'getAll'])->name('getPenaksir');
        Route::post('storePenaksir', [PenaksirController::class, 'store'])->name('storePenaksir');
        Route::post('updatePenaksir', [PenaksirController::class, 'update'])->name('updatePenaksir');

        Route::get('bagianLain', [BagianLainController::class, 'EmasIndex'])->name('bagianLain');
        Route::post('updateBagianLain', [BagianLainController::class, 'updateEmas'])->name('updateBagianLain');

        Route::get('sewaModal', [BagianLainController::class, 'SewaModalIndex'])->name('sewaModal');
        Route::post('updateSewaModal', [BagianLainController::class, 'updateSewaModal'])->name('updateSewaModal');
    });

    // Routes accessible by kasir, spv, and pusat roles
    Route::group(['middleware' => 'role:pusat,super,kasir'], function () {
        //Route Nasabah
        Route::get('nasabah', [NasabahController::class, 'index'])->name('nasabah');
        Route::get('getAllNasabah', [NasabahController::class, 'getAll'])->name('getAllNasabah');
        Route::get('getProvinsi', [NasabahController::class, 'getProvinsi'])->name('getProvinsi');
        Route::post('storeNasabah', [NasabahController::class, 'store'])->name('storeNasabah');

        // Route get Hsp
        Route::get('getHsp', [HspController::class, 'getAll'])->name('getHsp');

        //Route Gadai Offline
        Route::get('perGadaiOffline', [PerGadaiOfflineController::class, 'index'])->name('perGadaiOffline');
        Route::get('getNasabahId', [PerGadaiOfflineController::class, 'getNasabahId'])->name('getNasabahId');
        Route::get('getMasterCode', [PerGadaiOfflineController::class, 'getMasterCode'])->name('getMasterCode');
        Route::get('getPenaksirByPosko', [PerGadaiOfflineController::class, 'getPenaksirByPosko'])->name('getPenaksirByPosko');
        Route::get('getCabang', [PerGadaiOfflineController::class, 'getCabang'])->name('getCabang');
        Route::get('getGadaiEmas', [PerGadaiOfflineController::class, 'getGadaiEmas'])->name('getGadaiEmas');
        Route::get('getGadaiMotor', [PerGadaiOfflineController::class, 'getGadaiMotor'])->name('getGadaiMotor');
        Route::get('getGadaiMobil', [PerGadaiOfflineController::class, 'getGadaiMobil'])->name('getGadaiMobil');
        Route::post('storeGadai', [PerGadaiOfflineController::class, 'store'])->name('storeGadai');
        Route::post('uploadTerimaUang', [PerGadaiOfflineController::class, 'uploadTerimaUang'])->name('uploadTerimaUang');
        Route::post('uploadTambahan', [PerGadaiOfflineController::class, 'uploadLain'])->name('uploadTambahan');
        Route::post('uploadBuktiLain', [PerGadaiOfflineController::class, 'uploadBuktiLain'])->name('uploadBuktiLain');
        Route::get('getNoSbg', [PerGadaiOfflineController::class, 'getNoSbg'])->name('getNoSbg');
        Route::get('getGadaiSBG', [PerGadaiOfflineController::class, 'getGadaiSBG'])->name('getGadaiSBG');
        Route::get('getBarangBySbg', [BarangJaminanController::class, 'getBarangJaminan'])->name('getBarangJaminan');
        Route::post('storeBarangJaminan', [BarangJaminanController::class, 'store'])->name('storeBarang');
        Route::post('deleteBarangJaminan', [BarangJaminanController::class, 'delete'])->name('deleteBarang');
        Route::get('getMotorBySbg', [BarangJaminanController::class, 'getJaminanMotor'])->name('getJaminanMotor');
        Route::post('storeJaminanMotor', [BarangJaminanController::class, 'storeMotor'])->name('storeMotor');
        Route::post('deleteJaminanMotor', [BarangJaminanController::class, 'deleteMotor'])->name('deleteMotor');
        Route::get('getMobilBySbg', [BarangJaminanController::class, 'getJaminanMobil'])->name('getJaminanMobil');
        Route::post('storeJaminanMobil', [BarangJaminanController::class, 'storeMobil'])->name('storeMobil');
        Route::post('deleteJaminanMobil', [BarangJaminanController::class, 'deleteMobil'])->name('deleteMobil');
        Route::get('generate-pdf', [PDFController::class, 'generatePDF'])->name('generatepdf');
        Route::get('generate-pdf-dwi', [PDFController::class, 'generatePDFDwi'])->name('generatepdfDwi');
        Route::get('generate-pdf-lunas', [PDFController::class, 'generatePDFLunas'])->name('generatepdfLunas');
        Route::get('generate-pdf-panjang', [PDFController::class, 'generatePDFPerpanjang'])->name('generatepdfPerpanjang');
        Route::get('generate-pdf-mohon-lelang', [PDFController::class, 'generatePDFMohonLelang'])->name('generatepdfMohonLelang');
        Route::get('generate-pdf-kwitansi-lelang', [PDFController::class, 'generatePDFKwitansi'])->name('generatepdfKwitansi');
        Route::get('generate-pdf-kartu-piutang', [PDFController::class, 'generatePDFPiutang'])->name('generatepdfPiutang');
        Route::get('getGolongan', [PerGadaiOfflineController::class, 'getGolongan'])->name('getGolongan');
        Route::get('getSewaModal', [PerGadaiOfflineController::class, 'getSewaModal'])->name('getSewaModal');

        //Route Perpanjangan
        Route::get('perpanjangan', [PerpanjanganController::class, 'index'])->name('perpanjangan');
        Route::get('getGadaiEmasActive', [PerpanjanganController::class, 'getGadaiEmasActive'])->name('getGadaiEmasActive');
        Route::get('getGadaiMotorActive', [PerpanjanganController::class, 'getGadaiMotorActive'])->name('getGadaiMotorActive');
        Route::get('getGadaiMobilActive', [PerpanjanganController::class, 'getGadaiMobilActive'])->name('getGadaiMobilActive');
        Route::post('storeExtGadai', [PerpanjanganController::class, 'storeExtGadai'])->name('storeExtGadai');
        Route::post('storeExtGadaiMotor', [PerpanjanganController::class, 'storeExtGadaiMotor'])->name('storeExtGadaiMotor');


        //Route Pelunasan
        Route::get('pelunasan', [PelunasanController::class, 'index'])->name('pelunasan');
        Route::post('storePelunasan', [PelunasanController::class, 'store'])->name('storePelunasan');

        //Route Simulasi
        Route::get('simulasi-lunas', [SimulasiController::class, 'index'])->name('simulasiLunas');

        //Route Lelang
        Route::get('lelang', [LelangController::class, 'index'])->name('lelang');
        Route::get('validasi-spv', [LelangController::class, 'indexValidasi'])->name('validasiLelang');
        Route::get('input-approve', [LelangController::class, 'indexInput'])->name('inputLelang');
        Route::get('getLelang', [LelangController::class, 'getLelang'])->name('getLelang');
        Route::get('getLelangApprove', [LelangController::class, 'getLelangApprove'])->name('getLelangApprove');
        Route::post('getLelangByNomor', [LelangController::class, 'getLelangByNomor'])->name('getLelangByNomor');
        Route::post('getLelangByNomorApprove', [LelangController::class, 'getLelangByNomorApprove'])->name('getLelangByNomorApprove');
        Route::get('getNoLelang', [LelangController::class, 'getNoLelang'])->name('getNoLelang');
        Route::get('getNoKwitansi', [LelangController::class, 'getNoKwitansi'])->name('getNoKwitansi');
        Route::post('storeLelang', [LelangController::class, 'store'])->name('storeLelang');
        Route::post('updateLelang', [LelangController::class, 'update'])->name('updateLelang');
        Route::post('storeLelangApprove', [LelangController::class, 'inputLelangApprove'])->name('storeLelangApprove');

        //Route Kartu Piutang
        Route::get('kartu-piutang', [PerGadaiOfflineController::class, 'piutangIndex'])->name('kartuPiutang');

        //Route SBG Dwilipat
        Route::get('dwilipatGadai', [sbgDwilipatController::class, 'gadai'])->name('dwilipatGadai');
        Route::get('dwilipatPanjang', [sbgDwilipatController::class, 'panjang'])->name('dwilipatPanjang');
        Route::get('dwilipatLunas', [sbgDwilipatController::class, 'lunas'])->name('dwilipatLunas');

        //Route Laporan
        //masterData
        Route::get('laporan-all', [ExportController::class, 'index'])->name('laporanIndex');
        Route::get('export--nasabah', [ExportController::class, 'nasabahExport'])->name('nasabahExport');
        Route::get('export--stl', [ExportController::class, 'stlExport'])->name('stlExport');
        Route::get('export--penaksir', [ExportController::class, 'penaksirExport'])->name('penaksirExport');
        Route::get('export--users', [ExportController::class, 'usersExport'])->name('usersExport');
        //Transactions
        Route::get('export--trans', [ExportController::class, 'transExport'])->name('transExport');
        Route::get('export--bayar', [ExportController::class, 'bayarExport'])->name('bayarExport');
    });
});


// Route::group(['middleware' => 'auth'], function(){

//     // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';