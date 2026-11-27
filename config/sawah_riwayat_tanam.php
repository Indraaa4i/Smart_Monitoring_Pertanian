<?php

/**
 * Data demonstrasi arsip hasil panen untuk tab Riwayat Tanam.
 * Belum terhubung ke database atau tabel musim_tanam.
 * Saat sumber data permanen disetujui, isi ini dapat diganti oleh query Service.
 */
return [
    'contoh' => true,
    'sumber_hasil' => 'Gabah kering panen (GKP)',

    'siklus' => [
        [
            'kode' => 'MK-2026-A',
            'musim' => 'Musim Kemarau 2026',
            'lahan' => 'Blok A',
            'varietas' => 'Inpari 32',
            'luas_ha' => 1.2,
            'tanggal_mulai' => '2026-05-10',
            'tanggal_panen' => '2026-09-25',
            'tahun' => 2026,
            'hasil_panen_ton' => 7.44,
            'produktivitas_ton_ha' => 6.2,
            'status' => 'Selesai',
            'evaluasi' => [
                'penanggung_jawab' => 'Pak Budi',
                'kendala' => 'Pasokan air berkurang selama 5 hari pada fase pembungaan. Gulma muncul di sisi timur lahan.',
                'saran' => 'Perbaiki saluran masuk dan jadwalkan pengairan bergiliran. Lakukan penyiangan lebih awal pada musim berikutnya.',
            ],
        ],
        [
            'kode' => 'MK-2026-B',
            'musim' => 'Musim Kemarau 2026',
            'lahan' => 'Blok B',
            'varietas' => 'Ciherang',
            'luas_ha' => 0.8,
            'tanggal_mulai' => '2026-05-15',
            'tanggal_panen' => '2026-09-30',
            'tahun' => 2026,
            'hasil_panen_ton' => 4.96,
            'produktivitas_ton_ha' => 6.2,
            'status' => 'Selesai',
            'evaluasi' => null,
        ],
        [
            'kode' => 'MK-2026-C',
            'musim' => 'Musim Kemarau 2026',
            'lahan' => 'Blok C',
            'varietas' => 'Inpari 42',
            'luas_ha' => 0.5,
            'tanggal_mulai' => '2026-05-20',
            'tanggal_panen' => '2026-10-05',
            'tahun' => 2026,
            'hasil_panen_ton' => 2.90,
            'produktivitas_ton_ha' => 5.8,
            'status' => 'Selesai',
            'evaluasi' => null,
        ],
    ],
];
