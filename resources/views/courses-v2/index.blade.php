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
/* Стили для страницы списка курсов */
.courses-v2-container { max-width: 1200px; margin: 0 auto; padding: 32px 16px; }
.courses-v2-header { margin-bottom: 32px; }
.courses-v2-header h1 { color: #613482; margin-bottom: 16px; font-size: 2rem; }
.courses-v2-categories { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
.courses-v2-category { padding: 8px 16px; border-radius: 20px; background: #f5f5f5; color: #444; text-decoration: none; transition: all 0.2s; }
.courses-v2-category:hover { background: #e0e0e0; }
.courses-v2-category.active { background: #613482; color: white; }

.courses-v2-list { display: flex; flex-wrap: wrap; gap: 32px; }
.course-v2-card { background: #fff; border-radius: 18px; box-shadow: 0 2px 16px rgba(97,52,130,0.08); width: 340px; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .2s; border: 2px solid #eee; }
.course-v2-card:hover { box-shadow: 0 4px 32px rgba(97,52,130,0.18); border-color: #b39ddb; }
.course-v2-image { background: linear-gradient(120deg, #ede7f6 60%, #fff 100%); height: 180px; background-size: cover; background-position: center; }
.course-v2-info { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
.course-v2-info h2 { font-size: 1.2rem; color: #613482; margin-bottom: 12px; }
.course-v2-info p { color: #444; margin-bottom: 8px; display: flex; align-items: center; }
.course-v2-info p svg { margin-right: 8px; }
.course-v2-actions { margin-top: auto; padding-top: 16px; }
.course-v2-btn { display: inline-block; background: #613482; color: #fff; border-radius: 8px; padding: 8px 18px; text-decoration: none; transition: background .2s; }
.course-v2-btn:hover { background: #7e57c2; }

.courses-v2-empty { width: 100%; text-align: center; padding: 40px 0; }
.courses-v2-empty p { margin-bottom: 16px; color: #666; }

@media (max-width: 768px) {
    .courses-v2-list { justify-content: center; }
    .course-v2-card { width: 100%; max-width: 340px; }
}
</style>

<div class="courses-v2-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Курсы</li>
        </ol>
    </nav>
    <div class="courses-v2-header">
        <h1>{{ $title }}</h1>
    </div>
    
    <div class="courses-v2-list">
        @forelse($courses as $course)
            <div class="course-v2-card">
                {{--
                @if($course->course && $course->course->image)
                    <img src="/images/{{ $course->course->image }}" alt="{{ $course->course->title }}" class="img-fluid">
                @endif
                --}}
                <div class="course-v2-info">
                    <h2>{{ $course->title ?? ($course->course ? $course->course->title : 'Название курса') }}</h2>
                    <p class="course-v2-date">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#613482" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329" stroke="#613482" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($course->date)->format('d.m.Y') }}
                    </p>
                    <p class="course-v2-time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ $course->start_time ? \Carbon\Carbon::parse($course->start_time)->format('H:i') : '' }}
                        {{ $course->end_time ? ' - ' . \Carbon\Carbon::parse($course->end_time)->format('H:i') : '' }}
                    </p>
                    @if($course->speakers)
                    <p class="course-v2-speakers">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ $course->speakers }}
                    </p>
                    @endif
                    <div class="course-v2-actions" style="display: flex; gap: 8px;">
                        <a href="{{ route('courses-v2.show', $course->id) }}" class="course-v2-btn">Подробнее</a>
                        @if ($user && in_array($course->id, $participantCourseIds))
                            @if ($course->course && $course->course->feedback)
                                <a href="{{ $course->course->feedback }}" class="course-v2-btn" target="_blank" style="background-color: #28a745;">Подключиться</a>
                            @else
                                <button class="course-v2-btn" style="background-color: #6c757d; cursor: not-allowed;" disabled>Записан</button>
                            @endif
                        @else
                            <a href="{{ route('courses-v2.subscribe', $course->id) }}" class="course-v2-btn" style="background-color: #007bff;">Записаться</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="courses-v2-empty">
                <p>Курсы не найдены.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection 