<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;

class TrackService
{
    public static function storeMany(array $tracks, Artist $artist): Void
    {
        $albums = $artist->album()->get();

        foreach ($tracks as $track) {
            $artist->track()->updateOrCreate([
                'title' => $track['title'],
                'album_id' => $albums->first(function (Album $album) use ($track) {
                    return $album->title === $track['album'];
                })->id,
                'duration' => $track['duration'] ?? '0:00',
            ]);
        }
    }
}
