@extends('app')

@section('title', $category->name)

@section('content')
<div class="container videolibrary v2 category-page">
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('v2.video.index') }}">Видеотека</a></li>
            @if($category->parent)
                <li class="breadcrumb-item">
                    <a href="{{ route('v2.video.category', $category->parent->id) }}">{{ $category->parent->name }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <h1 class="my-4">{{ $category->name }}</h1>
    
    <div class="row mb-4">
        <div class="col-md-8 col-lg-6">
            <form class="video-search-form d-flex" onsubmit="return false;">
                <input type="text" class="form-control video-search-input" placeholder="Поиск видео..." id="videoSearch">
                <button class="btn video-search-btn" type="button" id="btnSearch">Найти</button>
            </form>
        </div>
    </div>

    @if($category->children->count() > 0)
        <div class="subcategories-container mb-4">
            <h5 class="mb-3">Подкатегории</h5>
            <div class="row">
                @foreach($category->children as $child)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('v2.video.category', $child->id) }}" class="subcategory-card">
                            <div class="subcategory-icon">
                                <i class="fa fa-folder"></i>
                            </div>
                            <div class="subcategory-info">
                                <h6 class="subcategory-title">{{ $child->name }}</h6>
                                <span class="subcategory-count">{{ $child->videos->count() }} видео</span>
                            </div>
                            <div class="subcategory-arrow">
                                <i class="fa fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <div class="category-content">
        <div class="row videos-container" id="videos-container">
            @if($videos->count() > 0)
                @foreach($videos as $video)
                    <div class="col-md-4 mb-4 video-item">
                        <div class="video-v2-card">
                            <a href="{{ route('v2.video.show', $video->id) }}" class="video-v2-image" style="background-image: url('{{ $video->thumbnail_url ?? asset('img/no-video.jpg') }}')">
                                <span class="video-v2-play"><i class="fa fa-play-circle"></i></span>
                            </a>
                            <div class="video-v2-info">
                                <h2 class="video-v2-title"><a href="{{ route('v2.video.show', $video->id) }}">{{ $video->title }}</a></h2>
                                @if($video->created_at)
                                    <p class="video-v2-date">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#613482" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329" stroke="#613482" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($video->created_at)->format('d.m.Y') }}
                                    </p>
                                @endif
                                @if($video->speakers)
                                    <p class="video-v2-speakers">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        {{ $video->speakers }}
                                    </p>
                                @endif
                                <div class="video-v2-actions">
                                    <a href="{{ route('v2.video.show', $video->id) }}" class="video-v2-btn">Смотреть</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @elseif($category->children->count() == 0)
                <div class="col-12">
                    <div class="alert alert-info">
                        В данной категории пока нет видео.
                    </div>
                </div>
            @endif
        </div>
        @if($category->videos()->count() > 10)
            <div class="text-center mt-3">
                <button class="btn btn-outline-primary load-more" data-category-id="{{ $category->id }}" data-page="1">
                    Загрузить еще
                </button>
            </div>
        @endif
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    function initLoadMore() {
        const loadMoreButton = document.querySelector('.load-more');
        if (!loadMoreButton) return;

        loadMoreButton.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            let page = parseInt(this.getAttribute('data-page'));
            page = isNaN(page) ? 1 : page + 1;
            const container = document.getElementById('videos-container');
            
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Загрузка...';
            this.disabled = true;

            fetch(`/v2/api/categories/${categoryId}/videos?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (data.videos && data.videos.data.length > 0) {
                        // Собираем id уже отрисованных видео
                        const existingIds = Array.from(container.querySelectorAll('[data-video-id]')).map(el => el.getAttribute('data-video-id'));
                        let added = [];
                        data.videos.data.forEach(video => {
                            if (!existingIds.includes(String(video.id))) {
                                container.insertAdjacentHTML('beforeend', createVideoCardHtml(video));
                                added.push(video.title);
                            }
                        });
                        console.log('Добавлены видео:', added);
                        this.setAttribute('data-page', page);
                        this.innerHTML = 'Загрузить еще';
                        this.disabled = false;

                        if (page >= data.videos.last_page) {
                            this.style.display = 'none';
                        }
                    } else {
                        this.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке видео:', error);
                    this.innerHTML = 'Ошибка. Попробовать снова';
                    this.disabled = false;
                });
        });
    }

    function initVideoSearch() {
        const searchInput = document.getElementById('videoSearch');
        const searchButton = document.getElementById('btnSearch');
        if (!searchInput || !searchButton) return;

        function performSearch() {
            const searchQuery = searchInput.value.toLowerCase().trim();
            const originalContainer = document.querySelector('.category-content');
            let searchResultsContainer = document.getElementById('search-results-container');

            if (searchQuery.length < 2) {
                if (searchResultsContainer) {
                    searchResultsContainer.remove();
                    originalContainer.style.display = 'block';
                }
                return;
            }

            if (!searchResultsContainer) {
                searchResultsContainer = document.createElement('div');
                searchResultsContainer.id = 'search-results-container';
                searchResultsContainer.className = 'mt-4';
                originalContainer.parentNode.insertBefore(searchResultsContainer, originalContainer.nextSibling);
            }
            
            originalContainer.style.display = 'none';
            searchResultsContainer.innerHTML = '<div class="text-center my-5"><i class="fa fa-spinner fa-spin fa-3x"></i><p class="mt-2">Идет поиск...</p></div>';

            const categoryId = '{{ $category->id }}';
            fetch(`/v2/api/search/videos?query=${encodeURIComponent(searchQuery)}&category_id=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    let html = `
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Результаты поиска по "${searchQuery}"</h3>
                            <button id="clear-search-results" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-times"></i> Очистить
                            </button>
                        </div>
                    `;

                    let allVideos = [];
                    if (data.success && data.results && data.results.length > 0) {
                        data.results.forEach(categoryResult => {
                            if (categoryResult.videos && categoryResult.videos.length > 0) {
                                allVideos = allVideos.concat(categoryResult.videos);
                            }
                        });
                    }

                    if (allVideos.length > 0) {
                        html += `<p class="text-muted">Найдено: ${allVideos.length}</p>`;
                        html += '<div class="row videos-container">';
                        allVideos.forEach(video => {
                            html += createVideoCardHtml(video);
                        });
                        html += '</div>';
                    } else {
                        html += '<div class="alert alert-info">По вашему запросу в этой категории ничего не найдено.</div>';
                    }
                    
                    searchResultsContainer.innerHTML = html;

                    document.getElementById('clear-search-results').addEventListener('click', () => {
                        searchInput.value = '';
                        searchResultsContainer.remove();
                        originalContainer.style.display = 'block';
                    });
                })
                .catch(error => {
                    console.error('Ошибка поиска:', error);
                    searchResultsContainer.innerHTML = '<div class="alert alert-danger">Произошла ошибка. Попробуйте снова.</div>';
                });
        }

        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }

    function createVideoCardHtml(video) {
        const thumbnailUrl = video.thumbnail_url || '{{ asset("img/no-video.jpg") }}';
        const videoUrl = `/v2/video/${video.id}`;
        return `
            <div class="col-md-4 mb-4 video-item" data-video-id="${video.id}">
                <div class="video-v2-card">
                    <a href="${videoUrl}" class="video-v2-image" style="background-image: url('${thumbnailUrl}')">
                        <span class="video-v2-play"><i class="fa fa-play-circle"></i></span>
                    </a>
                    <div class="video-v2-info">
                        <h2 class="video-v2-title"><a href="${videoUrl}">${video.title}</a></h2>
                        ${video.created_at ? `<p class="video-v2-date"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#613482" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/><path d="M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329" stroke="#613482" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> ${video.created_at}</p>` : ''}
                        ${video.speakers ? `<p class="video-v2-speakers"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> ${video.speakers}</p>` : ''}
                        <div class="video-v2-actions">
                            <a href="${videoUrl}" class="video-v2-btn">Смотреть</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    initLoadMore();
    initVideoSearch();
});
</script>
@endpush

