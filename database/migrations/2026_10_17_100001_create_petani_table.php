<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petani', function (Blueprint $table) {
            $table->id();                              // id_petani
            $table->string('username', 50)->unique();
            $table->string('password');                // hash, bukan teks asli
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petani');
    }
};
