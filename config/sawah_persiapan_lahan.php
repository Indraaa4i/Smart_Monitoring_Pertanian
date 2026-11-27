<?php

/**
 * Data demonstrasi dan pilihan formulir Persiapan Lahan.
 * Catatan yang disimpan pengguna berada di session, bukan di konfigurasi ini.
 */
return [
    'contoh' => true,

    'kondisi' => [
        'Lembap, gembur',
        'Kering',
        'Terlalu basah',
        'Tergenang',
        'Padat',
    ],

    'metode' => [
        'Bajak traktor + pelumpuran',
        'Cangkul + pelumpuran',
        'Traktor roda dua',
        'Pengolahan minimum',
        'Metode lainnya',
    ],

    'riwayat_contoh' => [
        [
            'tanggal' => '2026-10-28',
            'lahan' => 'Blok A',
            'kegiatan' => 'Perataan & pelumpuran',
            'sumber' => 'Manual',
            'hasil' => 'Siap tanam',
            'petugas' => 'Pak Budi',
            'foto' => [],
        ],
        [
            'tanggal' => '2026-10-20',
            'lahan' => 'Blok A',
            'kegiatan' => 'Pemeriksaan awal',
            'sumber' => 'IoT + manual',
            'hasil' => 'pH 6,4 · Air 3 cm',
            'petugas' => 'Pak Budi',
            'foto' => [],
        ],
    ],
];