@section('styles')
<style>
    .videolibrary.v2 .video-thumbnail {
        position: relative;
        height: 180px;
        overflow: hidden;
    }
    
    .videolibrary.v2 .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .videolibrary.v2 .placeholder-thumbnail {
        height: 180px;
        color: #6c757d;
    }
    
    .videolibrary.v2 .video-duration {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 2px 5px;
        border-radius: 3px;
        font-size: 0.8rem;
    }
    
    .category-description {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
        border-left: 4px solid #6c757d;
    }
    
    /* Стили для результатов поиска */
    .search-results {
        margin-bottom: 2rem;
    }
    
    .search-results .badge {
        font-size: 0.8rem;
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

    .video-search-form {
        display: flex;
        align-items: center;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(97,52,130,0.06);
        border: 1px solid #e0e0e0;
        background: #fff;
        height: 48px;
        position: relative;
        overflow: hidden;
    }
    .video-search-input {
        border: none !important;
        outline: none;
        box-shadow: none;
        background: transparent;
        border-radius: 12px 0 0 12px;
        height: 48px;
        font-size: 1rem;
        flex: 1 1 auto;
        padding-left: 1.2rem;
    }
    .video-search-input:focus {
        background: #f8f6fc;
        outline: none;
        box-shadow: 0 0 0 2px #b39ddb33;
    }
    .video-search-btn {
        background: linear-gradient(90deg, #7e57c2 0%, #ab47bc 100%);
        color: #fff;
        border: none;
        border-radius: 0 12px 12px 0;
        height: 48px;
        font-weight: 600;
        min-width: 90px;
        box-shadow: none;
        transition: background .18s, box-shadow .18s;
    }
    .video-search-btn:hover, .video-search-btn:focus {
        background: linear-gradient(90deg, #6a1b9a 0%, #8e24aa 100%);
        color: #fff;
    }
    @media (max-width: 600px) {
        .video-search-form { height: 44px; }
        .video-search-input { height: 44px; font-size: 0.98rem; }
        .video-search-btn { height: 44px; min-width: 44px; padding: 0 0.8rem; }
    }

    /* Стили для карточек подкатегорий */
    .subcategory-card {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px 20px;
        text-decoration: none;
        color: #333;
        transition: background-color 0.2s, box-shadow 0.2s;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .subcategory-card:hover {
        background-color: #e9ecef;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        border-color: #b39ddb;
    }
    .subcategory-icon {
        font-size: 2rem;
        color: #613482;
        margin-right: 15px;
        width: 40px; /* Фиксированная ширина для иконки */
        text-align: center;
    }
    .subcategory-info {
        flex-grow: 1;
    }
    .subcategory-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #613482;
        margin-bottom: 5px;
    }
    .subcategory-count {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .subcategory-arrow {
        font-size: 1.2rem;
        color: #613482;
        margin-left: 15px;
        opacity: 0.7;
    }
</style>
@endsection 