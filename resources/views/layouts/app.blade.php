<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Beranda') · Smart Sawah</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/smart-sawah.css', 'resources/js/smart-sawah.js'])
</head>
<body>
    <div class="shell">
        <div class="shell__inner">
            <x-sidebar />
            <main class="main">
                <x-topbar :daftar-lahan="$daftarLahan ?? collect()" :lahan="$lahan ?? null" :phase="$phase ?? null" />
                @yield('content')
                <x-notice />
            </main>
        </div>
    </div>
</body>
</html>
