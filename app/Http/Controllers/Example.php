<?php

namespace App\Http\Controllers;

use App\Http\Requests\YandexMusicParserRequest;
use Illuminate\Http\Request;
use App\Parsers\YandexMusicParser;

class Example extends Controller
{
    public function __invoke(YandexMusicParserRequest $request)
    {
        $data = $request->validated();

        $parser = new YandexMusicParser($data['link']);

        $artistName = $parser->parse();

        if (!$artistName) {
            return redirect()->route('main')->with(['failed' => 'Данные артиста не найдены в Яндекс Музыке']);
        }

        return redirect()->route('main')->with(['success' => 'Данные артиста ' . $artistName . ' успешно добавлены в базу данных']);
    }
}
