<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KondisiTanahController;
use App\Http\Controllers\RekomendasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('beranda');

// Halaman lain:
Route::get('/kondisi-tanah', KondisiTanahController::class)->name('kondisi-tanah');
Route::get('/rekomendasi', RekomendasiController::class)->name('rekomendasi');


Route::view('/riwayat', 'pages.Riwayat', ['title' => 'Riwayat'])->name('riwayat');