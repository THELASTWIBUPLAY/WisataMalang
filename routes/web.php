<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WisataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\FasilitasController;


Route::get('/', [WisataController::class, 'landing']);
Route::get('/wisata/{id}/wisata_show', [WisataController::class, 'show_ajax']);

Route::group(['prefix' => 'wisata'], function () {
    Route::get('/', [WisataController::class, 'index']); // Halaman Admin
    Route::get('/rekomendasi', [WisataController::class, 'rekomendasi']); // Halaman User
    Route::get('/list', [WisataController::class, 'list']);
    Route::get('/create_ajax', [WisataController::class, 'create_ajax']);
    Route::post('/store_ajax', [WisataController::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax', [WisataController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [WisataController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [WisataController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax', [WisataController::class, 'delete_ajax']);
    Route::get('/{id}/show_ajax', [WisataController::class, 'show_ajax']); // Detail Modal
    Route::post('/hitung_saw_ajax', [WisataController::class, 'hitung_saw_ajax']); // Proses SAW
});

Route::group(['prefix' => 'fasilitas'], function () {
    Route::get('/', [FasilitasController::class, 'index']);
    Route::get('/list', [FasilitasController::class, 'list']);
    Route::get('/create_ajax', [FasilitasController::class, 'create_ajax']);
    Route::post('/store_ajax', [FasilitasController::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax', [FasilitasController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [FasilitasController::class, 'update_ajax']);

    Route::get('/{id}/delete_ajax', [FasilitasController::class, 'confirm_ajax']);

    // 2. Rute DELETE: Untuk EKSEKUSI penghapusan (dipanggil saat klik "Ya, Hapus" di dalam modal)
    Route::delete('/{id}/delete_ajax', [FasilitasController::class, 'delete_ajax']);
});

Route::group(['prefix' => 'kriteria'], function () {
    Route::get('/', [KriteriaController::class, 'index']);
    Route::get('/list', [KriteriaController::class, 'list']);
    Route::get('/create_ajax', [KriteriaController::class, 'create_ajax']);
    Route::post('/store_ajax', [KriteriaController::class, 'store_ajax']);
    Route::get('/{id}/show_ajax', [KriteriaController::class, 'show_ajax']);
    Route::get('/{id}/edit_ajax', [KriteriaController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax', [KriteriaController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax', [KriteriaController::class, 'confirm_ajax']); // Untuk modal
    Route::delete('/{id}/delete_ajax', [KriteriaController::class, 'delete_ajax']); // Untuk eksekusi
});

Route::middleware('guest')->group(function () {
    // Rute untuk MENAMPILKAN form registrasi (sudah ada)
    Route::get('/register', [AuthController::class, 'register']);

    // Rute untuk MEMPROSES data registrasi (TAMBAHKAN INI)
    Route::post('/register', [AuthController::class, 'postRegister']); 
    
    // Login routes...
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'postLogin']);
});

// Logout bisa diakses kapan saja asal sudah login
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Halaman Profil (Satu rute untuk semua)
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update']);

    // Halaman Rekomendasi
    Route::get('/rekomendasi', [WisataController::class, 'rekomendasi']);
    Route::post('/wisata/hitung_saw_ajax', [WisataController::class, 'hitung_saw_ajax']);
});

// --- GRUP KHUSUS ADMIN (Level 1) ---
Route::group(['middleware' => ['auth', 'level:1']], function () {
    Route::get('/admin', [WisataController::class, 'index']);
    Route::resource('wisata', WisataController::class);
    Route::resource('kriteria', KriteriaController::class);
    Route::resource('fasilitas', FasilitasController::class);
    

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/list', [UserController::class, 'list']);
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        Route::post('/store_ajax', [UserController::class, 'store_ajax']);
        

        // Pastikan urutannya seperti ini: {id} dulu baru action-nya
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']);
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
    });
});

// Rute khusus User yang sudah login (Level 2)
Route::group(['middleware' => ['auth', 'level:2']], function () {

    // Halaman Rekomendasi (Pastikan rutenya ada)
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update']);

});
