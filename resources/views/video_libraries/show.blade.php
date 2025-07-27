@extends('app', [
'title' => 'Профиль пользователя',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])
@section('content')

<style>

    .p-3 {padding: 0px;}
    .mb-3{margin-bottom:0px;}
</style>
    <div class="container mt-5">
        <h3 class="mb-3">Videos</h3>
        @if($videos->isEmpty())
            <p>No videos found for this library.</p>
        @else
            <ul class="list-group">
                @foreach($videos as $video)
                    <h1 class="mb-4">{{ $video->title }}</h1>

                    <li class="list-group-item mb-3 p-3 border rounded shadow-sm">
                        <div class="mb-3">
{{--                            <strong>Google URL:</strong> {{ $video->google_url ?? 'N/A' }}--}}
                            @if($video->google_url)
                                <div class="mt-2">
                                    <iframe data-url="{{ $video->google_url }}" class="google-iframe rounded"  style="max-width:100%;" width="560" height="316" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
{{--                            <strong>Yandex URL:</strong> {{ $video->yandex_url ?? 'N/A' }}--}}
                            @if($video->yandex_url)
                                <yaplayertag id="ya-video-player-64669673148962f0e794b16664b5ab7643031c912713xWEBx2076x1730919877" style="width: 100%; height: 100%; position: relative; overflow: hidden; display: block;"><div class="_1gZJUfw" tabindex="-1" style="background-color: rgb(0, 0, 0);"><video x-webkit-airplay="allow" disableremoteplayback="" poster="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="_20Eh5WL _1ll4Zna _3D52tih" playsinline="playsinline" webkit-playsinline="webkit-playsinline" preload="auto" src="blob:https://disk.yandex.ru/6150c189-b3bc-460b-94ea-c2368de22bad" style="opacity: 1; object-fit: contain;"></video><div style="position: absolute; bottom: 1px; left: 1px;"></div></div><div class="_2jlIyxK"></div></yaplayertag>
                                <div class="mt-2">
                                    <iframe src="{{$video->yandex_url}}"></iframe>
                                    <a href="{{$video->yandex_url}}">Запись трансляции</a>
{{--                                    <iframe data-url="{{$video->yandex_url}}" class="yandex-iframe rounded" width="560" height="316" frameborder="0" allowfullscreen></iframe>--}}
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
{{--                            <strong>Video Path (YouTube URL):</strong> {{ $video->path ?? 'N/A' }}--}}
                            @if($video->path)
                                <div class="mt-2">
                                    <iframe data-url="{{ $video->path }}" class="youtube-iframe rounded" width="560" height="316" frameborder="0" allowfullscreen></iframe>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        <a href="{{ route('video-libraries.index') }}" class="btn btn-primary mt-3" style="background:#9c41b1;border:1px solid #9c41b1;">Назад к библиотекам</a>

    </div>
@endsection

    <style>
        /* Основные стили для контейнера */
        .container {
            max-width: 900px;
        }

        /* Стили для заголовков */
        h1, h3 {
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        /* Стили для списка видео */
        .list-group-item {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: .25rem;
            padding: 1.5rem;
        }

        /* Стили для iframe */
        iframe {
            border-radius: .5rem;
        }

        /* Стили для кнопки "Back to Libraries" */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
    <script>
        // Функция для создания embed URL Google Drive
        const getGoogleEmbedUrl = (url) => {
            debugger
            const match = url.match(/drive.google.com\/file\/d\/(.*)\//);
            return match ? `https://drive.google.com/file/d/${match[1]}/preview` : url;
        };

        // Функция для создания embed URL Yandex Disk
        const getYandexEmbedUrl = (url) => `${url}?preview`;

        // Функция для создания embed URL YouTube
        const getYouTubeEmbedUrl = (url) => {
            const match = url.match(/(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)|(?:https?:\/\/)?(?:www\.)?youtu\.be\/([a-zA-Z0-9_-]+)/);
            const videoId = match?.[1] || match?.[2];
            return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
        };

        document.addEventListener('DOMContentLoaded', () => {
            // Обновление URL в iframe для Google Drive
            document.querySelectorAll('.google-iframe').forEach(iframe => {
                iframe.src = getGoogleEmbedUrl(iframe.getAttribute('data-url'));
            });

            // Обновление URL в iframe для Yandex Disk
            document.querySelectorAll('.yandex-iframe').forEach(iframe => {
                iframe.src = getYandexEmbedUrl(iframe.getAttribute('data-url'));
            });

            // Обновление URL в iframe для YouTube
            document.querySelectorAll('.youtube-iframe').forEach(iframe => {
                iframe.src = getYouTubeEmbedUrl(iframe.getAttribute('data-url'));
            });
        });

    </script>
