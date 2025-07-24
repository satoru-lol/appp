@php
use App\Models\User;
use App\Models\Like;
use App\Models\Dislike;
use App\Models\ParticipantActions;
use App\Models\BlogComment;
@endphp

@extends('app')

@section('content')
<style>
    /* Стили для хлебных крошек и основного контейнера */
    .meetings-v2-show-container { max-width: 900px; margin: 32px auto; padding: 0 15px; }
    .breadcrumb {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(97,52,130,0.07);
        padding: 12px 22px;
        margin-bottom: 24px;
        font-size: 1.04rem;
        --bs-breadcrumb-divider-color: #b39ddb;
    }
    .breadcrumb-item + .breadcrumb-item::before { color: #b39ddb; font-size: 1.1em; padding: 0 6px; }
    .breadcrumb-item a { color: #613482; text-decoration: none; font-weight: 500; transition: color .18s; }
    .breadcrumb-item a:hover { color: #7e57c2; text-decoration: underline; }
    .breadcrumb-item.active { color: #7e57c2; font-weight: 600; }
    
    /* Стили для карточки встречи */
    .meeting-show-v2 { padding: 32px; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(80, 80, 120, 0.1); }
    .meeting-header .meeting-title { font-size: 2.2rem; font-weight: 700; margin-bottom: 12px; }
    .meeting-meta-info { display: flex; align-items: center; flex-wrap: wrap; gap: 12px 20px; color: #555; margin-bottom: 24px; font-size: 0.95rem; }
    .meeting-meta-info span { display: flex; align-items: center; gap: 6px; }
    .meeting-meta-info .likes-dislikes a { color: #555; text-decoration: none; }
    .meeting-meta-info .likes-dislikes img { width: 16px; }
    
    .meeting-details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin: 24px 0; }
    .detail-item { background: #f9f9f9; padding: 14px; border-radius: 8px; }
    .detail-item strong { color: #333; }
    
    .meeting-description { line-height: 1.7; color: #333; margin-bottom: 32px; }
    
    .meeting-actions .btn-v2 { border: none; border-radius: 8px; padding: 12px 24px; font-size: 1.1rem; font-weight: 600; color: #fff; cursor: pointer; transition: background 0.2s; text-decoration: none; }
    .btn-join { background: #6c63ff; } .btn-join:hover { background: #4b47b5; }
    .btn-cancel { background: #e74c3c; } .btn-cancel:hover { background: #b93222; }
    
    /* Стили для участников и комментариев */
    .participants-section, .comments-section { margin-top: 40px; }
    .section-title { 
        font-size: 1.6rem; 
        font-weight: 600; 
        margin-bottom: 16px; 
        border-bottom: 2px solid #f0f0f0; 
        padding-bottom: 8px; 
        color: #444;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .participants-list {
        list-style: none;
        padding: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    .participants-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f9f9f9;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    /* Стили для счетчиков */
    .counter-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #6c63ff;
        color: white;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 0.85rem;
        font-weight: 600;
        vertical-align: middle;
        box-shadow: 0 2px 5px rgba(108, 99, 255, 0.2);
    }
    
    /* Стили для кнопки "Показать еще" */
    .load-more-btn {
        display: block;
        width: 100%;
        background: #f5f5f5;
        border: none;
        border-radius: 8px;
        padding: 12px;
        margin-top: 16px;
        cursor: pointer;
        font-weight: 500;
        color: #555;
        transition: all 0.2s;
    }
    
    .load-more-btn:hover {
        background: #e9e9e9;
        color: #333;
    }
    
    .load-more-btn:active {
        transform: translateY(1px);
    }
    
    .load-more-btn i {
        margin-right: 8px;
    }
    
    /* Скрытые комментарии */
    .comment-card.hidden {
        display: none;
    }
    
    .participant-avatar img,
    .comment-avatar img {
        width: 32px;
        height: 32px;
        max-width: 32px;
        max-height: 32px;
        border-radius: 50%;
        background-color: #f0f0f0; /* Цвет фона для заглушки */
        opacity: 0; /* Скрываем до загрузки */
        transition: opacity 0.2s ease; /* Плавное появление */
    }

    .comment-card {
        background: #fff;
        border: 1px solid #e9e9e9;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }
    .comment-body { flex: 1; }
    .comment-author { font-weight: 600; }
    .comment-date { font-size: 0.85rem; color: #777; }
    .comment-text { margin-top: 8px; color: #333; }

    .add-comment-form textarea {
        resize: vertical;
        border-radius: 8px;
        border: 1px solid #ddd;
        padding: 10px;
    }
    .add-comment-form .btn-submit-comment {
        background-color: #6c63ff;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .add-comment-form .btn-submit-comment:hover {
        background-color: #574fdc;
    }
    .form-submit-wrapper {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    /* Стили для комментариев в стиле YouTube */
    .comments-section {
        margin-top: 40px;
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .comment-card {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
        padding: 0;
        background: transparent;
        border: none;
        border-radius: 0;
    }
    
    .comment-avatar {
        flex-shrink: 0;
    }
    
    .participant-avatar img,
    .comment-avatar img {
        width: 40px;
        height: 40px;
        max-width: 40px;
        max-height: 40px;
        border-radius: 50%;
        background-color: #f0f0f0;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .comment-body {
        flex: 1;
        min-width: 0;
    }
    
    .comment-header {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 4px;
    }
    
    .comment-author {
        font-weight: 500;
        font-size: 0.9rem;
        color: #0f0f0f;
    }
    
    .comment-date {
        font-size: 0.8rem;
        color: #606060;
    }
    
    .comment-text {
        font-size: 0.9rem;
        line-height: 1.4;
        color: #0f0f0f;
        margin-bottom: 8px;
        word-wrap: break-word;
    }
    
    .comment-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 4px;
    }
    
    .reply-btn {
        background: none;
        border: none;
        color: #606060;
        font-size: 0.8rem;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .reply-btn:hover {
        background-color: #f2f2f2;
        color: #0f0f0f;
    }
    
    .replies {
        margin-left: 52px;
        margin-top: 8px;
        border-left: 2px solid #e5e5e5;
        padding-left: 16px;
    }
    
    .replies .comment-card {
        margin-bottom: 12px;
    }
    
    .replies .comment-avatar img {
        width: 32px;
        height: 32px;
        max-width: 32px;
        max-height: 32px;
    }
    
    .replies .replies {
        margin-left: 40px;
    }
    
    .add-comment-form {
        margin-bottom: 24px;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    
    .add-comment-form textarea {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        font-size: 0.9rem;
        resize: vertical;
        min-height: 60px;
    }
    
    .add-comment-form textarea:focus {
        outline: none;
        border-color: #065fd4;
        box-shadow: 0 0 0 2px rgba(6, 95, 212, 0.2);
    }
    
    .form-submit-wrapper {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
    }
    
    .btn-submit-comment {
        background-color: #065fd4;
        color: white;
        border: none;
        border-radius: 18px;
        padding: 8px 16px;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-submit-comment:hover {
        background-color: #0356c2;
    }
    
    .btn-submit-comment:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    
    .btn-secondary {
        background-color: #f2f2f2;
        color: #606060;
        border: none;
        border-radius: 18px;
        padding: 8px 16px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .btn-secondary:hover {
        background-color: #e5e5e5;
    }
    
    /* Анимация для новых комментариев */
    .comment-card.new-comment {
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Скрытые комментарии */
    .comment-card.hidden {
        display: none;
    }
    
    .load-more-btn {
        display: block;
        width: 100%;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        margin-top: 16px;
        cursor: pointer;
        font-weight: 500;
        color: #606060;
        transition: all 0.2s;
        text-align: center;
    }
    
    .load-more-btn:hover {
        background: #e9ecef;
        color: #0f0f0f;
    }
    
    .no-comments-message {
        text-align: center;
        color: #606060;
        font-style: italic;
        padding: 20px;
    }
    
    /* Оптимизация для мобильных устройств */
    @media (max-width: 767px) {
        .comments-section {
            padding: 16px;
        }
        
        .comment-card {
            gap: 8px;
        }
        
        .comment-avatar img {
            width: 36px;
            height: 36px;
            max-width: 36px;
            max-height: 36px;
        }
        
        .replies {
            margin-left: 44px;
        }
        
        .comment-actions {
            gap: 12px;
        }
    }
    
    /* Стили для уведомлений */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        animation: slideInRight 0.3s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .likes-dislikes button {
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }
    
    .likes-dislikes button:hover {
        background-color: rgba(0,0,0,0.05);
    }
    
    .likes-dislikes button:active {
        transform: scale(0.95);
    }
    
    .likes-dislikes img {
        width: 16px;
        height: 16px;
    }
    
    .likes-count, .dislikes-count {
        font-size: 0.9rem;
        color: #555;
        min-width: 20px;
        text-align: center;
    }

    /* Оптимизация для мобильных устройств */
    @media (max-width: 767px) {
        .meeting-show-v2 {
            padding: 20px;
        }
        
        .meeting-header .meeting-title {
            font-size: 1.8rem;
        }
        
        .participants-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        }
        
        .participants-list li {
            margin-bottom: 8px;
        }
        
        .comment-card {
            padding: 12px;
        }
        
        .comment-avatar img {
            width: 32px;
            height: 32px;
        }
    }
</style>

<div class="meetings-v2-show-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
            <li class="breadcrumb-item"><a href="{{ route('v2.meetings.index') }}">Наши встречи</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($blog->name, 50) }}</li>
        </ol>
    </nav>
    
    <div class="meeting-show-v2">
        <div class="meeting-header">
            <h1 class="meeting-title">{{ $blog->name }}</h1>
            <div class="meeting-meta-info">
                <span><i class="fa fa-calendar-alt"></i> Опубликовано: {{ $blog->created_at->translatedFormat('d F Y') }}</span>
                <span><i class="fa fa-eye"></i> {{ $blog->views }}</span>
                @if(auth()->check())
                    <span class="likes-dislikes">
                        <button class="btn-like" data-post-id="{{ $blog->id }}" data-action="like">
                            <img src="{{ Like::where(['user_id' => auth()->id(), 'post_id' => $blog->id])->exists() ? '/img/like.png' : '/img/like_1.png' }}" alt="like">
                            <span class="likes-count">{{ Like::where('post_id', $blog->id)->count() }}</span>
                        </button>
                    </span>
                    <span class="likes-dislikes">
                        <button class="btn-dislike" data-post-id="{{ $blog->id }}" data-action="dislike">
                            <img src="{{ Dislike::where(['user_id' => auth()->id(), 'post_id' => $blog->id])->exists() ? '/img/dislike_1.png' : '/img/dislike.png' }}" alt="dislike">
                            <span class="dislikes-count">{{ Dislike::where('post_id', $blog->id)->count() }}</span>
                        </button>
                    </span>
                @endif
            </div>
        </div>

        {{-- @if($blog->image)
            <img src="{{ asset('img/blog/'.$blog->image) }}" alt="Изображение встречи" class="meeting-image" style="width:100%; border-radius:12px; margin-bottom:24px;">
        @endif --}}

        <div class="meeting-details-grid">
            <div class="detail-item"><strong>Дата проведения:</strong> {{ $formattedDate }}</div>
            <div class="detail-item"><strong>Формат:</strong> {{ $format->format ?? '-' }}</div>
            <div class="detail-item"><strong>Стоимость:</strong> {{ $blog->amount ?? 'Бесплатно' }}</div>
            <div class="detail-item"><strong>Продолжительность:</strong> 120 минут</div>
            <div class="detail-item"><strong>Организатор:</strong> {{ $blog->fio }}</div>
            @if(!empty($blog->feedback))
                 <div class="detail-item">
                    @if($blog->format_id == 1)
                        <strong><a href="{{ $blog->feedback }}" target="_blank" rel="noopener noreferrer">Ссылка на встречу</a></strong>
                    @elseif($blog->format_id == 2)
                        <strong>Место встречи:</strong> {{ $blog->feedback }}
                    @endif
                </div>
            @endif
        </div>

        @if($blog->blogContent && !empty(trim($blog->blogContent->text)))
        <div class="meeting-description">
            {!! $blog->blogContent->text !!}
        </div>
        @endif

        <div class="meeting-actions">
            @if(auth()->check())
                @if(ParticipantActions::where("object_name", "meeting")->where("object_id", $blog->id)->where("user_id", auth()->id())->exists())
                    <a href="{{ route('v2.meetings.cancelPart', ['id' => $blog->id]) }}" class="btn-v2 btn-cancel">Отменить участие</a>
                @elseif(auth()->id() != $blog->user_id && optional(auth()->user()->subscription)->level !== 0)
                     <a href="{{ route('v2.meetings.takePart', ['id' => $blog->id]) }}" class="btn-v2 btn-join">Принять участие</a>
                @endif
            @else
                <p><strong><a href="{{ route('login') }}">Войдите</a>, чтобы принять участие.</strong></p>
            @endif
        </div>
        
        @php
            $participants = ParticipantActions::where('object_name', 'meeting')->where('object_id', $blog->id)->with('user')->get();
        @endphp
        <div class="participants-section">
            <h3 class="section-title">Участники <span class="counter-badge">{{ $participants->count() }}</span></h3>
            @if($participants->isNotEmpty())
                <ul class="participants-list">
                    @foreach($participants as $participant)
                         <li>
                            <div class="participant-avatar">
                                @php
                                    $participantName = trim(($participant->user->firstname ?? '') . ' ' . ($participant->user->lastname ?? ''));
                                @endphp
                                <img class="js-user-avatar"
                                     data-user-id="{{ $participant->user->id }}"
                                     data-user-phone="{{ $participant->user->phone }}"
                                     data-user-firstname="{{ $participant->user->firstname }}"
                                     data-user-lastname="{{ $participant->user->lastname }}"
                                     data-user-avatar="{{ $participant->user->avatar ? 1 : 0 }}"
                                     alt="Аватар пользователя">
                            </div>
                            <span>{{ $participantName ?: 'Пользователь' }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Пока нет записавшихся участников.</p>
            @endif
        </div>

        @php
            $comments = BlogComment::where('blog_id', $blog->id)
                ->where(function ($query) {
                    $query->whereNull('id_com')->orWhere('id_com', 0);
                })
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->with(['user', 'replies' => function ($query) {
                    $query->with('user')
                          ->whereNotNull('comment')
                          ->where('comment', '!=', '')
                          ->orderBy('created_at', 'asc');
                }])
                ->latest()
                ->get();
            $commentsCount = BlogComment::where('blog_id', $blog->id)
                ->whereNotNull('comment')
                ->where('comment', '!=', '')
                ->count();
        @endphp
        <div class="comments-section">
            <h3 class="section-title">Комментарии <span class="counter-badge">{{ $commentsCount }}</span></h3>
            @auth
                <form id="commentForm" class="add-comment-form mb-4">
                    @csrf
                    <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                    <input type="hidden" name="id_com" value="">
                    <div class="mb-3">
                        <textarea name="comment" class="form-control" rows="3" placeholder="Напишите ваш комментарий..." required></textarea>
                    </div>
                    <div class="form-submit-wrapper">
                         <button type="submit" class="btn-submit-comment">
                            <i class="fa fa-paper-plane"></i>
                            Отправить
                        </button>
                    </div>
                </form>
            @else
                <p><a href="{{ route('login') }}">Войдите</a>, чтобы оставить комментарий.</p>
            @endauth

            <div id="commentsList">
            @forelse($comments as $index => $comment)
                    @include('v2.meetings.partials._comment', ['comment' => $comment, 'level' => 0])
            @empty
                    <p class="no-comments-message">Комментариев пока нет.</p>
            @endforelse
            </div>
            
            @if($comments->count() > 5)
                <button class="load-more-btn" id="loadMoreComments">
                    <i class="fa fa-chevron-down"></i> Показать еще комментарии
                </button>
            @endif
        </div>
    </div>
</div>

<script>
    // Скрипт можно вынести в отдельный файл, если он станет слишком большим
    document.addEventListener('DOMContentLoaded', function () {
        // Инициализация аватаров
        const initAvatars = () => {
    document.querySelectorAll('.js-user-avatar').forEach(function(img) {
                const user = {
            id: img.dataset.userId,
            phone: img.dataset.userPhone,
            firstname: img.dataset.userFirstname,
            lastname: img.dataset.userLastname,
            avatar: img.dataset.userAvatar == '1'
        };

                if (window.getUserAvatar) {
        img.src = window.getUserAvatar(user);
                   // Показываем изображение после установки src
                   img.onload = function() {
                       this.style.opacity = '1';
                   };
                   img.onerror = function() {
                       this.style.opacity = '1'; // Показываем даже при ошибке
                   };
                }
            });
        };
        initAvatars();

        // Кнопка "Показать еще"
    const loadMoreBtn = document.getElementById('loadMoreComments');
    if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
            const hiddenComments = document.querySelectorAll('.comment-card.hidden');
            let shown = 0;
                hiddenComments.forEach(function (comment, index) {
                    if (shown < 5) {
                    comment.classList.remove('hidden');
                    shown++;
                }
            });
            
                if (document.querySelectorAll('.comment-card.hidden').length === 0) {
                    loadMoreBtn.style.display = 'none';
            }
        });
    }

        // Обработка лайков/дизлайков
        const handleLikeDislike = (button) => {
            const postId = button.dataset.postId;
            const action = button.dataset.action;
            const url = action === 'like' ? `/v2/meetings/${postId}/like` : `/v2/meetings/${postId}/dislike`;
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.likes-count').textContent = data.likes_count;
                    document.querySelector('.dislikes-count').textContent = data.dislikes_count;
                    
                    const likeBtn = document.querySelector('.btn-like img');
                    const dislikeBtn = document.querySelector('.btn-dislike img');

                    likeBtn.src = data.user_liked ? '/img/like.png' : '/img/like_1.png';
                    dislikeBtn.src = data.user_disliked ? '/img/dislike_1.png' : '/img/dislike.png';
                }
            })
            .catch(error => console.error('Error:', error));
        };

        document.querySelectorAll('.btn-like, .btn-dislike').forEach(button => {
            button.addEventListener('click', () => handleLikeDislike(button));
        });

        // Обработка формы комментария
        const commentForm = document.getElementById('commentForm');
        const commentsList = document.getElementById('commentsList');

        if (commentForm && commentsList) {
            const parentIdInput = commentForm.querySelector('input[name="id_com"]');
            const formTextarea = commentForm.querySelector('textarea');
            const originalPlaceholder = formTextarea.placeholder;
            const formContainer = commentForm.parentElement;
            let cancelReplyBtn = null; // Для кнопки "Отменить ответ"

            // Функция для отмены ответа
            const cancelReply = () => {
                parentIdInput.value = '';
                formTextarea.placeholder = originalPlaceholder;
                formContainer.prepend(commentForm);
                if (cancelReplyBtn) {
                    cancelReplyBtn.remove();
                    cancelReplyBtn = null;
                }
            };

            // Обработчик для кнопок "Ответить"
            commentsList.addEventListener('click', function(e) {
                if (e.target.classList.contains('reply-btn')) {
                    e.preventDefault();
                    const commentId = e.target.dataset.commentId;
                    const commentCard = e.target.closest('.comment-card');
                    
                    // Сначала перемещаем форму
                    commentCard.after(commentForm);
                    
                    // Затем устанавливаем значения и фокус
                    parentIdInput.value = commentId;
                    formTextarea.placeholder = 'Напишите ваш ответ...';
                    formTextarea.focus();
                    
                    // Плавно прокручиваем к форме
                    commentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Добавить кнопку "Отмена"
                    if (!cancelReplyBtn) {
                        cancelReplyBtn = document.createElement('button');
                        cancelReplyBtn.textContent = 'Отменить ответ';
                        cancelReplyBtn.className = 'btn btn-sm btn-secondary';
                        cancelReplyBtn.type = 'button';
                        commentForm.querySelector('.form-submit-wrapper').prepend(cancelReplyBtn);
                        
                        cancelReplyBtn.addEventListener('click', cancelReply);
                    }
                }
            });

            commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
                const commentText = formTextarea.value.trim();
                if (!commentText) {
                    // Показываем уведомление об ошибке
                    alert('Комментарий не может быть пустым');
                    return;
                }

                const formData = new FormData(this);
                const parentId = formData.get('id_com');
                const submitBtn = this.querySelector('.btn-submit-comment');
                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...';


                fetch('{{ route("v2.meetings.addComment", ["id" => $blog->id]) }}', {
                method: 'POST',
                    body: formData,
                headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                        
                        let commentData = data.comment;
                        
                        const newCommentHtml = `
                            <div class="comment-card new-comment" data-comment-id="${commentData.id}">
                                <div class="comment-avatar">
                                    <img class="js-user-avatar"
                                         data-user-id="${commentData.user.id}"
                                         data-user-phone="${commentData.user.phone || ''}"
                                         data-user-firstname="${commentData.user.firstname || ''}"
                                         data-user-lastname="${commentData.user.lastname || ''}"
                                         data-user-avatar="${commentData.user.avatar ? 1 : 0}"
                                         alt="Аватар пользователя">
                                </div>
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="comment-author">
                                            ${(commentData.user.firstname + ' ' + commentData.user.lastname).trim() || 'Аноним'}
                                        </span>
                                        <span class="comment-date">${commentData.created_at}</span>
                                    </div>
                                    <div class="comment-text">${commentData.comment}</div>
                                    <div class="comment-actions">
                                        @auth
                                        <button class="reply-btn" data-comment-id="${commentData.id}">Ответить</button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        `;


                        if (parentId) {
                            const parentComment = commentsList.querySelector(`.comment-card[data-comment-id="${parentId}"]`);
                            let repliesContainer = parentComment.nextElementSibling;
                            if (!repliesContainer || !repliesContainer.classList.contains('replies')) {
                                repliesContainer = document.createElement('div');
                                repliesContainer.className = 'replies';
                                repliesContainer.style.marginLeft = '40px'; // Или другой отступ
                                parentComment.after(repliesContainer);
                            }
                            repliesContainer.insertAdjacentHTML('beforeend', newCommentHtml);
                        } else {
                             const noCommentsMessage = commentsList.querySelector('.no-comments-message');
                             if(noCommentsMessage) noCommentsMessage.remove();
                             commentsList.insertAdjacentHTML('afterbegin', newCommentHtml);
                    }
                    
                        // Сброс формы и возврат на место
                        cancelReply();
                        commentForm.reset();
                        initAvatars(); // Переинициализация аватаров для нового комментария
                        
                        // Обновляем счетчик
                        const counter = document.querySelector('.comments-section .counter-badge');
                        counter.textContent = parseInt(counter.textContent) + 1;

                    } else {
                        // Обработка ошибок валидации
                        alert(data.message || 'Произошла ошибка.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                    alert('Не удалось отправить комментарий.');
            })
            .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                });
            });
    }
});
</script> 
@endsection 