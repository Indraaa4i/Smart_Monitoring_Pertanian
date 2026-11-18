@extends('layouts.app')

@section('title', 'Fase Tanam')
@section('tanpa-catatan', '1')

@section('content')
<header class="halaman-kepala">
    <div>
        <p class="remah">Fase Tanam / Ringkasan</p>
        <h1 class="halaman-kepala__judul">Ringkasan Fase Tanam</h1>
        <p class="muted">Pantau siklus, tahapan, dan pekerjaan sawah dalam satu tempat.</p>
        <p class="muted muted--xs">{{ $waktu }}</p>
    </div>
    @if ($contoh)
        <span class="lencana-hijau">Data contoh</span>
    @endif
</header>

<x-fase-tab :tab="$tab" />
<x-fase-ringkasan :kartu="$kartu" />
<x-siklus-aktif :siklus="$siklus" />
<x-fase-tahapan :tahapan="$tahapan" />
<x-fase-aktivitas :aktivitas="$aktivitas" />

@if ($contoh)
    <p class="catatan">Data demonstrasi · Pembaruan sensor {{ $waktu }}. Semua waktu dalam WIB.</p>
@endif
@endsection