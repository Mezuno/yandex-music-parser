<?php

namespace App\Parsers;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

final class YandexMusicParser
{
    private static array $artist;
    private static array $tracks;
    private static array $albums;

    const SEARCH_NODES = [
        'ARTIST_TITLE' => '.page-artist__title',
        'ARTIST_MONTH_LISTENERS' => '.deco-typo-secondary > span',
        'ARTIST_FOLLOWERS' => 'span.d-like_theme-count > button > span > span > span.d-button__label',
        'TRACK_CARD' => 'div.d-track',
        'TRACK_ALBUM' => 'div.d-track__overflowable-column > div.d-track__overflowable-wrapper > div.d-track__meta > a',
        'TRACK_TITLE' => 'div.d-track__quasistatic-column > div.d-track__name',
        'TRACK_DURATION' => 'div.d-track__overflowable-column > div.d-track__end-column > div.d-track__info > span',
    ];

    /**
     * Main parse function that invoke all other functions.
     *
     * Returns artist name if success parse and null if artist not found.
     *
     * @param string $link
     * @return ?array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     */
    public static function parseArtistPage(string $link) : ?array
    {
        $response = self::getArtistPageCurl($link);

        if ($response['info']['http_code'] === 404) {
            return null;
        }

        $dom = self::getDom($response['html']);

        self::getArtistFromDom($dom);
        self::getTracksAndAlbumsFromDom($dom);

        return self::compactArtistData();
    }

    /**
     * Get an artist page from a link using curl.
     *
     * @param string $link
     * @return array
     */
    private static function getArtistPageCurl(string $link) : array
    {
        $curlHandle = curl_init($link);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER , 1);
        $html = curl_exec($curlHandle);
        $info = curl_getinfo($curlHandle);
        curl_close($curlHandle);
        return ['html' => $html, 'info' => $info];
    }

    /**
     * Get a PHPHtmlParser\Dom object from passed html.
     *
     * @param string $html
     * @return Dom
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     */
    private static function getDom(string $html) : Dom
    {
        $dom = new Dom;
        return $dom->loadStr($html);
    }

    /**
     * Method sets artist data to self::$artist field from $artistNodes.
     *
     * @param Dom $dom
     * @return Void
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     */
    private static function getArtistFromDom(Dom $dom) : Void
    {
        self::$artist['title'] = $dom->find(self::SEARCH_NODES['ARTIST_TITLE'], 0)->text;

        self::$artist['month_listeners'] = (int)str_replace(
            ' ', '',
            $dom->find(self::SEARCH_NODES['ARTIST_MONTH_LISTENERS'], 0)->text ?? ''
        ) ?? 0;

        self::$artist['followers'] = (int)str_replace(
            ' ', '',
            $dom->find(self::SEARCH_NODES['ARTIST_FOLLOWERS'], 0)->text ?? ''
        ) ?? 0;
    }

    /**
     * Method find tracks`s data in $dom and sets it to self::$tracks and self::$albums.
     *
     * @param Dom $dom
     * @return Void
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     */
    private static function getTracksAndAlbumsFromDom(Dom $dom) : Void
    {
        $count = $dom->find(self::SEARCH_NODES['TRACK_CARD'])->count();

        for ($i = 0; $i < $count; $i++) {

            foreach (
                $dom->find(self::SEARCH_NODES['TRACK_CARD'])[$i]
                    ->find(self::SEARCH_NODES['TRACK_ALBUM'])
                     as $trackAlbum) {
                self::$albums[$i]['title'] = $trackAlbum->title;
            }

            foreach (
                $dom->find(self::SEARCH_NODES['TRACK_CARD'])[$i]
                    ->find(self::SEARCH_NODES['TRACK_TITLE'])
                    as $track) {
                self::$tracks[$i]['title'] = $track->title;
                self::$tracks[$i]['album'] = self::$albums[$i]['title'];
            }

            foreach (
                $dom->find(self::SEARCH_NODES['TRACK_CARD'])[$i]
                    ->find(self::SEARCH_NODES['TRACK_DURATION'])
                     as $trackDuration) {
                self::$tracks[$i]['duration'] = str_replace(' ', '', $trackDuration->text);
            }

        }

        self::$albums = array_unique(self::$albums, SORT_REGULAR);
    }

    /**
     * Compact artist data contains in class for return.
     *
     * @return array
     */
    private static function compactArtistData(): array
    {
        return [
            'artist' => self::$artist,
            'albums' => self::$albums,
            'tracks' => self::$tracks,
        ];
    }
}
