<?php

return [
    'interval_minutes' => 30,        // interval normal laporan sensor (dipakai untuk mengelompokkan riwayat)
    'offline_after_minutes' => 90,   // tanpa data lebih lama dari ini = sensor offline
    'history_rows' => 3,
    'weather' => [
        'base_url' => 'https://api.bmkg.go.id/publik',
        'timeout' => 5,          // detik
        'cache_minutes' => 30,
    ],

    'limit_label' => 'batas contoh', // ganti ke 'batas' bila batas sudah baku

    'offline' => [
        'headline' => 'Data belum masuk',
        'description' => 'Sensor belum mengirim data terbaru.',
        'hint' => 'Periksa perangkat sensor di sawah.',
    ],

    // Fase tanam berurutan, dihitung dari tanggal tanam petak.
    'phases' => [
        ['label' => 'Fase awal tanam', 'days' => 15, 'tip' => 'Pastikan tanah tetap lembap dan periksa genangan di petak.'],
    ],

    // Parameter ukur. Kunci = nama kolom di tabel data_sensor.
    // Menambah parameter baru = tambah kolom di data_sensor + satu entri di sini.
    'sensors' => [
        'kelembapan' => [
            'label' => 'Kelembapan tanah', 'short' => 'Kelembapan', 'icon' => 'droplet',
            'unit' => '%', 'suffix' => '%', 'decimals' => 0,
            'scale' => ['min' => 0, 'max' => 100], 'safe' => ['min' => 60, 'max' => 80],
            'messages' => [
                'ok' => ['headline' => 'Tanah cukup lembap', 'hint' => 'Periksa tanah sebelum menambah air.'],
                'low' => ['headline' => 'Tanah mulai kering', 'hint' => 'Periksa tanah; pertimbangkan menambah air.'],
                'high' => ['headline' => 'Tanah terlalu basah', 'hint' => 'Periksa genangan dan saluran air di petak.'],
            ],
        ],
        'ph' => [
            'label' => 'pH tanah', 'short' => 'pH', 'icon' => 'flask',
            'unit' => 'pH', 'suffix' => '', 'decimals' => 1,
            'scale' => ['min' => 0, 'max' => 14], 'safe' => ['min' => 5.5, 'max' => 7.0],
            'messages' => [
                'ok' => ['headline' => 'Keasaman tanah sesuai batas', 'hint' => 'Pantau pH; belum perlu perubahan.'],
                'low' => ['headline' => 'Tanah cenderung asam', 'hint' => 'Periksa ulang pH di beberapa titik petak.'],
                'high' => ['headline' => 'Tanah cenderung basa', 'hint' => 'Periksa ulang pH di beberapa titik petak.'],
            ],
        ],
    ],

    // Saran sesuai status keseluruhan sawah.
    'actions' => [
        'aman' => [
            ['icon' => 'footprints', 'title' => 'Periksa kondisi sawah', 'text' => 'Tanah masih cukup lembap. Cek langsung sebelum menambah air.'],
            ['icon' => 'clock', 'title' => 'Pantau kembali sore ini', 'text' => 'Cek kelembapan dan pH setelah hujan atau saat kondisi berubah.'],
        ],
        'waspada' => [
            ['icon' => 'footprints', 'title' => 'Periksa sawah sekarang', 'text' => 'Ada pembacaan di luar batas. Pastikan kondisi sebenarnya di petak.'],
            ['icon' => 'clock', 'title' => 'Pantau lagi dalam 30 menit', 'text' => 'Lihat apakah nilai kembali ke batas.'],
        ],
        'offline' => [
            ['icon' => 'alert', 'title' => 'Periksa sensor', 'text' => 'Data belum masuk. Pastikan perangkat menyala dan terhubung.'],
            ['icon' => 'footprints', 'title' => 'Cek sawah langsung', 'text' => 'Sambil menunggu data, periksa kondisi sawah secara manual.'],
        ],
    ],
];
