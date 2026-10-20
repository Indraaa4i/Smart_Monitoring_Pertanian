<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataSensor extends Model
{
    protected $table = 'data_sensor';

    public $timestamps = false;

    protected $fillable = ['lahan_id', 'sensor_id', 'kelembapan', 'ph', 'waktu'];

    protected function casts(): array
    {
        return ['kelembapan' => 'float', 'ph' => 'float', 'waktu' => 'datetime'];
    }

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }

    public function sensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class, 'sensor_id');
    }
}
