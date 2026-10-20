<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('beranda');

// Halaman lain belum ada di desain: sementara memakai placeholder.
Route::view('/kondisi-tanah', 'pages.placeholder', ['title' => 'Kondisi Tanah'])->name('kondisi-tanah');
Route::view('/rekomendasi', 'pages.placeholder', ['title' => 'Rekomendasi'])->name('rekomendasi');
Route::view('/riwayat', 'pages.placeholder', ['title' => 'Riwayat'])->name('riwayat');
