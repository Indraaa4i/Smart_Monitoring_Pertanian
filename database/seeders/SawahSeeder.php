<?php

namespace Database\Seeders;

use App\Models\Petani;
use Illuminate\Database\Seeder;

class SawahSeeder extends Seeder
{
    public function run(): void
    {
        // Akun contoh. Ganti password sebelum dipakai sungguhan.
        $petani = Petani::create(['username' => 'pak_tani', 'password' => 'password123']);

        $petani->lahan()->create([
            'nama_lahan' => 'Sawah Blok A',
            'luas_lahan' => 1.50,
            'lokasi' => 'Indramayu',
            'kode_wilayah' => '32.12.13.2005',
            'tanggal_tanam' => today()->subDays(5),
        ]);
    }
}
