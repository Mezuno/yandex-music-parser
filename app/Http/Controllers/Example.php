<?php

namespace App\Http\Controllers;

use App\Http\Requests\YandexMusicParserRequest;
use App\Repositories\ArtistRepository;
use App\Parsers\YandexMusicParser;
use App\Services\AlbumService;
use App\Services\TrackService;
use Illuminate\Http\RedirectResponse;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class Example extends Controller
{
    /**
     * Controller for example YandexMusicParser usage.
     *
     * @param YandexMusicParserRequest $request
     *
     * @return RedirectResponse
     *
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public function __invoke(YandexMusicParserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $artistData = YandexMusicParser::parseArtistPage($data['link']);

        if (empty($artistData)) {
            return redirect()->back()->with(['failed' => 'Данные артиста не найдены в Яндекс Музыке']);
        }

        $artist = ArtistRepository::store($artistData['artist']);
        AlbumService::storeMany($artistData['albums'], $artist);
        TrackService::storeMany($artistData['tracks'], $artist);

        return redirect()->back()->with(['success' => 'Данные артиста ' . $artist->title . ' успешно добавлены в базу данных']);
    }
}
