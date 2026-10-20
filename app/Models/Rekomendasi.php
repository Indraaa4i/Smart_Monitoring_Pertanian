<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';

    protected $fillable = ['lahan_id', 'status_kelembapan', 'status_ph', 'rekomendasi_pengairan'];

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }
}
