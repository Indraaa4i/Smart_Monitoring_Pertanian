<?php

/**
 * Data demonstrasi Jadwal & Aktivitas.
 *
 * offset_hari dihitung dari tanggal saat data contoh pertama kali dibuat
 * untuk session pengguna. Data yang dibuat pengguna disimpan di session.
 */
return [
    'contoh' => true,

    'aktivitas_awal' => [
        [
            'kegiatan' => 'Periksa tinggi air',
            'offset_hari' => 0,
            'waktu' => '07:00',
            'prioritas' => 'normal',
            'pengingat' => '30',
            'status' => 'selesai',
            'catatan' => 'Pemeriksaan rutin kondisi air pada lahan.',
        ],
        [
            'kegiatan' => 'Pemeriksaan hama',
            'offset_hari' => 0,
            'waktu' => '15:00',
            'prioritas' => 'tinggi',
            'pengingat' => '30',
            'status' => 'menunggu',
            'catatan' => 'Periksa gejala serangan hama pada tanaman.',
        ],
        [
            'kegiatan' => 'Periksa bibit',
            'offset_hari' => 0,
            'waktu' => '16:00',
            'prioritas' => 'normal',
            'pengingat' => '60',
            'status' => 'menunggu',
            'catatan' => 'Amati kondisi bibit dan pertumbuhannya.',
        ],
        [
            'kegiatan' => 'Pemupukan susulan I',
            'offset_hari' => 1,
            'waktu' => '08:00',
            'prioritas' => 'normal',
            'pengingat' => '30',
            'status' => 'menunggu',
            'catatan' => 'Sesuaikan pelaksanaan dengan kondisi tanaman.',
        ],
        [
            'kegiatan' => 'Pindah tanam',
            'offset_hari' => 11,
            'waktu' => '07:00',
            'prioritas' => 'tinggi',
            'pengingat' => '60',
            'status' => 'menunggu',
            'catatan' => 'Periksa kesiapan bibit dan lahan sebelum kegiatan.',
        ],
    ],
];
