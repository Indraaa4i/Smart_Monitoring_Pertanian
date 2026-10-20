@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<x-welcome name="Pak Tani" :date="now()->locale('id')->translatedFormat('l, j F Y')" :status="$status" />

@if ($phase)
    <x-phase :label="$phase['label']" :day="$phase['day']" :total="$phase['total']" :tip="$phase['tip']" />
@endif

<section class="metrics">
    @foreach ($cards as $card)
        <x-metric-card :card="$card" />
    @endforeach

    <x-weather-card :weather="$weather" />
</section>

<section class="bottom">
    <x-actions :items="$actions" />
    <x-history :columns="$history['columns']" :rows="$history['rows']" :last="$history['last']" />
</section>
@endsection
