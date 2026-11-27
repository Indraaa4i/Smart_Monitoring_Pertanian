<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaseTanamController;
use App\Http\Controllers\KondisiTanahController;
use App\Http\Controllers\RekomendasiController;
use Illuminate\Support\Facades\Route;

// Beranda
Route::get('/', DashboardController::class)->name('beranda');

// Kondisi Tanah
Route::get('/kondisi-tanah', KondisiTanahController::class)
    ->name('kondisi-tanah');

// Fase Tanam
Route::get('/fase-tanam', FaseTanamController::class)
    ->name('fase-tanam');

// Fase Tanam > Persiapan Lahan
Route::post('/fase-tanam/persiapan-lahan', [
    FaseTanamController::class,
    'storePersiapanLahan',
])->name('fase-tanam.persiapan.store');

// Fase Tanam > Penanaman
Route::get('/fase-tanam/penanaman', [FaseTanamController::class, 'penanaman'])
    ->name('fase-tanam.penanaman');

// Fase Tanam > Persemaian Bibit
Route::get('/fase-tanam/persemaian-bibit', [FaseTanamController::class, 'persemaian'])
    ->name('fase-tanam.persemaian');

Route::post('/fase-tanam/persemaian-bibit', [FaseTanamController::class, 'storePersemaian'])
    ->name('fase-tanam.persemaian.store');

// Fase Tanam > Pemeliharaan
Route::get('/fase-tanam/pemeliharaan', [FaseTanamController::class, 'pemeliharaan'])
    ->name('fase-tanam.pemeliharaan');

// Fase Tanam > Jadwal & Aktivitas
Route::post('/fase-tanam/aktivitas', [FaseTanamController::class, 'storeAktivitas'])
    ->name('fase-tanam.aktivitas.store');

Route::post('/fase-tanam/aktivitas/{aktivitas}/selesai', [
    FaseTanamController::class,
    'selesaikanAktivitas',
])->name('fase-tanam.aktivitas.selesai');

// Rekomendasi
Route::get('/rekomendasi', RekomendasiController::class)
    ->name('rekomendasi');

// Riwayat
Route::view('/riwayat', 'pages.Riwayat', [
    'title' => 'Riwayat',
])->name('riwayat');
