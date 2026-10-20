<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lahan', function (Blueprint $table) {
            $table->id();                              // id_lahan
            $table->foreignId('petani_id')->constrained('petani')->restrictOnDelete();
            $table->string('nama_lahan', 100);         // tambahan: nama tampil di dashboard
            $table->decimal('luas_lahan', 8, 2)->nullable(); // hektare (di ERD: varchar)
            $table->string('lokasi')->nullable();
            $table->string('kode_wilayah', 20)->nullable();  // tambahan: kode adm4 BMKG
            $table->date('tanggal_tanam')->nullable();       // tambahan: dasar hitungan "hari ke-N"
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['petani_id', 'nama_lahan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lahan');
    }
};
