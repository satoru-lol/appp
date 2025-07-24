@extends('app')

@section('title', $title ?? 'АЧПП - Ассоциация частнопрактикующих психологов и психотерапевтов')
@section('description', $description ?? 'Профессиональное развитие психологов: курсы, встречи, клубы, видеотека')
@section('keywords', $keywords ?? 'психология, психотерапия, курсы психологов, АЧПП')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
        padding: 4rem 0;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('/img/hero-pattern.svg') no-repeat center center;
        background-size: cover;
        opacity: 0.1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
    }
    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        line-height: 1.2;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.9;
    }
    .hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    .hero-stat {
        text-align: center;
    }
    .hero-stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        display: block;
    }
    .hero-stat-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 3rem;
        color: #333;
        position: relative;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        border-radius: 2px;
    }
    .card-modern {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        height: 100%;
    }
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 48px rgba(97, 52, 130, 0.2);
    }
    .card-modern .card-img-top {
        height: 200px;
        object-fit: cover;
    }
    .card-modern .card-body {
        padding: 1.5rem;
    }
    .card-modern .card-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
    }
    .specialist-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    .specialist-card:hover {
        transform: translateY(-5px);
    }
    .specialist-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 1rem;
        border: 3px solid #613482;
    }
    .specialist-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .specialist-degree {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    .specialist-rating {
        color: #ffc107;
        margin-bottom: 1rem;
    }
    .btn-primary-modern {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-primary-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(97, 52, 130, 0.3);
    }
    .btn-outline-modern {
        border: 2px solid #613482;
        color: #613482;
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-outline-modern:hover {
        background: #613482;
        color: white;
        transform: translateY(-2px);
    }
    .search-section {
        background: #f8f9fa;
        padding: 3rem 0;
    }
    .search-form {
        max-width: 600px;
        margin: 0 auto;
    }
    .search-input {
        border-radius: 0.5rem 0 0 0.5rem;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
    .search-btn {
        border-radius: 0 0.5rem 0.5rem 0;
        background: #613482;
        border: 1px solid #613482;
        color: white;
        padding: 0.75rem 1.5rem;
    }
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }
        .hero-subtitle {
            font-size: 1rem;
        }
        .section-title {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Ассоциация частнопрактикующих<br>
                        психологов и психотерапевтов
                    </h1>
                    <p class="hero-subtitle">
                        Пространство для профессионального роста, обмена опытом и взаимной поддержки специалистов в области психологии и психотерапии
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        @guest
                            <a href="{{ route('v2.refactored.auth.register') }}" class="btn btn-primary-modern">
                                <i class="bi bi-person-plus me-2"></i>Присоединиться
                            </a>
                            <a href="{{ route('v2.refactored.auth.login') }}" class="btn btn-outline-modern">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
                            </a>
                        @else
                            <a href="{{ route('v2.refactored.profile.index') }}" class="btn btn-primary-modern">
                                <i class="bi bi-person-circle me-2"></i>Мой профиль
                            </a>
                            <a href="{{ route('v2.refactored.courses.index') }}" class="btn btn-outline-modern">
                                <i class="bi bi-book me-2"></i>Курсы
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number">{{ $stats['users_count'] ?? 0 }}</span>
                        <span class="hero-stat-label">Участников</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">{{ $stats['specialists_count'] ?? 0 }}</span>
                        <span class="hero-stat-label">Специалистов</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">{{ $stats['courses_count'] ?? 0 }}</span>
                        <span class="hero-stat-label">Курсов</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">{{ $stats['videos_count'] ?? 0 }}</span>
                        <span class="hero-stat-label">Видео</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h3 class="text-center mb-4">Найти нужную информацию</h3>
                <form action="{{ route('v2.refactored.home.search') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control search-input" 
                               placeholder="Поиск по курсам, специалистам, статьям..." 
                               value="{{ request('q') }}">
                        <button type="submit" class="btn search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Top Specialists -->
@if($specialists && $specialists->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Наши специалисты</h2>
        <div class="row">
            @foreach($specialists as $specialist)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="specialist-card">
                        <img src="{{ $specialist->user->avatar ? asset('storage/' . $specialist->user->avatar) : asset('img/default-avatar.png') }}" 
                             alt="{{ $specialist->user->firstname }}" class="specialist-avatar">
                        <h5 class="specialist-name">
                            {{ $specialist->user->firstname }} {{ $specialist->user->lastname }}
                        </h5>
                        <p class="specialist-degree">{{ $specialist->degree ?? 'Психолог' }}</p>
                        <div class="specialist-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $specialist->rating >= $i ? '-fill' : '' }}"></i>
                            @endfor
                            <span class="ms-2">{{ number_format($specialist->rating, 1) }}</span>
                        </div>
                        <p class="text-muted small">
                            Опыт: {{ $specialist->experience }} лет<br>
                            {{ $specialist->location }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('v2.refactored.specialists.index') }}" class="btn btn-outline-modern">
                Все специалисты
            </a>
        </div>
    </div>
</section>
@endif

<!-- Popular Courses -->
@if($courses && $courses->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title">Популярные курсы</h2>
        <div class="row">
            @foreach($courses as $course)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card card-modern">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" class="card-img-top" alt="{{ $course->title }}">
                        @else
                            <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                                 style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%); height: 200px;">
                                <i class="bi bi-book text-white" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text text-muted">
                                {{ Str::limit($course->description, 100) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-eye me-1"></i>{{ $course->views }} просмотров
                                </small>
                                <a href="{{ route('v2.refactored.courses.show', $course->id) }}" 
                                   class="btn btn-primary-modern btn-sm">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('v2.refactored.courses.index') }}" class="btn btn-outline-modern">
                Все курсы
            </a>
        </div>
    </div>
</section>
@endif

<!-- Latest Blogs -->
@if($blogs && $blogs->count() > 0)
<section class="py-5">
    <div class="container">
        <h2 class="section-title">Последние статьи</h2>
        <div class="row">
            @foreach($blogs as $blog)
                <div class="col-lg-6 mb-4">
                    <div class="card card-modern">
                        <div class="row g-0">
                            <div class="col-md-4">
                                @if($blog->image)
                                    <img src="{{ asset('storage/' . $blog->image) }}" class="img-fluid rounded-start h-100" alt="{{ $blog->title }}">
                                @else
                                    <div class="bg-gradient d-flex align-items-center justify-content-center h-100 rounded-start" 
                                         style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%);">
                                        <i class="bi bi-file-text text-white" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $blog->title }}</h5>
                                    <p class="card-text">{{ Str::limit($blog->description, 80) }}</p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            {{ $blog->created_at->format('d.m.Y') }}
                                        </small>
                                    </p>
                                    <a href="{{ route('v2.refactored.blogs.show', $blog->id) }}" 
                                       class="btn btn-primary-modern btn-sm">
                                        Читать
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('v2.refactored.blogs.index') }}" class="btn btn-outline-modern">
                Все статьи
            </a>
        </div>
    </div>
</section>
@endif

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, #613482 0%, #4a276b 100%); color: white;">
    <div class="container text-center">
        <h2 class="mb-4">Готовы присоединиться к нашему сообществу?</h2>
        <p class="lead mb-4">
            Получите доступ к эксклюзивным курсам, встречам с коллегами и профессиональной поддержке
        </p>
        @guest
            <a href="{{ route('v2.refactored.auth.register') }}" class="btn btn-light btn-lg me-3">
                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
            </a>
            <a href="{{ route('v2.refactored.home.about') }}" class="btn btn-outline-light btn-lg">
                Узнать больше
            </a>
        @else
            <a href="{{ route('v2.refactored.profile.index', ['tab' => 'subscription']) }}" class="btn btn-light btn-lg me-3">
                <i class="bi bi-credit-card me-2"></i>Оформить подписку
            </a>
            <a href="{{ route('v2.refactored.meetings.index') }}" class="btn btn-outline-light btn-lg">
                Наши встречи
            </a>
        @endguest
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анимация появления карточек при скролле
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
    const cards = document.querySelectorAll('.card-modern, .specialist-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
@endsection