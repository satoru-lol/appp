@extends('app', [
    'title' => 'Видео',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
    <div class="container-xl px-4 mt-5">
        <div class="text-center mb-5">
            <h1 class="display-3 font-weight-bold text-dark">Видео {{ $videoId }}</h1>
        </div>
        <div class="row">
            <!-- Пример видео, вы можете заменить это на ваши динамические данные -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="video-card shadow-lg rounded-lg overflow-hidden">
                    <video class="w-100" controls>
                        <source src="video1.mp4" type="video/mp4">
                        Ваш браузер не поддерживает видео.
                    </video>
                    <div class="p-4 text-center">
                        <h4 class="font-weight-bold text-dark mb-3">Видео 1</h4>
                        <a href="#" class="btn btn-brand">Смотреть анонс</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="video-card shadow-lg rounded-lg overflow-hidden">
                    <video class="w-100" controls>
                        <source src="video2.mp4" type="video/mp4">
                        Ваш браузер не поддерживает видео.
                    </video>
                    <div class="p-4 text-center">
                        <h4 class="font-weight-bold text-dark mb-3">Видео 2</h4>
                        <a href="#" class="btn btn-brand">Смотреть анонс</a>
                    </div>
                </div>
            </div>
            <!-- Добавьте больше видео по мере необходимости -->
        </div>
    </div>
@endsection
<style>
    /* Основные стили для видео-страницы */
    .video-card {
        position: relative;
        background: #fff;
        border-radius: 12px; /* Умеренно скругленные углы */
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Легкая тень */
    }

    .video-card:hover {
        transform: translateY(-5px); /* Легкий эффект подъема */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        border: 1px solid #e0e0e0; /* Легкая граница при наведении */
    }

    .video-card video {
        display: block;
        width: 100%;
        height: auto;
    }

    .btn-brand {
        background-color: #562E74;
        color: #fff;
        border: none;
        border-radius: 30px; /* Полностью скругленные углы */
        padding: 12px 24px; /* Отступы */
        font-size: 1rem; /* Размер шрифта */
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn-brand:hover {
        background-color: #4a1f54;
        transform: translateY(-2px); /* Легкий эффект подъема */
    }

    .text-center h1 {
        font-size: 3rem; /* Крупный размер шрифта */
        color: #333;
        font-weight: 700;
    }

    .card-body {
        padding: 2rem;
    }

    .video-card .p-4 {
        padding: 1.5rem;
    }

    .video-card h4 {
        font-size: 1.2rem;
        color: #333;
        margin-bottom: 1rem;
    }

    .container-xl {
        max-width: 1200px;
    }

    .row {
        margin-top: 2rem;
    }

</style>
