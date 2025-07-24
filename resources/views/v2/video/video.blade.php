@extends('app')

@section('title', $video->title)

@section('content')
<div class="container videolibrary v2 video-page">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('v2.video.index') }}">Видеотека</a></li>
            @if($video->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('v2.video.category', $video->category->id) }}">{{ $video->category->name }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $video->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="video-player-container mb-4">
                <div class="video-player">
                    @if(strpos($video->url, 'youtube.com') !== false || strpos($video->url, 'youtu.be') !== false)
                        @php
                            // Извлечение ID видео из YouTube URL
                            $videoId = '';
                            if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $video->url, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $video->url, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('/youtu\.be\/([^\&\?\/]+)/', $video->url, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.youtube.com/embed/{{ $videoId }}" 
                                title="{{ $video->title }}"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @elseif(strpos($video->url, 'vimeo.com') !== false)
                        @php
                            // Извлечение ID видео из Vimeo URL
                            $videoId = '';
                            if (preg_match('/vimeo\.com\/(\d+)/', $video->url, $matches)) {
                                $videoId = $matches[1];
                            }
                        @endphp
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://player.vimeo.com/video/{{ $videoId }}" 
                                title="{{ $video->title }}"
                                frameborder="0"
                                allow="autoplay; fullscreen; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @elseif(strpos($video->url, 'kinescope.io') !== false)
                        @php
                            // Подготавливаем URL для Kinescope
                            $kinescopeUrl = str_replace('kinescope.io/', 'kinescope.io/embed/', $video->url);
                            if (strpos($kinescopeUrl, 'embed/') === false) {
                                $kinescopeUrl = str_replace('kinescope.io', 'kinescope.io/embed', $kinescopeUrl);
                            }
                            // Убираем параметры запроса, если есть
                            $kinescopeUrl = explode('?', $kinescopeUrl)[0];
                        @endphp
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="{{ $kinescopeUrl }}" 
                                title="{{ $video->title }}"
                                frameborder="0" 
                                allow="autoplay; fullscreen" 
                                allowfullscreen
                            ></iframe>
                        </div>
                    @else
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="{{ $video->url }}" 
                                title="{{ $video->title }}"
                                frameborder="0"
                                allow="autoplay; fullscreen"
                                allowfullscreen
                                @if($video->thumbnail_url) poster="{{ $video->thumbnail_url }}" @endif
                            ></iframe>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="video-info mb-4">
                <h1 class="video-title">{{ $video->title }}</h1>
                
                <div class="video-meta">
                    <span class="badge bg-secondary me-2">Добавлено: {{ $video->created_at->format('d.m.Y') }}</span>
                    @if($video->category)
                        <span class="badge bg-primary">{{ $video->category->name }}</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="related-videos-list-scrollable" style="max-height:600px; overflow-y:auto; overflow-x:hidden;">
                <div class="row g-2">
                @if($video->category && $video->category->videos->count() > 1)
                    @foreach($video->category->videos->sortBy('title') as $relatedVideo)
                        @if($relatedVideo->id != $video->id)
                        <div class="col-12">
                            <a href="{{ route('v2.video.show', $relatedVideo->id) }}" class="video-preview-compact d-block position-relative overflow-hidden rounded-3" style="background-image: url('{{ $relatedVideo->thumbnail_url ?? asset('img/no-video.jpg') }}'); background-size: cover; background-position: center; min-height: 140px; height: 140px;">
                                <div class="video-preview-overlay position-absolute top-0 start-0 w-100 h-100" style="background:rgba(40,16,60,0.45);">
                                    <div class="d-flex justify-content-between align-items-start w-100 px-2 pt-2 position-absolute top-0 start-0">
                                        <div class="video-preview-title text-white text-truncate" style="font-size:1rem; font-weight:600; max-width:70%;">{{ $relatedVideo->title }}</div>
                                        <div class="video-preview-date text-white-50 small ms-2 mt-1">{{ $relatedVideo->created_at ? \Carbon\Carbon::parse($relatedVideo->created_at)->format('d.m.Y') : '' }}</div>
                                    </div>
                                    <span class="video-preview-play position-absolute top-50 start-50 translate-middle" style="font-size:2.2rem; color:#fff; opacity:0.95;">
                                        <i class="fa fa-play-circle"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                        @endif
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">Нет других видео в этой категории</div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initVideoPlayer() {
        const playerContainer = document.getElementById('kinescope-player');
        if (!playerContainer) return;

        const videoId = playerContainer.dataset.videoId;
        if (!videoId) return;

        if (typeof Kinescope !== 'undefined') {
             Kinescope.IframePlayer.create(playerContainer.id, {
                videoId: videoId,
                size: {
                    width: '100%',
                    height: '100%'
                }
            }).then(player => {
                console.log('Kinescope player initialized:', player);
            }).catch(error => {
                console.error('Kinescope player initialization error:', error);
            });
        } else {
            console.error('Kinescope SDK is not loaded.');
        }
    }

    initVideoPlayer();
});
</script>
@endpush

