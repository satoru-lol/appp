<div class="comment-card" data-comment-id="{{ $comment->id }}">
    <div class="comment-avatar">
        <img class="js-user-avatar"
             data-user-id="{{ $comment->user->id ?? 0 }}"
             data-user-phone="{{ $comment->user->phone ?? '' }}"
             data-user-firstname="{{ $comment->user->firstname ?? '' }}"
             data-user-lastname="{{ $comment->user->lastname ?? '' }}"
             data-user-avatar="{{ ($comment->user && $comment->user->avatar) ? 1 : 0 }}"
             alt="Аватар пользователя">
    </div>
    <div class="comment-body">
        <div class="comment-header">
            <span class="comment-author">
                @if($comment->user)
                    {{ trim(($comment->user->firstname ?? '') . ' ' . ($comment->user->lastname ?? '')) ?: 'Аноним' }}
                @else
                    Пользователь удален
                @endif
            </span>
            <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="comment-text">{{ $comment->comment }}</div>
        <div class="comment-actions">
            @auth
            <button class="reply-btn" data-comment-id="{{ $comment->id }}">Ответить</button>
            @endauth
        </div>
    </div>
</div>

@if($comment->replies->count() > 0)
<div class="replies">
    @foreach ($comment->replies as $reply)
        @if(!empty(trim($reply->comment)))
            @include('v2.meetings.partials._comment', ['comment' => $reply, 'level' => $level + 1])
        @endif
    @endforeach
</div>
@endif 