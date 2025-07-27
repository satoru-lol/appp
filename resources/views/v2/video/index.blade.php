@extends('app')

@section('title', 'Видеотека')

@section('content')
<div class="container videolibrary v2">
    <h1 class="my-4">Видеотека</h1>

    <div class="row mb-4">
        <div class="col-md-8 col-lg-6">
            <form class="video-search-form d-flex" onsubmit="return false;">
                <input type="text" class="form-control video-search-input" placeholder="Поиск видео..." id="videoSearch">
                <button class="btn video-search-btn" type="button" id="btnSearch">Найти</button>
            </form>
        </div>
    </div>

    <div class="category-list row">
        @foreach($categories as $category)
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="category-card-modern">
                    <a href="{{ route('v2.video.category', $category->id) }}" class="category-card-link">
                        <div class="category-card-icon"><i class="fa fa-folder"></i></div>
                        <div class="category-card-info">
                            <div class="category-card-title">{{ $category->name }}</div>
                            <div class="category-card-count">{{ $category->videos_count }} видео</div>
                        </div>
                        <div class="category-card-arrow"><i class="fa fa-chevron-right"></i></div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('js')
<script>
/**
 * Скрипты для видеотеки v2
 */

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация функциональности видеотеки
    initVideolibrary();
});

/**
 * Инициализация всех компонентов видеотеки
 */
function initVideolibrary() {
    initCategoryToggles();
    initLoadMore();
    initVideoSearch();
    initVideoPlayer();
}

/**
 * Инициализация сворачивания/разворачивания категорий
 */
function initCategoryToggles() {
    const toggleButtons = document.querySelectorAll('.toggle-category');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const contentElement = document.getElementById('category-content-' + categoryId);
            if (!contentElement) return;
            
            const icon = this.querySelector('i');
            if (!icon) return;
            
            if (contentElement.style.display === 'none') {
                // Разворачиваем категорию
                contentElement.style.display = 'block';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
                
                // Сохраняем состояние в localStorage
                saveExpandState(categoryId, true);
            } else {
                // Сворачиваем категорию
                contentElement.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
                
                // Сохраняем состояние в localStorage
                saveExpandState(categoryId, false);
            }
        });
        
        // Применяем сохраненное состояние при загрузке
        const categoryId = button.getAttribute('data-category-id');
        const isExpanded = loadExpandState(categoryId);
        const contentElement = document.getElementById('category-content-' + categoryId);
        if (!contentElement) return;
        
        const icon = button.querySelector('i');
        if (!icon) return;
        
        if (isExpanded === false) {
            contentElement.style.display = 'none';
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-right');
        }
    });
}

/**
 * Инициализация "загрузить еще" функциональности
 */
