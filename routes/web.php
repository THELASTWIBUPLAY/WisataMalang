<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WisataController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\KriteriaController;


Route::get('/', [WisataController::class, 'landing']);

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