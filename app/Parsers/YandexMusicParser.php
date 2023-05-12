<?php

namespace App\Parsers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class YandexMusicParser
{
    private array $artist;
    private array $artistNodes;
    private array $tracks;
    private array $albums;

    public function __construct(
        public string $link
    )
    {
    }


    /**
     * Main parse function that invoke all other functions.
     *
     * Returns false if artist doesn`t exists, and artist name if success parse.
     *
     * @return mixed
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws NotLoadedException
     * @throws StrictException
     */
    public function parse() : mixed
    {
        $html = $this->getPageHtmlCurl();

        $dom = $this->getDom($html);

        $this->setArtistNodes($dom);

        if (!$this->checkArtistExists()) {
            return false;
        }

        $this->setArtist();

        $artist = $this->storeArtist();

        $this->setTraksAndAlbums($dom, $artist->id);

        $this->storeAlbums();

        $tracks = $this->prepareTracksDataToStore($artist->id);

        $this->storeTracks($tracks);

        return $artist->title;
    }

    /**
     * Get page html with curl.
     *
     * @return bool|string
     */
    private function getPageHtmlCurl(): bool|string
    {
        $curlHandle = curl_init($this->link);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER , 1);
        $response = curl_exec($curlHandle);
        curl_close($curlHandle);
        return $response;
    }

    /**
     * @param $html
     * @return Dom
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     */
    private function getDom($html): Dom
    {
        $dom = new Dom;
        return $dom->loadStr($html);
    }

    /**
     * Method find artist data in $dom.
     *
     * @param Dom $dom
     * @return Void
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     */
    private function setArtistNodes(Dom $dom) : Void
    {
        $this->artistNodes['title'] = $dom->find('.page-artist__title', 0);
        $this->artistNodes['monthListeners'] = $dom->find('.deco-typo-secondary > span', 0);
        $this->artistNodes['followers'] = $dom->find('span.d-like_theme-count > button > span > span > span.d-button__label', 0);
    }

    /**
     * Check if artists exists.
     *
     * @return bool
     */
    private function checkArtistExists()
    {
        return $this->artistNodes['title'] !== null;
    }

    /**
     * Method sets artist data to $this->artist field from $artistNodes.
     *
     * @return bool
     */
    private function setArtist() : Bool
    {
        if (empty($this->artistNodes['title'])) {
            return false;
        }

        $this->artist['title'] = $this->artistNodes['title']->text;

        if (!empty($this->artistNodes['monthListeners'])) {
            $this->artist['monthListeners'] = $this->artistNodes['monthListeners']->text;
        }

        if (!empty($this->artistNodes['followers'])) {
            $this->artist['followers'] = $this->artistNodes['followers']->text;
        }

        $this->artist['monthListeners'] = str_replace(' ', '', $this->artist['monthListeners'] ?? 0);
        $this->artist['followers'] = str_replace(' ', '', $this->artist['followers'] ?? 0);

        return true;
    }

    /**
     * Method stores current artist data to database.
     *
     * @return Artist
     */
    private function storeArtist(): Artist
    {
        return Artist::firstOrCreate(
            ['title' => $this->artist['title']],
            [
                'title' => $this->artist['title'],
                'month_listeners' => (int)$this->artist['monthListeners'],
                'followers' => (int)$this->artist['followers']
            ]
        );
    }

    /**
     * Method find tracks`s data in $dom and sets it to $this->tracks and $this->albums.
     *
     * @param $dom
     * @return Void
     */
    private function setTraksAndAlbums(Dom $dom, Int $artistId) : Void
    {
        $count = $dom->find('div.d-track')->count();

        for ($i = 0; $i < $count; $i++) {

            foreach ($dom->find('div.d-track')[$i]->find('div.d-track__overflowable-column > div.d-track__overflowable-wrapper > div.d-track__meta > a') as $trackAlbum) {
                $this->albums[$i]['title'] = $trackAlbum->title;
                $this->albums[$i]['artist_id'] = $artistId;
            }

            foreach ($dom->find('div.d-track')[$i]->find('div.d-track__quasistatic-column > div.d-track__name') as $track) {
                $this->tracks[$i]['title'] = $track->title;
                $this->tracks[$i]['artist_id'] = $artistId;
            }

            foreach ($dom->find('div.d-track')[$i]->find('div.d-track__overflowable-column > div.d-track__end-column > div.d-track__info > span') as $trackDuration) {
                $this->tracks[$i]['duration'] = str_replace(' ', '', $trackDuration->text);
            }

        }
    }

    /**
     * Stores albums data to database.
     *
     * @return Void
     */
    private function storeAlbums() : Void
    {
        foreach ($this->albums as $key => $album) {
            $this->albums[$key]['id'] = Album::firstOrCreate($album, $album)->id;
        }
    }

    /**
     * Prepare tracks data to store.
     *
     * @return array
     */
    private function prepareTracksDataToStore(Int $artistId): array
    {
        $tracks = [];

        for ($i = 0; $i < count($this->tracks); $i++) {
            $tracks[] = [
                'title' => $this->tracks[$i]['title'],
                'artist_id' => $artistId,
                'album_id' => $this->albums[$i]['id'],
                'duration' => $this->tracks[$i]['duration'] ?? '0:00',
            ];
        }

        return $tracks;
    }

    /**
     * Stores tracks data to database.
     *
     * @param array $tracks
     * @return void
     */
    private function storeTracks(array $tracks)
    {
        foreach ($tracks as $track) {
            Track::firstOrCreate($track, $track);
        }
    }
}