@section('styles')
<style>
    .videolibrary.v2.video-page .video-player-container {
        /* background-color: #000; */
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }
    
    .videolibrary.v2.video-page .video-title {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .videolibrary.v2.video-page .video-meta {
        margin-bottom: 1rem;
    }
    
    .videolibrary.v2.video-page .related-video-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .videolibrary.v2.video-page .list-group-item.active {
        background-color: #562E74;
        border-color: #562E74;
    }

    .videolibrary.v2.video-page .list-group-item.active .related-video-link {
        color: #fff;
    }
    
    .videolibrary.v2.video-page .related-video-link:hover {
        background-color: #f8f9fa;
    }
    
    .videolibrary.v2.video-page .related-video-info {
        flex: 1;
        overflow: hidden;
    }
    
    .videolibrary.v2.video-page .related-video-info h6 {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Стили для карточки с похожими видео */
    .videolibrary.v2.video-page .related-videos-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .videolibrary.v2.video-page .related-videos-card .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
    }
    .video-v2-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 2px 16px rgba(97,52,130,0.08);
        width: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s;
        border: 2px solid #eee;
        margin-bottom: 0;
    }
    .video-v2-card:hover {
        box-shadow: 0 4px 32px rgba(97,52,130,0.18);
        border-color: #b39ddb;
    }
    .video-v2-image {
        display: block;
        background: linear-gradient(120deg, #ede7f6 60%, #fff 100%);
        height: 180px;
        background-size: cover;
        background-position: center;
        position: relative;
        text-align: center;
        transition: filter .2s;
    }
    .video-v2-play {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3rem;
        color: #613482;
        opacity: 0.85;
        pointer-events: none;
    }
    .video-v2-info {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .video-v2-title {
        font-size: 1.2rem;
        color: #613482;
        margin-bottom: 12px;
        font-weight: 600;
    }
    .video-v2-title a {
        color: #613482;
        text-decoration: none;
    }
    .video-v2-title a:hover {
        text-decoration: underline;
    }
    .video-v2-date, .video-v2-speakers {
        color: #444;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        font-size: 0.98rem;
    }
    .video-v2-date svg, .video-v2-speakers svg {
        margin-right: 8px;
    }
    .video-v2-actions {
        margin-top: auto;
        padding-top: 16px;
    }
    .video-v2-btn {
        display: inline-block;
        background: #613482;
        color: #fff;
        border-radius: 8px;
        padding: 8px 18px;
        text-decoration: none;
        transition: background .2s;
    }
    .video-v2-btn:hover {
        background: #7e57c2;
    }
    @media (max-width: 768px) {
        .video-v2-card { width: 100%; max-width: 340px; }
    }
    .video-preview-compact {
        min-height: 140px;
        height: 140px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(97,52,130,0.08);
        transition: box-shadow .18s, transform .18s;
        cursor: pointer;
        position: relative;
        display: block;
    }
    .video-preview-compact:hover {
        box-shadow: 0 4px 16px rgba(97,52,130,0.18);
        transform: translateY(-2px) scale(1.02);
    }
    .video-preview-overlay {
        background: rgba(40,16,60,0.45);
        color: #fff;
        border-radius: 12px;
        z-index: 2;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
    .video-preview-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 1rem;
        font-weight: 600;
        max-width: 70%;
    }
    .video-preview-date {
        font-size: 0.85rem;
        white-space: nowrap;
        margin-left: 8px;
        margin-top: 2px;
    }
    .video-preview-play {
        z-index: 3;
        pointer-events: none;
    }
    .related-videos-list-scrollable::-webkit-scrollbar {
        width: 6px;
        background: #eee;
        border-radius: 6px;
    }
    .related-videos-list-scrollable::-webkit-scrollbar-thumb {
        background: #d1c4e9;
        border-radius: 6px;
    }
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
</style>
@endsection 