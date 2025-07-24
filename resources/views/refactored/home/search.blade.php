@extends('app')

@section('title', $title ?? 'Поиск - АЧПП')
@section('description', $description ?? 'Результаты поиска на платформе АЧПП')

@section('content')
<div class="container py-5">
    <h1>Результаты поиска</h1>
    
    <div class="mb-4">
        <p>Поиск по запросу: <strong>{{ $query }}</strong></p>
        <p>Тип поиска: <strong>{{ $type }}</strong></p>
    </div>

    @if(isset($results['courses']) && $results['courses']->count() > 0)
        <h3>Курсы</h3>
        <div class="row mb-4">
            @foreach($results['courses'] as $course)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($results['specialists']) && $results['specialists']->count() > 0)
        <h3>Специалисты</h3>
        <div class="row mb-4">
            @foreach($results['specialists'] as $specialist)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $specialist->user->firstname ?? '' }} {{ $specialist->user->lastname ?? '' }}</h5>
                            <p class="card-text">{{ $specialist->degree }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($results['blogs']) && $results['blogs']->count() > 0)
        <h3>Статьи</h3>
        <div class="row mb-4">
            @foreach($results['blogs'] as $blog)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $blog->title }}</h5>
                            <p class="card-text">{{ Str::limit($blog->description, 100) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(isset($results['videos']) && $results['videos']->count() > 0)
        <h3>Видео</h3>
        <div class="row mb-4">
            @foreach($results['videos'] as $video)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $video->title }}</h5>
                            <p class="card-text">{{ Str::limit($video->description, 100) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if(
        (!isset($results['courses']) || $results['courses']->count() == 0) &&
        (!isset($results['specialists']) || $results['specialists']->count() == 0) &&
        (!isset($results['blogs']) || $results['blogs']->count() == 0) &&
        (!isset($results['videos']) || $results['videos']->count() == 0)
    )
        <div class="alert alert-info">
            <h4>Ничего не найдено</h4>
            <p>По вашему запросу "{{ $query }}" ничего не найдено. Попробуйте изменить поисковый запрос.</p>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('v2.refactored.home.index') }}" class="btn btn-primary">Вернуться на главную</a>
    </div>
</div>
@endsection