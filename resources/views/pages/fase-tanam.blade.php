@extends('layouts.app')

@section(
    'title',
    $tabAktif === 'riwayat'
        ? 'Riwayat Tanam'
        : ($tabAktif === 'jadwal'
            ? 'Jadwal & Aktivitas'
            : ($tabAktif === 'persiapan'
                ? 'Persiapan Lahan'
                : ($tabAktif === 'siklus' ? 'Siklus Tanam' : 'Fase Tanam')))
)

@section('tanpa-catatan', '1')

@section('content')
<header class="halaman-kepala">
    <div>
        @if ($tabAktif === 'siklus')
            <p class="remah">Fase Tanam / Siklus Tanam</p>
            <h1 class="halaman-kepala__judul">Manajemen Siklus Tanam</h1>
            <p class="muted">Kelola dan pantau siklus tanam pada setiap lahan.</p>
        @elseif ($tabAktif === 'riwayat')
            <p class="remah">Fase Tanam / Riwayat Tanam</p>
            <h1 class="halaman-kepala__judul">Riwayat Tanam</h1>
            <p class="muted">Pelajari hasil panen dan evaluasi dari musim sebelumnya.</p>
        @elseif ($tabAktif === 'jadwal')
            <p class="remah">Fase Tanam / Jadwal & Aktivitas</p>
            <h1 class="halaman-kepala__judul">Jadwal & Aktivitas</h1>
            <p class="muted">Atur pekerjaan lapangan dan pengingat agar tidak terlewat.</p>
        @elseif ($tabAktif === 'persiapan')
            <p class="remah">Fase Tanam / Persiapan lahan</p>
            <h1 class="halaman-kepala__judul">Persiapan Lahan</h1>
            <p class="muted">Catat kondisi tanah dan pengolahan sebelum penanaman.</p>
        @else
            <p class="remah">Fase Tanam / Ringkasan</p>
            <h1 class="halaman-kepala__judul">Ringkasan Fase Tanam</h1>
            <p class="muted">Pantau siklus, tahapan, dan pekerjaan sawah dalam satu tempat.</p>
        @endif

        <p class="muted muted--xs">{{ $waktu }}</p>
    </div>

    @php
        $tampilkanLencanaContoh = match ($tabAktif) {
            'riwayat' => $dataRiwayat['contoh'] ?? false,
            'jadwal' => $dataJadwal['contoh'] ?? false,
            'persiapan' => $dataPersiapan['contoh'] ?? false,
            default => $contoh,
        };
    @endphp

    @if ($tampilkanLencanaContoh)
        <span class="lencana-hijau">Data contoh</span>
    @endif
</header>

<x-fase-tab :tab="$tab" />

@if ($tabAktif === 'siklus')
    @include('pages.partials.fase-tanam-siklus')
@elseif ($tabAktif === 'riwayat')
    @include('pages.partials.fase-tanam-riwayat', ['riwayat' => $dataRiwayat])
@elseif ($tabAktif === 'jadwal')
    @include('pages.partials.fase-tanam-jadwal', ['jadwal' => $dataJadwal])
@elseif ($tabAktif === 'persiapan')
    @include('pages.partials.fase-tanam-persiapan', ['dataPersiapan' => $dataPersiapan])
@else
    <x-fase-ringkasan :kartu="$kartu" />
    <x-siklus-aktif :siklus="$siklus" />
    <x-fase-tahapan :tahapan="$tahapan" />
    <x-fase-aktivitas :aktivitas="$aktivitas" />
@endif

@if ($tampilkanLencanaContoh)
    <p class="catatan">Data demonstrasi · Pembaruan {{ $waktu }}. Semua waktu dalam WIB.</p>
@endif
@endsection
