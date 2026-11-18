<?php

/**
 * Data contoh halaman Fase Tanam (sesuai desain Figma 10 Nov 2026).
 * Belum memakai database: tabel `musim_tanam` belum disetujui/dibuat.
 * Saat tabelnya ada, FaseTanamService membaca database dan file ini cukup dihapus.
 */
return [
    'contoh' => true, // true = tampilkan lencana "Data contoh"

    'siklus_aktif' => [
        'kode' => 'MH-2026-A',
        'musim' => 'Musim Hujan 2026',
        'status' => 'Aktif',
        'lahan' => 'Blok A',
        'luas_ha' => 1.2,
        'varietas' => 'Inpari 32',
        'mulai_persiapan' => '2026-10-20',
        'tanggal_tanam' => '2026-11-04',
        'perkiraan_panen' => '2027-02-22',
        'penanggung_jawab' => 'Pak Budi',
        'catatan' => 'Periksa pematang dan tinggi air secara berkala.',

        // 'rute' = nama route halaman detail tahap. Selama route-nya belum ada, tombolnya nonaktif.
        // 'tombol_modal' = tampil sebagai tombol di modal Detail siklus.
        'tahap' => [
            ['nama' => 'Persiapan lahan', 'status' => 'selesai', 'rute' => 'fase-tanam.persiapan-lahan', 'tombol_modal' => true],
            ['nama' => 'Persemaian bibit', 'status' => 'selesai', 'rute' => 'fase-tanam.persemaian-bibit', 'tombol_modal' => true],
            ['nama' => 'Penanaman', 'status' => 'selesai', 'rute' => 'fase-tanam.penanaman', 'tombol_modal' => true],
            ['nama' => 'Pemeliharaan', 'status' => 'berlangsung', 'rute' => 'fase-tanam.pemeliharaan', 'tombol_modal' => true],
            ['nama' => 'Panen & evaluasi', 'status' => 'rencana', 'rute' => null, 'tombol_modal' => false],
        ],

        'rute_catat_pemeliharaan' => 'fase-tanam.pemeliharaan.baru',
    ],
];