function initLoadMore() {
    const loadMoreButtons = document.querySelectorAll('.load-more');
    
    loadMoreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const categoryId = this.getAttribute('data-category-id');
            const page = parseInt(this.getAttribute('data-page')) + 1;
            const container = document.getElementById('videos-container-' + categoryId) || document.getElementById('videos-container');
            if (!container) return;
            
            // Показываем индикатор загрузки
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Загрузка...';
            this.disabled = true;
            
            fetch(`/v2/api/categories/${categoryId}/videos?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (data.videos && data.videos.data && data.videos.data.length > 0) {
                        let html = '';
                        
                        data.videos.data.forEach(video => {
                            html += createVideoCardHtml(video);
                        });
                        
                        container.innerHTML += html;
                        this.setAttribute('data-page', page);
                        
                        // Восстанавливаем кнопку
                        this.innerHTML = 'Загрузить еще';
                        this.disabled = false;
                        
                        // Скрываем кнопку, если это последняя страница
                        if (page >= data.videos.last_page) {
                            this.style.display = 'none';
                        }
                    } else {
                        this.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке видео:', error);
                    this.innerHTML = 'Ошибка загрузки';
                    this.disabled = false;
                });
        });
    });
}

/**
 * Инициализация поиска видео через AJAX
 */
function initVideoSearch() {
    const searchInput = document.getElementById('videoSearch');
    const searchButton = document.getElementById('btnSearch');
    
    if (!searchInput || !searchButton) return;
    
    function performSearch() {
        const searchQuery = searchInput.value.toLowerCase().trim();
        
        if (searchQuery.length < 2) {
            clearSearchResults();
            return;
        }
        
        searchButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        searchButton.disabled = true;
        
        let searchResultsContainer = document.getElementById('search-results');
        if (!searchResultsContainer) {
            searchResultsContainer = document.createElement('div');
            searchResultsContainer.id = 'search-results';
            searchResultsContainer.className = 'search-results-container mt-4'; 
            const categoryList = document.querySelector('.category-list');
            if (categoryList) {
                 categoryList.parentNode.insertBefore(searchResultsContainer, categoryList);
            }
        }
        searchResultsContainer.innerHTML = '';

        const categoryList = document.querySelector('.category-list');
        if (categoryList) {
            categoryList.style.display = 'none';
        }
        
        const resultsHeader = document.createElement('div');
        resultsHeader.className = 'd-flex justify-content-between align-items-center mb-3';
        resultsHeader.innerHTML = `
            <h3>Результаты поиска: <span class="text-primary">${searchQuery}</span></h3>
            <button id="clear-search" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-times"></i> Очистить
            </button>
        `;
        searchResultsContainer.appendChild(resultsHeader);
        
        document.getElementById('clear-search').addEventListener('click', clearSearchResults);

        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'text-center my-5 loading-indicator';
        loadingIndicator.innerHTML = '<i class="fa fa-spinner fa-spin fa-3x"></i><p class="mt-2">Идет поиск...</p>';
        searchResultsContainer.appendChild(loadingIndicator);
        
        fetch(`/v2/api/search/videos?query=${encodeURIComponent(searchQuery)}`)
            .then(response => response.json())
            .then(data => {
                searchButton.innerHTML = 'Найти';
                searchButton.disabled = false;
                
                const loading = searchResultsContainer.querySelector('.loading-indicator');
                if (loading) {
                    loading.remove();
                }
                
                if (data.success && data.total > 0) {
                    const resultsCount = document.createElement('p');
                    resultsCount.className = 'text-muted mb-4';
                    resultsCount.textContent = `Найдено видео: ${data.total}`;
                    searchResultsContainer.appendChild(resultsCount);
                    
                    data.results.forEach(categoryResults => {
                        const categoryCard = document.createElement('div');
                        categoryCard.className = 'card mb-4';
                        categoryCard.innerHTML = `
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <a href="/v2/video/category/${categoryResults.category.id}" class="category-link text-decoration-none">
                                        ${categoryResults.category.name}
                                        <i class="fa fa-arrow-right ms-2 small"></i>
                                    </a>
                                </h5>
                                <span class="badge bg-primary">${categoryResults.videos.length}</span>
                            </div>
                            <div class="card-body">
                                <div class="row videos-container">
                                    ${categoryResults.videos.map(video => createVideoCardHtml(video)).join('')}
                                </div>
                            </div>
                        `;
                        searchResultsContainer.appendChild(categoryCard);
                    });
                } else {
                    const noResults = document.createElement('div');
                    noResults.className = 'alert alert-info';
                    noResults.textContent = 'К сожалению, по вашему запросу ничего не найдено.';
                    searchResultsContainer.appendChild(noResults);
                }
            })
            .catch(error => {
                console.error('Ошибка при поиске видео:', error);
                searchButton.innerHTML = 'Найти';
                searchButton.disabled = false;

                const loading = searchResultsContainer.querySelector('.loading-indicator');
                if (loading) {
                    loading.remove();
                }
                const errorMsg = document.createElement('div');
                errorMsg.className = 'alert alert-danger';
                errorMsg.textContent = 'Произошла ошибка во время поиска. Попробуйте еще раз.';
                searchResultsContainer.appendChild(errorMsg);
            });
    }

    function clearSearchResults() {
        const searchInput = document.getElementById('videoSearch');
        const searchResultsContainer = document.getElementById('search-results');
        const categoryList = document.querySelector('.category-list');
        
        searchInput.value = '';

        if (searchResultsContainer) {
            searchResultsContainer.remove();
        }
        if (categoryList) {
            categoryList.style.display = '';
        }
    }
    
    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
}


function initVideoPlayer() {
    // Эта функция нужна для страницы просмотра видео, оставляем пустой здесь
}

function createVideoCardHtml(video) {
    const thumbnailUrl = video.thumbnail_url || '{{ asset("img/no-video.jpg") }}';
    const videoUrl = `/v2/video/${video.id}`;
    return `
        <div class="col-md-4 mb-4 video-item">
            <div class="video-v2-card">
                <a href="${videoUrl}" class="video-v2-image" style="background-image: url('${thumbnailUrl}')">
                    <span class="video-v2-play"><i class="fa fa-play-circle"></i></span>
                </a>
                <div class="video-v2-info">
                    <h2 class="video-v2-title"><a href="${videoUrl}">${video.title}</a></h2>
                    ${video.created_at ? `<p class='video-v2-date'><svg width='16' height='16' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z' stroke='#613482' stroke-width='1.5' stroke-miterlimit='10' stroke-linecap='round' stroke-linejoin='round'/><path d='M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329' stroke='#613482' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg> ${video.created_at}</p>` : ''}
                    ${video.speakers ? `<p class='video-v2-speakers'><svg width='16' height='16' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z' stroke='#613482' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/><path d='M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z' stroke='#613482' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/><path d='M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z' stroke='#613482' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg> ${video.speakers}</p>` : ''}
                    <div class="video-v2-actions">
                        <a href="${videoUrl}" class="video-v2-btn">Смотреть</a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function saveExpandState(categoryId, isExpanded) {
    try {
        let states = JSON.parse(localStorage.getItem('videolibrary_expand_states')) || {};
        states[categoryId] = isExpanded;
        localStorage.setItem('videolibrary_expand_states', JSON.stringify(states));
    } catch (e) {
        console.error("Could not save state to localStorage", e);
    }
}

function loadExpandState(categoryId) {
    try {
        const states = JSON.parse(localStorage.getItem('videolibrary_expand_states')) || {};
        return states.hasOwnProperty(categoryId) ? states[categoryId] : true;
    } catch (e) {
        console.error("Could not load state from localStorage", e);
        return true; 
    }
}
</script>
@endpush

@section('styles')
<style>
    .videolibrary.v2 .category-header {
        cursor: pointer;
        background-color: #f8f9fa;
    }
    
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
    
    .videolibrary.v2 .category-card {
        transition: all 0.3s ease;
    }
    
    .videolibrary.v2 .toggle-category {
        padding: 0;
        color: #6c757d;
    }
    
    #no-search-results {
        margin-bottom: 20px;
    }
    
    /* Стили для результатов поиска */
    .search-results {
        margin-bottom: 2rem;
    }
    
    .search-results .badge {
        font-size: 0.8rem;
    }

    .category-card-modern {
        height: 100%;
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
    
    /* Стили для кликабельных категорий в поиске */
    .category-link {
        color: #613482;
        transition: color 0.2s;
        display: inline-flex;
        align-items: center;
    }
    
    .category-link:hover {
        color: #7e57c2;
    }
    
    .category-link .fa {
        opacity: 0;
        transform: translateX(-5px);
        transition: all 0.2s ease-in-out;
    }
    
    .category-link:hover .fa {
        opacity: 1;
        transform: translateX(0);
    }

    .category-list.row {
        display: flex;
        flex-wrap: wrap;
    }
    .col-md-4.col-sm-6.mb-4 {
        display: flex;
        align-items: stretch;
    }
    .category-card-modern {
        width: 100%;
    }
    
    .category-card-modern {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(97,52,130,0.08);
        border: 1.5px solid #eee;
        padding: 22px 20px 16px 20px;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s, border-color 0.2s;
        min-height: 140px;
        position: relative;
    }
    .category-card-modern:hover {
        box-shadow: 0 4px 32px rgba(97,52,130,0.18);
        border-color: #b39ddb;
    }
    .category-card-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: color 0.2s;
    }
    .category-card-link:hover .category-card-title {
        color: #7e57c2;
    }
    .category-card-icon {
        font-size: 2.2rem;
        color: #613482;
        margin-right: 18px;
        width: 44px;
        text-align: center;
    }
    .category-card-info {
        flex-grow: 1;
        overflow: hidden; /* Fix for ellipsis */
    }
    .category-card-title {
        font-size: 1.18rem;
        font-weight: 600;
        color: #613482;
        margin-bottom: 4px;
        transition: color 0.2s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .category-card-count {
        font-size: 0.95rem;
        color: #6c757d;
    }
    .category-card-arrow {
        font-size: 1.2rem;
        color: #613482;
        margin-left: 15px;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .category-card-link:hover .category-card-arrow {
        opacity: 1;
    }
    .subcategory-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .subcategory-badge {
        display: inline-flex;
        align-items: center;
        background: #ede7f6;
        color: #613482;
        border-radius: 8px;
        padding: 3px 10px 3px 9px; /* уменьшен padding */
        font-size: 0.97rem;
        text-decoration: none;
        font-weight: 500;
        transition: background 0.18s, color 0.18s;
        border: 1px solid #e0e0e0;
        height: 32px;
    }
    .subcategory-badge:hover {
        background: #d1c4e9;
        color: #7e57c2;
        border-color: #b39ddb;
    }
    .subcategory-badge-title {
        margin-right: 7px;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
    }
    .subcategory-badge-count {
        background: #b39ddb;
        color: #fff;
        border-radius: 6px;
        font-size: 0.92em;
        padding: 0 7px;
        margin-left: 2px;
        font-weight: 600;
        display: flex;
        align-items: center;
        height: 22px;
        line-height: 1;
    }
</style>
@endsection 