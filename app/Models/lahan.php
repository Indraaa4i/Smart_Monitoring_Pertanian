<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lahan extends Model
{
    use SoftDeletes;

    protected $table = 'lahan';

    protected $fillable = ['petani_id', 'nama_lahan', 'luas_lahan', 'lokasi', 'kode_wilayah', 'tanggal_tanam'];

    protected function casts(): array
    {
        return ['luas_lahan' => 'float', 'tanggal_tanam' => 'date'];
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'petani_id');
    }

    public function sensor(): HasMany
    {
        return $this->hasMany(Sensor::class, 'lahan_id');
    }

    public function dataSensor(): HasMany
    {
        return $this->hasMany(DataSensor::class, 'lahan_id');
    }

    public function rekomendasi(): HasMany
    {
        return $this->hasMany(Rekomendasi::class, 'lahan_id');
    }
}
