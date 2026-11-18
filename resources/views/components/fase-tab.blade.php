@props(['tab'])
<nav class="fase-tab" aria-label="Bagian Fase Tanam">
    @foreach ($tab as $item)
        @if (! empty($item['aktif']))
            <a href="{{ $item['url'] }}" class="fase-tab__item is-active" aria-current="page">{{ $item['label'] }}</a>
        @elseif (! empty($item['url']))
            <a href="{{ $item['url'] }}" class="fase-tab__item">{{ $item['label'] }}</a>
        @else
            <button type="button" class="fase-tab__item" aria-disabled="true" title="Segera hadir">{{ $item['label'] }}</button>
        @endif
    @endforeach
</nav>