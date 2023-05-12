<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;
    protected $table = 'tracks';
    protected $guarded = [];

    public function artist()
    {
        return $this->hasOne(Artist::class, 'artist_id', 'id');
    }

    public function album()
    {
        return $this->hasOne(Album::class, 'album_id', 'id');
    }
}
