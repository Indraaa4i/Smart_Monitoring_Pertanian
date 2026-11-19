@extends('layouts.app')

@section('title', 'Fase Tanam')
@section('tanpa-catatan', '1')

@section('content')
<header class="halaman-kepala">
    <div>
        @if ($tabAktif === 'siklus')
            <p class="remah">Fase Tanam / Siklus Tanam</p>
            <h1 class="halaman-kepala__judul">Manajemen Siklus Tanam</h1>
            <p class="muted">Kelola dan pantau siklus tanam pada setiap lahan.</p>
        @else
            <p class="remah">Fase Tanam / Ringkasan</p>
            <h1 class="halaman-kepala__judul">Ringkasan Fase Tanam</h1>
            <p class="muted">Pantau siklus, tahapan, dan pekerjaan sawah dalam satu tempat.</p>
        @endif

        <p class="muted muted--xs">{{ $waktu }}</p>
    </div>

    @if ($contoh)
        <span class="lencana-hijau">Data contoh</span>
    @endif
</header>


<x-fase-tab :tab="$tab" />

@if ($tabAktif === 'siklus')
    @include('pages.partials.fase-tanam-siklus')
@else
    <x-fase-ringkasan :kartu="$kartu" />
    <x-siklus-aktif :siklus="$siklus" />
    <x-fase-tahapan :tahapan="$tahapan" />
    <x-fase-aktivitas :aktivitas="$aktivitas" />
@endif


@if ($contoh)
    <p class="catatan">Data demonstrasi · Pembaruan sensor {{ $waktu }}. Semua waktu dalam WIB.</p>
@endif
@endsection