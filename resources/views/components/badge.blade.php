@props(['label' => 'Aman', 'tone' => 'ok'])
<span @class(['badge', 'badge--'.$tone])><x-icon :name="$tone === 'ok' ? 'check-circle' : 'alert'" :size="13" /> {{ $label }}</span>
