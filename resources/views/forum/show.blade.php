@extends('app', [
    'title' => $topic->name,
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@use('App\Models\User', 'User')
@use('App\Models\Forum', 'Forum')
@use('App\Models\ForumTopic', 'ForumTopic')
@use('App\Models\ForumContent', 'ForumContent')

@php
$forum = Forum::where("id", $topic->forum_id)->first();
$topicUser = User::where("id", $forum->user_id)->first();
$content = ForumContent::where("id", $topic->forum_content_id)->first();
@endphp

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('forum') }}">Форум <span>—</span></a></li>
            <li>{{ $topic->name }}</li>
        </ul>
    </div>
</div>
<div class="comment_block">
    <div class="container">
        <div class="title">
            <h2>{{ $topic->name }}</h2>
        </div>
        <div class="comment_all">
            <div class="item">
                <div class="topcomment">
                    <div class="img_com">
                        <img src="/avatar/{{ $forum->user_id }}" width="50px" height="50px" alt="">
                    </div>
                    <div class="info_user">
                        <p>Автор темы</p>
                        <h3>{{ optional($topicUser)->firstname }} {{ optional($topicUser)->lastname }}</h3>
                    </div>
                </div>
                <div class="info_all">
                    {{ $content->text ?? "" }}

                </div>
                <div class="date_comment">
                    <p>{{ $topic->created_at }}</p>
                </div>
            </div>
            <h3>Комментарии</h3>
            @forelse ($comments as $item)
                @if(empty($item->id_com))
                    @php
                        $comUser = User::findOrFail($item->user_id, ['firstname', 'lastname']);
                        $comContent = ForumContent::findOrFail($item->forum_content_id, ['text']);
                    @endphp
                    <div class="item item_white {{ $item->forum_comment_id ? 'item_child' : '' }}">
                        <div class="topcomment">
                            <div class="img_com">
                                <img src="/avatar/{{ $item->user_id }}" width="50px" height="50px" alt="">
                            </div>
                            <div class="info_user">
                                @if ($item->user_id === $forum->user_id)
                                    <p>Автор темы</p>
                                @else
                                    <p></p>
                                @endif
                                <h3>{{ optional($comUser)->firstname }} {{ optional($comUser)->lastname }}</h3>
                            </div>
                        </div>
                        <div class="info_all">
                            {{ $comContent->text }}
                        </div><br>
                        <button class="btn btn-sm btn-success d-flex align-items-center gap-2" onclick='answ({{$item->id}})'>

                            <span>Ответить</span>
                        </button>
                        <div class="date_comment justify-content-between">
                            <p>{{ $item->created_at }}</p>

                            @auth

                                <div class="d-flex gap-3">
                                    {{--
                                    <div class="btn btn-sm btn-primary d-flex align-items-center gap-2" onclick="replyToComment({comment: {{ $item->id }}, author: '{{ $comUser->firstname }} {{ $comUser->lastname }}', message: '{{ substr($comContent->text, 0, 150) }}...'})">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-quote" viewBox="0 0 16 16">
                                            <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                            <path d="M7.066 4.76A1.665 1.665 0 0 0 4 5.668a1.667 1.667 0 0 0 2.561 1.406c-.131.389-.375.804-.777 1.22a.417.417 0 1 0 .6.58c1.486-1.54 1.293-3.214.682-4.112zm4 0A1.665 1.665 0 0 0 8 5.668a1.667 1.667 0 0 0 2.561 1.406c-.131.389-.375.804-.777 1.22a.417.417 0 1 0 .6.58c1.486-1.54 1.293-3.214.682-4.112z"/>
                                        </svg>

                                        <span>Ответить</span>
                                    </div>
                                    --}}

                                    @if ($user->group === 'admin' || $user->id === $item->user_id)
                                        <form action="{{ route('forum.destroyComment', $item->id) }}" method="POST">
                                            @csrf

                                            <button class="btn btn-sm btn-danger d-flex align-items-center gap-2" onclick="confirm('Вы уверены, что хотите удалить данный комментарий?') ? this.submit() : ''">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                </svg>

                                                <span>Удалить</span>
                                            </button>
                                        </form>
                                        {{--
                                        <div class="btn btn-sm btn-info d-flex align-items-center gap-2">
                                            <svg   svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                            </svg>
                                            <span>Редактировать</span>
                                        </div>
                                        --}}
                                    @endif

                                </div>
                            @endauth
                        </div>
                        <div class='sh_{{$item->id}} sh_' style='display: none; width: 70%'>
                            <form action="{{ route('forum.addComment') }}" method="POST">
                                @csrf

                                <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                                <input type="hidden" name="reply_id" id="reply_id">

                                <input type='hidden' name='id_post' value='{{$forum->id}}'>
                                <input type='hidden' name='forum_content_id' value='{{$comContent->id}}'>
                                <input type='hidden' name='id_com' value='{{$item->id}}'>

                                <div class="card card-body border-0 shadow my-3">
                                    <div class="alert alert border-3 border-light alert-dismissible d-none" id="reply">
                                        <div>
                                            <p>Вы отвечаете на <b id="reply_user"></b>:</p>
                                            <p id="reply_message" class="mb-0"></p>
                                        </div>
                                        <button type="button" class="btn-close" onclick="replyToComment('cancel')"></button>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                                        <textarea name="comment_text" class="form-control @error('comment_text') is-invalid @enderror" required placeholder="Введите текст...">{{ old('comment_text') }}</textarea>
                                        <button type="submit" class="btn btn-success mt-auto d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                                            </svg>

                                            <span>Отправить</span>
                                        </button>
                                    </div>
                                    @error('comment_text')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </form>
                        </div><hr>
                        <h5>Ответы</h5>
                        @forelse (\App\Models\ForumComment::where("id_com", $item->id)->get() as $item)
                            @php
                                $comUser = User::findOrFail($item->user_id, ['firstname', 'lastname']);
                                $comContent = ForumContent::findOrFail($item->forum_content_id, ['text']);
                            @endphp
                            <div class="item item_white {{ $item->forum_comment_id ? 'item_child' : '' }}">
                                <div class="topcomment">
                                    <div class="img_com">
                                        <img src="/avatar/{{ $item->user_id }}" width="50px" height="50px" alt="">
                                    </div>
                                    <div class="info_user">
                                        @if ($item->user_id === $forum->user_id)
                                            <p>Автор темы</p>
                                        @else
                                            <p></p>
                                        @endif
                                <h3>{{ optional($comUser)->firstname }} {{ optional($comUser)->lastname }}</h3>
                                    </div>
                                </div>
                                <div class="info_all">
                                    {{ $comContent->text }}
                                </div><br>{{--
                                <button class="btn btn-sm btn-success d-flex align-items-center gap-2" onclick='answ({{$item->id}})'>

                                    <span>Ответить</span>
                                </button>--}}
                                <div class="date_comment justify-content-between">
                                    <p>{{ $item->created_at }}</p>

                                    @auth

                                        <div class="d-flex gap-3">
                                            {{--
                                            <div class="btn btn-sm btn-primary d-flex align-items-center gap-2" onclick="replyToComment({comment: {{ $item->id }}, author: '{{ $comUser->firstname }} {{ $comUser->lastname }}', message: '{{ substr($comContent->text, 0, 150) }}...'})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left-quote" viewBox="0 0 16 16">
                                                    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                                    <path d="M7.066 4.76A1.665 1.665 0 0 0 4 5.668a1.667 1.667 0 0 0 2.561 1.406c-.131.389-.375.804-.777 1.22a.417.417 0 1 0 .6.58c1.486-1.54 1.293-3.214.682-4.112zm4 0A1.665 1.665 0 0 0 8 5.668a1.667 1.667 0 0 0 2.561 1.406c-.131.389-.375.804-.777 1.22a.417.417 0 1 0 .6.58c1.486-1.54 1.293-3.214.682-4.112z"/>
                                                </svg>

                                                <span>Ответить</span>
                                            </div>
                                            --}}

                                            @if ($user->group === 'admin' || $user->id === $item->user_id)
                                                <form action="{{ route('forum.destroyComment', $item->id) }}" method="POST">
                                                    @csrf

                                                    <button class="btn btn-sm btn-danger d-flex align-items-center gap-2" onclick="confirm('Вы уверены, что хотите удалить данный комментарий?') ? this.submit() : ''">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>

                                                        <span>Удалить</span>
                                                    </button>
                                                </form>
                                                {{--
                                                <div class="btn btn-sm btn-info d-flex align-items-center gap-2">
                                                    <svg   svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                                    </svg>
                                                    <span>Редактировать</span>
                                                </div>
                                                --}}
                                            @endif

                                        </div>
                                    @endauth
                                </div>

                            </div>

                            <div>

                            </div>

                        @empty
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left" viewBox="0 0 16 16">
                                    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                </svg>
                                <span>Ответы отсутствуют</span>
                            </div>
                        @endforelse
                    </div>
                @endif


                <div>

                </div>

            @empty
            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat-left" viewBox="0 0 16 16">
                    <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                </svg>
                <span>Комментарии отсутствуют</span>
            </div>
            @endforelse
            @auth
            <form action="{{ route('forum.addComment') }}" method="POST">
                @csrf

                <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                <input type="hidden" name="reply_id" id="reply_id">

                <div class="card card-body border-0 shadow my-3">
                    <div class="alert alert border-3 border-light alert-dismissible d-none" id="reply">
                        <div>
                            <p>Вы отвечаете на <b id="reply_user"></b>:</p>
                            <p id="reply_message" class="mb-0"></p>
                        </div>
                        <button type="button" class="btn-close" onclick="replyToComment('cancel')"></button>
                    </div>

                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                        <textarea name="comment_text" class="form-control @error('comment_text') is-invalid @enderror" required placeholder="Введите текст...">{{ old('comment_text') }}</textarea>
                        <button type="submit" class="btn btn-success mt-auto d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                            </svg>

                            <span>Отправить</span>
                        </button>
                    </div>
                    @error('comment_text')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </form>
            @endauth
            @guest
            <div class="sign_up_comment">
                <div class="container">
                    <div class="text_comment">
                        <h4>Для участия в обсуждении <a href="{{ route('login') }}">войдите</a> или  <a href="{{ route('login') }}">зарегистрируйтесь.</a></h4>
                    </div>
                </div>
            </div>
            @endguest
        </div>
    </div>
</div>

<script>
    function answ(id){
        $('.sh_').hide();
        $('.sh_'+id).show();
    }
    function replyToComment(data)
    {

        const reply = document.getElementById('reply')
        const replyUser = document.getElementById('reply_user')
        const replyMsg = document.getElementById('reply_message')

        const replyInput = document.getElementById('reply_id')

        if('cancel' === data) {
            reply.classList.add('d-none')
            replyInput.value = ''

            return
        }

        replyUser.innerHTML = data.author
        replyMsg.innerHTML = data.message

        reply.classList.remove('d-none')
        replyInput.value = data.comment
    }
</script>
@endsection
