@extends('app')

@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('v2.club.index') }}">Клубы</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $club->title }}</li>
        </ol>
    </nav>
</div>
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
    .club-v2-page-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 15px 32px;
    }
</style>
<div class="club-v2-page-container">
    <div class="club-v2-page">
        {{--
        @if(!empty($club->image))
            <img src="/images/{{ $club->image }}" alt="{{ $club->title }}" class="club-v2-page-image">
        @endif
        --}}
        <h1 class="club-v2-page-title">{{ $club->title }}</h1>
        
        <div class="club-v2-page-meta">
            @if(!empty($club->clubDates->date))
                <div class="club-v2-meta-item">
                    <i class="fa fa-calendar"></i>
                    {{ date('d.m.Y', strtotime($club->clubDates->date)) }}
                </div>
                <div class="club-v2-meta-item">
                    <i class="fa fa-clock-o"></i>
                    {{ date('H:i', strtotime($club->clubDates->start_time)) }} - {{ date('H:i', strtotime($club->clubDates->end_time)) }}
                </div>
            @endif
            @if(!empty($club->speakers))
                <div class="club-v2-meta-item">
                    <i class="fa fa-user"></i>
                    {{ $club->speakers }}
                </div>
            @endif
        </div>
        
        <div class="club-v2-page-content">
            {!! $club->text !!}
        </div>
        
        @if(!empty($club->product_level))
        <div class="club-v2-page-price-card">
            <div class="price-info">
                @if($product)
                    Стоимость: <strong>{{$product->price}} р</strong>
                @endif
            </div>
            <div class="price-actions">
                @if ($subscriptionStatus->hasClubAccess)
                    <button class="club-v2-btn-disabled" disabled>Доступ по подписке</button>
                @else
                    <a href="#" data-bs-toggle="modal" data-bs-target="#subscriptionModal" class="club-v2-btn">Вступить в клуб</a>
                @endif
            </div>
        </div>
        @endif
        
        <div class="club-v2-page-actions">
            <a href="{{ route('v2.club.index') }}" class="club-v2-btn-secondary">Назад к списку</a>
            @if ($subscriptionStatus->hasClubAccess)
                <a href="{{ $club->feedback }}" target="_blank" class="club-v2-btn">Подключиться к клубу</a>
            @else
                <a href="#" data-bs-toggle="modal" data-bs-target="#subscriptionModal" class="club-v2-btn-disabled">Вступить в клуб</a>
            @endif
        </div>
    </div>
</div>
@endsection

<!-- Модальное окно для подписки -->
<div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionModalLabel">Доступ к клубу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Для доступа к клубным встречам необходима активная подписка соответствующего уровня.</p>
                <p>Перейдите в раздел "Подписки", чтобы выбрать подходящий тариф.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                {{-- <a href="{{ route('subscriptions') }}" class="club-v2-btn">Перейти к подпискам</a> --}}
            </div>
        </div>
    </div>
</div> 