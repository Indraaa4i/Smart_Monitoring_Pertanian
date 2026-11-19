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

// Fase Tanam > Penanaman
Route::get('/fase-tanam/penanaman', [FaseTanamController::class, 'penanaman'])
    ->name('fase-tanam.penanaman');

    Route::get('/fase-tanam/persemaian-bibit', [FaseTanamController::class, 'persemaian'])
    ->name('fase-tanam.persemaian');
    
// Rekomendasi
Route::get('/rekomendasi', RekomendasiController::class)
    ->name('rekomendasi');

// Riwayat
Route::view('/riwayat', 'pages.Riwayat', [
    'title' => 'Riwayat'
])->name('riwayat');