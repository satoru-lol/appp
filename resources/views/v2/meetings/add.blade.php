@extends('app', [
    'title' => isset($blog) ? 'Редактирование встречи' : 'Добавление встречи',
    'keywords' => '',
    'description' => ''
])

@section('content')
<style>
    .breadcrumb {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(97,52,130,0.07);
        padding: 12px 22px;
        margin-bottom: 24px;
        font-size: 1.04rem;
        --bs-breadcrumb-divider-color: #b39ddb;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        color: #b39ddb;
        font-size: 1.1em;
        padding-right: 6px;
        padding-left: 6px;
    }
    .breadcrumb-item a {
        color: #613482;
        text-decoration: none;
        font-weight: 500;
        transition: color .18s;
    }
    .breadcrumb-item a:hover {
        color: #7e57c2;
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #7e57c2;
        font-weight: 600;
    }
    .meetings-v2-container { max-width: 1200px; margin: 0 auto; padding: 32px 15px; }
    .meeting-form-v2 { max-width: 800px; margin: 32px auto; padding: 32px; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(80, 80, 120, 0.1); }
    .meeting-form-v2 .form-title { font-size: 2rem; font-weight: 700; margin-bottom: 24px; }
    .meeting-form-v2 .form-group { margin-bottom: 20px; }
    .meeting-form-v2 .form-group label { display: block; font-weight: 600; margin-bottom: 8px; }
    .meeting-form-v2 .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; }
    .meeting-form-v2 .form-control:focus { border-color: #6c63ff; outline: none; }
    .meeting-form-v2 .form-check-label { margin-left: 8px; }
    .meeting-form-v2 .btn-submit { background: #6c63ff; color: #fff; border: none; border-radius: 8px; padding: 14px 28px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .meeting-form-v2 .btn-submit:hover { background: #4b47b5; }
    .text-danger { color: #e74c3c; font-size: 0.9rem; margin-top: 4px; }
</style>

<div class="meetings-v2-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('v2.meetings.index') }}">Наши встречи</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ isset($blog) ? 'Редактирование' : 'Создание' }}</li>
        </ol>
    </nav>
    
    <div class="meeting-form-v2">
        <h1 class="form-title">{{ isset($blog) ? 'Редактирование встречи' : 'Создание новой встречи' }}</h1>

        <form action="{{ isset($blog) ? route('v2.meetings.update', $blog->id) : route('v2.meetings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($blog))
                @method('PUT')
            @endif

            <div class="form-group">
                <label for="name">Название встречи</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $blog->name ?? '') }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="date">Дата и время</label>
                <input type="datetime-local" id="date" name="date" class="form-control" value="{{ old('date', isset($blog->date) ? \Carbon\Carbon::parse($blog->date)->format('Y-m-d\TH:i') : '') }}" required>
                @error('date') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="fio">ФИО организатора</label>
                <input type="text" id="fio" name="fio" class="form-control" value="{{ old('fio', $blog->fio ?? '') }}" required>
                @error('fio') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="format_id">Формат встречи</label>
                <select id="format_id" name="format_id" class="form-control" required>
                    @foreach (\App\Models\MeetingFormat::all() as $format)
                        <option value="{{ $format->id }}" {{ (old('format_id', $blog->format_id ?? '') == $format->id) ? 'selected' : '' }}>{{ $format->format }}</option>
                    @endforeach
                </select>
                @error('format_id') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="feedback">Ссылка или место встречи</label>
                <input type="text" id="feedback" name="feedback" class="form-control" value="{{ old('feedback', $blog->feedback ?? '') }}">
                @error('feedback') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="image">Изображение</label>
                <input type="file" id="image" name="image" class="form-control">
                @if(isset($blog) && $blog->image)
                    <img src="{{ asset('img/blog/'.$blog->image) }}" alt="Current image" style="max-width: 200px; margin-top: 10px;">
                @endif
                @error('image') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="text">Описание</label>
                <textarea id="text" name="text" class="form-control" rows="8">{{ old('text', $blog->blogContent->text ?? '') }}</textarea>
                @error('text') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="amount">Стоимость</label>
                <input type="text" id="amount" name="amount" class="form-control" placeholder="Оставьте пустым для бесплатной встречи" value="{{ old('amount', $blog->amount ?? '') }}">
                 @error('amount') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Количество участников</label>
                <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Оставьте пустым для неограниченного количества" value="{{ old('quantity', $blog->quantity ?? '') }}">
                @error('quantity') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-submit">{{ isset($blog) ? 'Сохранить изменения' : 'Создать встречу' }}</button>
        </form>
    </div>
</div>
@endsection 