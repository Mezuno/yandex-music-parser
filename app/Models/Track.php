<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;
    protected $table = 'tracks';
    protected $guarded = [];

    public function artist(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Artist::class, 'artist_id', 'id');
    }

    public function album(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Album::class, 'album_id', 'id');
    }
}
