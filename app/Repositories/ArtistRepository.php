<?php

namespace App\Repositories;

use App\Models\Artist;

class ArtistRepository
{
    /**
     * Method stores current artist data to database.
     *
     * @param array $data
     * @return Artist
     */
    public static function store(array $data): Artist
    {
        return Artist::updateOrCreate(
            ['title' => $data['title']],
            [
                'title' => $data['title'],
                'month_listeners' => (int)$data['month_listeners'],
                'followers' => (int)$data['followers']
            ]
        );
    }
}
