<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    protected $table = 'artists';
    protected $guarded = [];

    public function album(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function track(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Track::class);
    }
}
