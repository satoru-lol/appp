<div class="comment-card new-comment" style="margin-left: {{ $comment['id_com'] ? 40 : 0 }}px;" data-comment-id="{{ $comment['id'] }}">
    <div class="comment-avatar">
        <img class="js-user-avatar"
             data-user-id="{{ $comment['user']['id'] }}"
             data-user-phone="{{ $comment['user']['phone'] ?? '' }}"
             data-user-firstname="{{ $comment['user']['firstname'] ?? '' }}"
             data-user-lastname="{{ $comment['user']['lastname'] ?? '' }}"
             data-user-avatar="{{ $comment['user']['avatar'] ? 1 : 0 }}"
             alt="Аватар пользователя">
    </div>
    <div class="comment-body">
        <div class="d-flex justify-content-between">
            <span class="comment-author">
                {{ ($comment['user']['firstname'] ?? '') . ' ' . ($comment['user']['lastname'] ?? '') ?: 'Аноним' }}
            </span>
            <span class="comment-date">{{ $comment['created_at'] }}</span>
        </div>
        <p class="comment-text">{{ $comment['comment'] }}</p>
        @auth
        <button class="btn btn-sm btn-link reply-btn" data-comment-id="{{ $comment['id'] }}">Ответить</button>
        @endauth
    </div>
</div> 