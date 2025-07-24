@php
    $title = $course && $course->title ? $course->title : ($courseInfo && $courseInfo->title ? $courseInfo->title : 'Курс');
@endphp

@extends('app')
@section('content')
<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('courses-v2.index') }}">Курсы</a></li>
            @if($course && $course->course && $course->course->course_category_id)
                @php
                    $category = \App\Models\CourseCategory::find($course->course->course_category_id);
                @endphp
                @if($category)
                    <li class="breadcrumb-item"><a href="{{ route('courses-v2.category', $category->id) }}">{{ $category->name }}</a></li>
                @endif
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
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
/* Стили для страницы отдельного курса */
.course-v2-page-container { max-width: 1100px; margin: 0 auto; padding: 40px 16px; }
.course-v2-page { display: flex; gap: 40px; background: #fff; border-radius: 18px; box-shadow: 0 2px 16px rgba(97,52,130,0.08); overflow: hidden; }
.course-v2-page-image { min-width: 340px; max-width: 340px; background: linear-gradient(120deg, #ede7f6 60%, #fff 100%); height: auto; }
.course-v2-page-image img { width: 100%; height: 100%; object-fit: cover; }
.course-v2-page-info { padding: 32px 24px; flex: 1; }
.course-v2-page-info h1 { color: #613482; font-size: 2rem; margin-bottom: 18px; }
.course-v2-page-info h2 { color: #613482; font-size: 1.4rem; margin: 24px 0 12px; }

.course-v2-page-meta { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
.course-v2-meta-item { display: flex; align-items: center; color: #444; }
.course-v2-meta-item svg { margin-right: 8px; }

.course-v2-page-description { margin-bottom: 24px; }
.course-v2-page-text { color: #444; line-height: 1.6; }

.course-v2-page-product { margin-bottom: 24px; padding: 16px; background: #f5f0ff; border-radius: 12px; }
.course-v2-product-info { color: #444; }
.course-v2-product-info p { margin-bottom: 8px; }

.course-v2-page-actions { display: flex; gap: 16px; margin-top: 32px; }
.course-v2-btn { display: inline-block; background: #613482; color: #fff; border-radius: 8px; padding: 10px 24px; text-decoration: none; transition: background .2s; }
.course-v2-btn:hover { background: #7e57c2; }
.course-v2-btn-secondary { display: inline-block; background: transparent; color: #613482; border: 1px solid #613482; border-radius: 8px; padding: 10px 24px; text-decoration: none; transition: all .2s; }
.course-v2-btn-secondary:hover { background: #f5f0ff; }

@media (max-width: 768px) {
    .course-v2-page { flex-direction: column; }
    .course-v2-page-image { min-width: 100%; max-width: 100%; height: 240px; }
    .course-v2-page-actions { flex-direction: column; }
}
</style>

<div class="course-v2-page-container">
    <div class="course-v2-page">
        {{--
        <div class="course-v2-page-image">
        </div>
        --}}
        <div class="course-v2-page-info">
            <h1>{{ $course->title ?? ($course->course ? $course->course->title : 'Название курса') }}</h1>

            
            <div class="course-v2-page-meta">
                @if($course && $course->date)
                <div class="course-v2-meta-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#613482" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329" stroke="#613482" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($course->date)->format('d.m.Y') }}</span>
                </div>
                @endif
                
                @if($course && ($course->start_time || $course->end_time))
                <div class="course-v2-meta-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>
                        {{ $course->start_time ? \Carbon\Carbon::parse($course->start_time)->format('H:i') : '' }}
                        {{ $course->end_time ? ' - ' . \Carbon\Carbon::parse($course->end_time)->format('H:i') : '' }}
                    </span>
                </div>
                @endif
                
                @if($course && $course->speakers)
                <div class="course-v2-meta-item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ $course->speakers }}</span>
                </div>
                @endif
            </div>
            
            @if($course && $course->description)
            <div class="course-v2-page-description">
                <h2>Описание</h2>
                <div class="course-v2-page-text">
                    {!! nl2br(e($course->description)) !!}
                </div>
            </div>
            @endif
            
            @if($courseInfo && $courseInfo->text)
            <div class="course-v2-page-description">
                <h2>Дополнительная информация</h2>
                <div class="course-v2-page-text">
                    {!! nl2br(e($courseInfo->text)) !!}
                </div>
            </div>
            @endif
            
            @if($product)
            <div class="course-v2-page-product">
                <h2>Доступно по подписке</h2>
                <div class="course-v2-product-info">
                    <p>Уровень: {{ $product->name }}</p>
                    <p>Стоимость: {{ $product->price }} руб.</p>
                </div>
            </div>
            @endif
            
            <div class="course-v2-page-actions">
                <a href="{{ route('courses-v2.index') }}" class="course-v2-btn-secondary">Назад к списку</a>
                @auth
                    @php
                        $isParticipant = \App\Models\ParticipantActions::where('object_id', $course->id)
                            ->where('object_name', 'courses')
                            ->where('user_id', auth()->id())
                            ->exists();
                        
                        $userSubscription = \App\Models\Subscription::where('user_id', auth()->id())->first();
                        $userLevel = $userSubscription ? $userSubscription->level : 0;
                        $hasAccess = ($userLevel == -1 || $userLevel > 0);
                        
                        if ($userLevel > 0 && $userLevel != -1) {
                            $productPermission = \App\Models\ProductPermission::join('products', 'products.id', '=', 'product_permissions.product_id')
                                ->where('products.level', $userLevel)
                                ->first();
                            
                            $hasAccess = $productPermission && $productPermission->course;
                        }
                    @endphp
                    
                    @if($isParticipant)
                        @if($course->course && $course->course->feedback)
                             <a href="{{ $course->course->feedback }}" class="course-v2-btn" target="_blank">Подключиться к занятию</a>
                        @else
                            <div class="course-v2-btn" style="background-color: #6c757d; cursor: not-allowed;">Вы записаны на курс</div>
                        @endif
                    @elseif($hasAccess)
                        <a href="{{ route('courses-v2.subscribe', $course->id) }}" class="course-v2-btn">Записаться</a>
                    @else
                        <a href="{{ route('v2.profile.index', ['tab' => 'profile']) }}" class="course-v2-btn">Подключить подписку</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="course-v2-btn">Войти для записи</a>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection 