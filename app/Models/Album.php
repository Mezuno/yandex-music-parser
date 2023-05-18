<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;
    protected $table = 'albums';
    protected $guarded = [];

    public function track(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Track::class);
    }

    public function artist(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Artist::class, 'artist_id', 'id');
    }
}
