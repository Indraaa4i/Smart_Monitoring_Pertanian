<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Petani extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'petani';

    protected $fillable = ['username', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function lahan(): HasMany
    {
        return $this->hasMany(Lahan::class, 'petani_id');
    }
}
