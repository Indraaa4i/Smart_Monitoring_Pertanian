@props(['type' => 'info'])
@if ($slot->isEmpty())
    <footer class="notice">
        <x-icon name="info" :size="14" />
        <p>Smart Sawah membantu memantau dan memberi saran. Keputusan dan tindakan di sawah tetap dilakukan oleh petani, tanpa kendali irigasi otomatis.</p>
    </footer>
@else
    <div @class(['notice', 'notice--'.$type])>
        {{ $slot }}
    </div>
@endif
