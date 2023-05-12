<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>YandexMusicParser</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body class="container d-flex justify-content-center">

    <form class="mt-5 p-5 w-100" action="{{ route('parse') }}" method="post">
        @csrf
        @method('post')

        @if(!empty(session()->get('success')))
            <div class="alert alert-success">
                {{ session()->get('success')  }}
            </div>
        @endif

        @if(!empty(session()->get('failed')))
            <div class="alert alert-danger">
                {{ session()->get('failed')  }}
            </div>
        @endif

        @if ($errors->has('link'))
            <div class="alert alert-danger">
                <ul>@foreach($errors->get('link') as $message)<li>{{$message}}</li>@endforeach</ul>
            </div>
        @endif

        <input type="text" name="link" class="form-control input-lg" placeholder="Ссылка типа https://music.yandex.ru/artist/36800/tracks">
        <button class="btn btn-dark btn-sm mt-2">Парсить</button>
    </form>

</body>
</html>
