<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sensor extends Model
{
    protected $table = 'sensor';

    protected $fillable = ['lahan_id', 'nama_sensor'];

    public function lahan(): BelongsTo
    {
        return $this->belongsTo(Lahan::class, 'lahan_id');
    }

    public function dataSensor(): HasMany
    {
        return $this->hasMany(DataSensor::class, 'sensor_id');
    }
}
