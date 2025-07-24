@extends('app')

@section('title', $title ?? 'О нас - АЧПП')
@section('description', $description ?? 'Узнайте больше об Ассоциации частнопрактикующих психологов и психотерапевтов')

@section('content')
<style>
    .about-hero {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    .about-section {
        padding: 4rem 0;
    }
    .mission-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        text-align: center;
        height: 100%;
    }
    .mission-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }
</style>

<section class="about-hero">
    <div class="container">
        <h1 class="display-4 mb-4">О нашей Ассоциации</h1>
        <p class="lead">
            Мы объединяем профессионалов в области психологии и психотерапии для развития, обмена опытом и взаимной поддержки
        </p>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center mb-5">Наша миссия</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h4>Объединение</h4>
                            <p>Создаем профессиональное сообщество психологов и психотерапевтов</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="bi bi-book"></i>
                            </div>
                            <h4>Образование</h4>
                            <p>Предоставляем качественные курсы и материалы для профессионального развития</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="mission-card">
                            <div class="mission-icon">
                                <i class="bi bi-heart"></i>
                            </div>
                            <h4>Поддержка</h4>
                            <p>Оказываем взаимную поддержку и помощь коллегам по профессии</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-section bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2>Что мы предлагаем</h2>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Профессиональные курсы</strong> от ведущих специалистов
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Регулярные встречи</strong> и обмен опытом
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Онлайн-клубы</strong> по интересам
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Видеотека</strong> с записями лекций и семинаров
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Профессиональная поддержка</strong> коллег
                    </li>
                </ul>
                <a href="{{ route('v2.refactored.auth.register') }}" class="btn btn-primary btn-lg">
                    Присоединиться к нам
                </a>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="/img/about-illustration.svg" alt="О нас" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection