
<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KondisiTanahController;
use App\Http\Controllers\RekomendasiController;
use Illuminate\Support\Facades\Route;

// Beranda
Route::get('/', DashboardController::class)->name('beranda');

// Kondisi Tanah
Route::get('/kondisi-tanah', KondisiTanahController::class)
    ->name('kondisi-tanah');

// Fase Tanam (Placeholder)
Route::view('/fase-tanam', 'pages.FaseTanam')
    ->name('fase-tanam');

// Rekomendasi
Route::get('/rekomendasi', RekomendasiController::class)
    ->name('rekomendasi');

// Riwayat
Route::view('/riwayat', 'pages.Riwayat', [
    'title' => 'Riwayat'
])->name('riwayat');
