@extends('app', [
'title' => 'Видео',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])

@section('content')
    <div class="container-xl px-4 mt-4">
        <div class="transaction-card card">
            <div class="card-title">
                <h3>Видеотека</h3>
            </div>
            <div class="card-body">
                <div id="video-buttons-container" class="btn-container">
                    <a href="{{ route('video.detail', ['id' => 1]) }}" class="btn btn-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                            <path d="M8 3.293l5 3-5 3-5-3 5-3zM7 0v16l7-8L7 0z"/>
                        </svg>
                        Всё о развитии и продвижении частной практики
                    </a>
                    <a href="{{ route('video.detail', ['id' => 2]) }}" class="btn btn-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                            <path d="M8 3.293l5 3-5 3-5-3 5-3zM7 0v16l7-8L7 0z"/>
                        </svg>
                        Всё о профессиональной подготовке психолога к частной практике
                    </a>
                    <a href="{{ route('video.detail', ['id' => 3]) }}" class="btn btn-brand">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-play-circle" viewBox="0 0 16 16">
                            <path d="M8 3.293l5 3-5 3-5-3 5-3zM7 0v16l7-8L7 0z"/>
                        </svg>
                        Фрагменты с курсов Портала ДПО
                    </a>
                    <!-- Дополнительные кнопки будут добавлены через JavaScript -->
                </div>
                <button id="show-more-btn" class="btn btn-brand mt-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM4.646 6.354a.5.5 0 0 1 .708 0L8 8.293l2.646-2.646a.5.5 0 1 1 .708.708L8.707 9l2.647 2.646a.5.5 0 1 1-.708.708L8 9.707l-2.646 2.647a.5.5 0 1 1-.708-.708L7.293 9 4.646 6.354z"/>
                    </svg>
                    Смотреть все
                </button>
            </div>
        </div>
    </div>
@endsection
