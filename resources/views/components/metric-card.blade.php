@props(['card'])
@php
    $pos = fn ($v) => max(0, min(100, ($v - $card['min']) / ($card['max'] - $card['min']) * 100));
@endphp
<article class="card metric">
    <header class="metric__head">
        <h2 class="metric__title"><x-icon :name="$card['icon']" :size="17" /> {{ $card['title'] }}</h2>
        <x-badge :label="$card['status']" :tone="$card['tone']" />
    </header>
    <p class="metric__value">{{ $card['value'] }}@if ($card['unit'])<span>{{ $card['unit'] }}</span>@endif</p>
    <p class="metric__headline">{{ $card['headline'] }}</p>
    <p class="muted">{{ $card['description'] }}</p>
    <div class="gauge">
        <div class="gauge__track" role="img" aria-label="{{ $card['description'] }}">
            <span class="gauge__safe" style="left: {{ $pos($card['safeMin']) }}%; width: {{ $pos($card['safeMax']) - $pos($card['safeMin']) }}%"></span>
            @if ($card['current'] !== null)
                <span class="gauge__marker" style="left: {{ $pos($card['current']) }}%"></span>
            @endif
        </div>
        <div class="gauge__labels"><span>{{ $card['minLabel'] }}</span><strong>{{ $card['rangeLabel'] }}</strong><span>{{ $card['maxLabel'] }}</span></div>
    </div>
    <p class="metric__hint"><x-icon name="arrow-right" :size="14" /> {{ $card['hint'] }}</p>
</article>
