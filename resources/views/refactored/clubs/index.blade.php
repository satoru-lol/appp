@extends('app')

@section('title', $title ?? 'Онлайн-клубы - АЧПП')
@section('description', $description ?? 'Присоединяйтесь к онлайн-клубам АЧПП для профессионального общения и развития')

@section('content')
<style>
    .clubs-hero {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    .club-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    .club-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 48px rgba(97, 52, 130, 0.2);
    }
    .club-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
    }
    .club-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
    }
    .club-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    .club-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
    }
    .club-participants {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .club-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-club-join {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        width: 100%;
    }
    .btn-club-join:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(97, 52, 130, 0.3);
        color: white;
    }
    .btn-club-joined {
        background: #28a745;
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        width: 100%;
    }
    .search-section {
        background: #f8f9fa;
        padding: 2rem 0;
    }
    .search-form {
        max-width: 600px;
        margin: 0 auto;
    }
    .tabs-nav {
        border-bottom: 2px solid #f0f0f0;
        margin-bottom: 2rem;
    }
    .tab-link {
        padding: 1rem 1.5rem;
        color: #6c757d;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    .tab-link.active {
        color: #613482;
        border-bottom-color: #613482;
    }
    .tab-link:hover {
        color: #613482;
    }
</style>

<!-- Hero Section -->
<section class="clubs-hero">
    <div class="container">
        <h1 class="display-4 mb-4">Онлайн-клубы</h1>
        <p class="lead mb-4">
            Присоединяйтесь к профессиональным клубам для обмена опытом и развития
        </p>
        @auth
            <a href="{{ route('v2.refactored.clubs.my') }}" class="btn btn-light btn-lg me-3">
                <i class="bi bi-people me-2"></i>Мои клубы
            </a>
        @else
            <a href="{{ route('v2.refactored.auth.register') }}" class="btn btn-light btn-lg me-3">
                <i class="bi bi-person-plus me-2"></i>Присоединиться
            </a>
        @endauth
        <a href="{{ route('v2.refactored.clubs.search') }}" class="btn btn-outline-light btn-lg">
            <i class="bi bi-search me-2"></i>Найти клуб
        </a>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <form action="{{ route('v2.refactored.clubs.search') }}" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" 
                       placeholder="Поиск клубов по названию или описанию..." 
                       value="{{ request('q') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<div class="container py-5">
    <!-- Navigation Tabs -->
    <div class="tabs-nav d-flex justify-content-center mb-4">
        <a href="#all-clubs" class="tab-link active" data-tab="all-clubs">Все клубы</a>
        <a href="#upcoming" class="tab-link" data-tab="upcoming">Предстоящие</a>
        @auth
            <a href="#my-clubs" class="tab-link" data-tab="my-clubs">Мои клубы</a>
        @endauth
    </div>

    <!-- All Clubs Tab -->
    <div class="tab-content active" id="all-clubs">
        <h2 class="text-center mb-4">Все клубы</h2>
        @if($clubs && $clubs->count() > 0)
            <div class="row">
                @foreach($clubs as $club)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="club-card">
                            @if($club->image)
                                <img src="{{ asset('storage/' . $club->image) }}" 
                                     alt="{{ $club->title }}" class="club-image">
                            @else
                                <div class="club-image bg-gradient d-flex align-items-center justify-content-center" 
                                     style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%);">
                                    <i class="bi bi-people text-white" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <h3 class="club-title">{{ $club->title }}</h3>
                            <p class="club-description">
                                {{ Str::limit(strip_tags($club->description), 100) }}
                            </p>
                            
                            <div class="club-meta">
                                <div class="club-participants">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $club->participants_count ?? 0 }} участников</span>
                                </div>
                                @if(isset($club->formatted_date))
                                    <div class="club-date">
                                        <i class="bi bi-calendar"></i>
                                        <span>{{ $club->formatted_date }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('v2.refactored.clubs.show', $club->id) }}" 
                                   class="btn btn-outline-primary flex-grow-1">
                                    Подробнее
                                </a>
                                @auth
                                    @if(isset($club->is_participant) && $club->is_participant)
                                        <button class="btn btn-club-joined" disabled>
                                            <i class="bi bi-check me-1"></i>Участвую
                                        </button>
                                    @else
                                        <form action="{{ route('v2.refactored.clubs.join', $club->id) }}" 
                                              method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-club-join">
                                                <i class="bi bi-plus me-1"></i>Присоединиться
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('v2.refactored.auth.login') }}" 
                                       class="btn btn-club-join flex-grow-1">
                                        Войти для участия
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Клубы не найдены</h4>
                <p class="text-muted">В данный момент нет доступных клубов для вашего уровня</p>
            </div>
        @endif
    </div>

    <!-- Upcoming Clubs Tab -->
    <div class="tab-content" id="upcoming">
        <h2 class="text-center mb-4">Предстоящие встречи</h2>
        @if($upcomingClubs && $upcomingClubs->count() > 0)
            <div class="row">
                @foreach($upcomingClubs as $club)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="club-card">
                            @if($club->image)
                                <img src="{{ asset('storage/' . $club->image) }}" 
                                     alt="{{ $club->title }}" class="club-image">
                            @else
                                <div class="club-image bg-gradient d-flex align-items-center justify-content-center" 
                                     style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%);">
                                    <i class="bi bi-calendar-event text-white" style="font-size: 3rem;"></i>
                                </div>
                            @endif
                            
                            <h3 class="club-title">{{ $club->title }}</h3>
                            <p class="club-description">
                                {{ Str::limit(strip_tags($club->description), 100) }}
                            </p>
                            
                            <div class="club-meta">
                                <div class="club-date">
                                    <i class="bi bi-clock"></i>
                                    <span>{{ $club->formatted_date }} {{ $club->formatted_time ?? '' }}</span>
                                </div>
                                <div class="club-participants">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $club->participants_count ?? 0 }}</span>
                                </div>
                            </div>
                            
                            <a href="{{ route('v2.refactored.clubs.show', $club->id) }}" 
                               class="btn btn-club-join">
                                Подробнее
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calendar-x display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Нет предстоящих встреч</h4>
                <p class="text-muted">Скоро появятся новые интересные клубы</p>
            </div>
        @endif
    </div>

    <!-- My Clubs Tab -->
    @auth
        <div class="tab-content" id="my-clubs">
            <h2 class="text-center mb-4">Мои клубы</h2>
            @if($userClubs && $userClubs->count() > 0)
                <div class="row">
                    @foreach($userClubs as $club)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="club-card">
                                @if($club->image)
                                    <img src="{{ asset('storage/' . $club->image) }}" 
                                         alt="{{ $club->title }}" class="club-image">
                                @else
                                    <div class="club-image bg-gradient d-flex align-items-center justify-content-center" 
                                         style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%);">
                                        <i class="bi bi-star text-white" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                <h3 class="club-title">{{ $club->title }}</h3>
                                <p class="club-description">
                                    {{ Str::limit(strip_tags($club->description), 100) }}
                                </p>
                                
                                <div class="club-meta">
                                    <div class="club-participants">
                                        <i class="bi bi-people"></i>
                                        <span>{{ $club->participants_count ?? 0 }} участников</span>
                                    </div>
                                    @if(isset($club->formatted_date))
                                        <div class="club-date">
                                            <i class="bi bi-calendar"></i>
                                            <span>{{ $club->formatted_date }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <a href="{{ route('v2.refactored.clubs.show', $club->id) }}" 
                                       class="btn btn-outline-primary flex-grow-1">
                                        Подробнее
                                    </a>
                                    <form action="{{ route('v2.refactored.clubs.leave', $club->id) }}" 
                                          method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100"
                                                onclick="return confirm('Вы уверены, что хотите покинуть клуб?')">
                                            <i class="bi bi-box-arrow-right me-1"></i>Покинуть
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-person-x display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Вы пока не участвуете в клубах</h4>
                    <p class="text-muted">Присоединяйтесь к интересным клубам для профессионального развития</p>
                    <a href="#all-clubs" class="btn btn-primary" data-tab="all-clubs">
                        Найти клубы
                    </a>
                </div>
            @endif
        </div>
    @endauth
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение табов
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetTab = this.getAttribute('data-tab');
            
            // Убираем активные классы
            tabLinks.forEach(l => l.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));
            
            // Добавляем активные классы
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
            
            // Обновляем URL
            window.history.pushState({}, '', '#' + targetTab);
        });
    });
    
    // Проверяем hash в URL при загрузке
    const hash = window.location.hash.substring(1);
    if (hash) {
        const targetLink = document.querySelector(`[data-tab="${hash}"]`);
        if (targetLink) {
            targetLink.click();
        }
    }
    
    // Анимация появления карточек
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, observerOptions);

    // Применяем анимацию к карточкам
    const cards = document.querySelectorAll('.club-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>
@endsection