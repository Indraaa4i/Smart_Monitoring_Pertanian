@props(['name', 'date', 'status'])
<section class="welcome">
    <div>
        <h1 class="welcome__title">Selamat datang, {{ $name }}</h1>
        <p class="welcome__sub">{{ $date }} · Pantau sawah hari ini</p>
    </div>
    <span @class(['welcome__status', 'welcome__status--'.$status->tone()])>
        <x-icon :name="$status->tone() === 'ok' ? 'shield' : 'alert'" :size="15" /> {{ $status->plotLabel() }}
    </span>
</section>
