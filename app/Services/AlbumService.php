<?php

namespace App\Services;

use App\Models\Artist;

class AlbumService
{
    public static function storeMany(array $albums, Artist $artist): Void
    {
        foreach ($albums as $album) {
            $artist->album()->updateOrCreate($album);
        }
    }
}
