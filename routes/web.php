<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('beranda');

// Halaman lain:
Route::view('/kondisi-tanah', 'pages.placeholder', ['title' => 'Kondisi Tanah'])->name('kondisi-tanah');
Route::view('/rekomendasi', 'pages.placeholder', ['title' => 'Rekomendasi'])->name('rekomendasi');


Route::view('/riwayat', 'pages.Riwayat', ['title' => 'Riwayat'])->name('riwayat');