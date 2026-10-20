@props(['columns', 'rows', 'last' => null])
<section class="card history">
    <header class="card__head">
        <h2 class="card__title">Pemantauan terbaru</h2>
        <a href="{{ route('riwayat') }}" class="link">Lihat riwayat <x-icon name="arrow-up-right" :size="12" /></a>
    </header>
    <p class="muted">Data terakhir{{ $last ? ' · '.$last.' WIB' : '' }}</p>
    <div class="table-scroll">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    @foreach ($columns as $col)<th>{{ $col }}</th>@endforeach
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td class="muted">{{ $row['time'] }}</td>
                        @foreach ($row['values'] as $v)<td><strong>{{ $v }}</strong></td>@endforeach
                        <td>
                            <span @class(['status', 'status--'.$row['tone']])>
                                <x-icon :name="$row['tone'] === 'ok' ? 'check-circle' : 'alert'" :size="13" /> {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($columns) + 2 }}" class="muted">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
