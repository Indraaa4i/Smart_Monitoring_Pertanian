@props(['status' => 'offline', 'label'])

<span {{ $attributes->class(['status-pill', 'status-pill--'.$status]) }}>{{ $label }}</span>