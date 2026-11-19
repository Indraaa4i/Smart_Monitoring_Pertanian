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
    
    'daftar_siklus' => [
        [
            'id' => 'MH-2026-A',
            'nama' => 'MH-2026-A',
            'lahan' => 'Blok A',
            'varietas' => 'Inpari 32',
            'luas_ha' => 1.2,
            'mulai' => '2026-10-20',
            'akhir' => '2027-02-22',
            'tahap' => 'Pemeliharaan',
            'status' => 'Aktif',
            'musim' => 'Musim Hujan 2026',
            'catatan' => 'Siklus utama Blok A.',
        ],
        [
            'id' => 'MH-2026-B',
            'nama' => 'MH-2026-B',
            'lahan' => 'Blok B',
            'varietas' => 'Ciherang',
            'luas_ha' => 0.8,
            'mulai' => '2026-10-25',
            'akhir' => '2027-03-11',
            'tahap' => 'Persemaian',
            'status' => 'Aktif',
            'musim' => 'Musim Hujan 2026',
            'catatan' => 'Siklus Blok B.',
        ],
        [
            'id' => 'MH-2026-C',
            'nama' => 'MH-2026-C',
            'lahan' => 'Blok C',
            'varietas' => 'Inpari 42',
            'luas_ha' => 0.5,
            'mulai' => '2026-11-15',
            'akhir' => '2027-03-20',
            'tahap' => 'Persiapan',
            'status' => 'Rencana',
            'musim' => 'Musim Hujan 2026',
            'catatan' => 'Jadwal pengolahan belum ditetapkan.',
        ],
    ],

];