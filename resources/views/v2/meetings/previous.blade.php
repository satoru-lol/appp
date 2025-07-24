@extends('app')

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
    .meetings-v2-title { font-size: 2.2rem; font-weight: 700; margin-bottom: 32px; }
    .meetings-v2-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
    .meeting-card-v2 {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 16px rgba(80, 80, 120, 0.08);
        padding: 0;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s;
        position: relative;
        opacity: 0.8;
        height: 100%;
    }
    .meeting-card-v2:hover { box-shadow: 0 4px 32px rgba(80, 80, 120, 0.16); opacity: 1; }
    .meeting-card-v2 .meeting-image {
        width: 100%;
        height: 180px;
        border-radius: 18px 18px 0 0;
        object-fit: cover;
        background-color: #f5f5f5;
    }
    .meeting-card-v2 .meeting-image-placeholder {
        width: 100%;
        height: 180px;
        border-radius: 18px 18px 0 0;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 1.1rem;
        text-align: center;
        padding: 20px;
    }
    .meeting-card-v2 .meeting-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .meeting-card-v2 .meeting-info {
        flex-grow: 1;
    }
    .meeting-card-v2 .meeting-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 8px; }
    .meeting-card-v2 .meeting-date { color: #888; font-weight: 500; margin-bottom: 6px; }
    .meeting-card-v2 .meeting-meta { font-size: 0.98rem; color: #555; margin-bottom: 4px; }
    .meeting-card-v2 .meeting-actions { margin-top: 12px; display: flex; gap: 10px; }
    .meeting-card-v2 .btn-v2 { border: none; border-radius: 8px; padding: 8px 18px; font-weight: 500; background: #888; color: #fff; transition: background 0.2s; cursor: pointer; text-decoration: none; }
    .meeting-card-v2 .btn-v2:hover { background: #555; }
    .meeting-stats {
        display: flex;
        gap: 16px;
        margin-top: 12px;
        color: #666;
        font-size: 0.9rem;
    }
    .stat-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .stat-item i {
        color: #888;
    }
    @media (max-width: 767px) { 
        .meeting-card-v2 .meeting-content { padding: 18px 16px 16px 16px; } 
        .meeting-card-v2 .meeting-title { font-size: 1.1rem; }
        .meeting-card-v2 .meeting-image,
        .meeting-card-v2 .meeting-image-placeholder {
            height: 150px;
        }
    }
</style>
<div class="meetings-v2-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('v2.meetings.index') }}">Наши встречи</a></li>
            <li class="breadcrumb-item active" aria-current="page">Прошедшие встречи</li>
        </ol>
    </nav>
    <div class="meetings-v2-title">Прошедшие встречи</div>
    <div class="meetings-v2-list">
        @forelse ($blogs as $item)
            <div class="meeting-card-v2">
                @if($item->image)
                    <div class="meeting-image-container">
                        <img 
                            src="{{ asset('img/blog/'.$item->image) }}" 
                            alt="{{ $item->name }}" 
                            class="meeting-image js-meeting-image"
                            data-fallback-text="{{ $item->name }}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="meeting-image-placeholder" style="display: none;">
                            {{ Str::limit($item->name, 50) }}
                        </div>
                    </div>
                @else
                    <div class="meeting-image-placeholder">
                        {{ Str::limit($item->name, 50) }}
                    </div>
                @endif
                <div class="meeting-content">
                    <div class="meeting-info">
                        <div class="meeting-title">{{ $item->name }}</div>
                        <div class="meeting-date">{{ $item->formattedDate }}</div>
                        <div class="meeting-meta"><b>Формат:</b> {{ $item->format->format ?? '-' }}</div>
                        <div class="meeting-meta"><b>Организатор:</b> {{ $item->fio }}</div>
                        
                        <div class="meeting-stats">
                            <div class="stat-item">
                                <i class="fa fa-users"></i> {{ $item->participants_count }}
                            </div>
                            <div class="stat-item">
                                <i class="fa fa-comments"></i> {{ $item->comments_count }}
                            </div>
                            <div class="stat-item">
                                <i class="fa fa-eye"></i> {{ $item->views ?? 0 }}
                            </div>
                        </div>
                    </div>
                    <div class="meeting-actions">
                        <a href="{{ route('v2.meetings.show', $item->id) }}" class="btn-v2">Подробнее</a>
                    </div>
                </div>
            </div>
        @empty
            <div>Нет прошедших встреч для отображения.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $blogs->links('vendor.pagination.bootstrap-5') }}</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Дополнительная проверка загрузки изображений
    document.querySelectorAll('.js-meeting-image').forEach(function(img) {
        // Проверяем, загружено ли изображение
        if (img.complete) {
            // Если изображение уже загружено, но с ошибкой
            if (img.naturalWidth === 0) {
                img.style.display = 'none';
                img.nextElementSibling.style.display = 'flex';
            }
        } else {
            // Добавляем обработчик на загрузку
            img.addEventListener('error', function() {
                this.style.display = 'none';
                this.nextElementSibling.style.display = 'flex';
            });
        }
    });
});
</script>
@endsection 