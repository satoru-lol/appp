@extends('app', [
'title' => 'Video Libraries',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Видеотека</h1>
        {{--<a href="{{ route('video-libraries.create') }}" class="btn btn-primary mb-4">Создать видеотеку</a>--}}

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            @foreach($libraries as $library)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0 rounded-lg youtube-card">
                        <div class="card-body p-0">
                            <div class="position-relative">
                                <img src="https://media.licdn.com/dms/image/D4D12AQG28boaE6Xkuw/article-cover_image-shrink_720_1280/0/1681101067732?e=2147483647&v=beta&t=83E-ENNVKpgZYvskSNEt70JYMObaijVb0IAxVAD9lAQ" alt="Thumbnail" class="card-img-top youtube-thumbnail">
                                <div class="card-overlay d-flex align-items-center justify-content-center">
                                    <span class="text-white">▶</span>
                                </div>
                            </div>
                            <div class="card-footer p-3">
                                <h5 class="card-title">{{ $library->title }}</h5>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('video-libraries.show', $library->id) }}" class="btn btn-outline-info btn-sm">Посмотреть</a>
                                    <a href="{{ route('video-libraries.edit', $library->id) }}" class="btn btn-outline-warning btn-sm">Редактировать</a>
                                    <form action="{{ route('video-libraries.destroy', $library->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Стили для карточек */
        .youtube-card {
            border-radius: 0.5rem;
            overflow: hidden;
            position: relative;
        }

        .youtube-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-body {
            padding: 0;
        }

        .card-footer {
            padding: 1rem;
        }

        .card-title {
            font-size: 1rem;
            font-weight: bold;
            color: #333;
            margin: 0;
        }

        .btn {
            border-radius: 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 500;
        }

        .btn-outline-info {
            color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: #fff;
        }

        .btn-outline-warning {
            color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #fff;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
        }

        .youtube-card:hover .card-overlay {
            display: flex;
        }

        .card-overlay span {
            font-size: 2rem;
            color: #fff;
            cursor: pointer;
        }
    </style>
@endsection
