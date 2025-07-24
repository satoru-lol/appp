@extends('app', [
    'title' => 'Форум',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li>Форум</li>
        </ul>
    </div>
</div>
<div class="block_form_three">
    <div class="container">
        <div class="form_group">
            <h2>Форум</h2>
            @isset($search)
            <a href="{{ route('forum') }}">Все темы</a>
            @else
                @if(auth()->user() && $isPermittedAdd)
                    <div class="dropdown">
                        <a class="dropdown-toggle" id="addTopic" type="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false">Добавить тему</a>
                        {{--<div class="dropdown-menu shadow w-100">
                            <form action="{{ route('forum.addTopic') }}" method="POST" class="px-4 py-3">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Название</label>
                                    <input type="text" name="topic_name" value="{{ old('topic_name') }}" class="form-control @error('topic_name') is-invalid @enderror" id="topic_name" required placeholder="Введите название темы">

                                    @error('topic_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="desc" class="form-label">Описание</label>
                                    <input type="text" name="topic_desc" value="{{ old('topic_desc') }}" class="form-control @error('topic_desc') is-invalid @enderror" id="topic_desc" placeholder="Введите описание темы (необязательно)">

                                    @error('topic_desc')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="text" class="form-label">Текст</label>
                                    <textarea id="text" name="topic_text" class="form-control  @error('topic_name') is-invalid @enderror" required placeholder="Текст...">{{ old('topic_text') }}</textarea>

                                    @error('topic_text')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="text" class="form-label">Категория</label>
                                    <select name="topic_category" class="form-select @error('topic_category') is-invalid @enderror" required>
                                        <option>Выберите категорию</option>
                                        @forelse ($categories as $item)
                                            <option value="{{ $item->id }}"  {{ old('topic_category') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @empty
                                        @endforelse
                                    </select>

                                    @error('topic_category')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addTopic').click()">Отмена</button>
                                <button type="submit" class="btn btn-primary">Создать</button>
                            </form>
                        </div>--}}
                    </div>

                @endif
            @endisset
        </div>
        @isset($search)
        @else
        <div class="tab_group">
            <a href="{{ route('forum') }}" class="{{ Request::url() === route('forum') ? 'avtive' : '' }}">ВСЕ</a>
            @forelse ($categories as $item)
            @php $active = Request::url() === route('forum.category', $item->id) ? 'avtive' : '';
            @endphp

            @if ($user && $user->group === 'admin')
            <div class="dropdown">
                <a href="javascript:;" class="{{ $active }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $item->name }}</a>
                <ul class="dropdown-menu">
                    <li>
                        <div class="dropdown-item d-flex align-items-center gap-3" onclick="location.href = '{{ route('forum.category', $item->id) }}'" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                                <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z" />
                                <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z" />
                            </svg>
                            <span>Перейти</span>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center gap-3" type="button" data-bs-toggle="modal" data-bs-target="#addCategory" onclick="editCategory({name: '{{ $item->name }}', status: {{ $item->status }}, action: '{{ route('forum.updateCategory', $item->id) }}' })">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                            </svg>
                            <span>Редактировать</span>
                        </div>
                    </li>
                    @if(auth()->user() && auth()->user()->group == "admin")
                        <li>
                            <div class="dropdown-item btn btn-danger d-flex align-items-center gap-3" type="button" onclick="confirm('Вы уверены, что хотите удалить данную категорию? Все курсы связанные с данной категорией будут удалены') ? location.href = '{{ route('forum.destroyCategory', $item->id) }}' : ''">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                                <span>Удалить</span>
                            </div>
                        </li>
                    @endif

                </ul>
            </div>
            @else
            <a href="{{ route('forum.category', $item->id) }}" class="{{ $active }}">{{ $item->name }}</a>
            @endif
            @empty
            @endforelse

            @if ($user && $user->group === 'admin')
            <a href="javascript:;" class="d-flex align-items-center avtive" data-bs-toggle="modal" data-bs-target="#addCategory" onclick="resetForm()">
                <span>Добавить</span>

                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                </svg>
            </a>

            <div class="modal fade" id="addCategory" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('forum.addCategory') }}" method="POST" id="addcat">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="addCategoryLabel">Добавление категорий</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <label for="name">Название</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                                <div class="my-3"></div>

                                <label for="status">Видимость</label>
                                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="0">Не видно</option>
                                    <option value="1">Видно</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                <button type="submit" class="btn btn-primary" id="buttonAddOrUpdate">
                                    <span>Добавить</span>
                                    <span class="d-none">Сохранить</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
        <div class="tab_group mob">
            <select onchange="location.href = this.value === 0 ? '{{ route('forum') }}' : '{{ route('forum') }}/category/'+this.value">
                <option value="0" {{ Request::url() === route('forum') ? 'selected' : '' }}>ВСЕ</option>
                @forelse ($categories as $item)
                <option value="{{ $item->id }}" {{ Request::url() === route('forum.category', $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                @empty
                @endforelse
            </select>
        </div>
        @endisset
        <form action="{{ route('forum.search') }}" method="POST">
            @csrf
            <div class="search-box overflow-hidden">

                <input type="text" name="search" value="{{ request()->input('search') }}" class="search-input" placeholder="Найти тему">

                <button class="search-button" type="submit">
                    <img src="/img/search1.svg" alt="">
                </button>
            </div>
        </form>
    </div>
</div>
<div class="table_block">
    <div class="container">
        <div class="table_group">
            @use('App\Models\Forum', 'Forum')
            @use('App\Models\ForumTopic', 'ForumTopic')
            @use('App\Models\ForumContent', 'ForumContent')
            @use('App\Models\ForumComment', 'ForumComment')
            @use('App\Models\User', 'User')

            @isset($search)
                @forelse ($topics as $item)
                @php
                    $forum = Forum::find($item->forum_id);
                    $user = $forum ? User::find($forum->user_id) : null;
                @endphp

                @if ($item->status == 0)
                    @continue
                @endif

                <div class="tables">
                    <div class="dates">
                        <div class="block_left">
                            <span>{{ $item->created_at }}</span>
                            <span>{{ ForumComment::where('forum_topic_id', $item->id)->where('status', 1)->count() }} ответов</span>
                        </div>
                        <h4>Автор: {{ $user ? ($user->firstname . ' ' . $user->lastname) : 'Неизвестный автор' }}</h4>
                    </div>
                    <h3><a href="{{ route('forum.showTopic', $item->id) }}">{{ $item->name }}</a></h3>
                    <small class="text-muted">{{ $item->desc ?? '' }}</small>
                    <h4 class="mob_h">Автор: {{ $user ? ($user->firstname . ' ' . $user->lastname) : 'Неизвестный автор' }}</h4>
                </div>
                @empty
                <div class="alert alert-info mb-0">Темы отсутствуют</div>
                @endforelse
            @else
                @forelse ($forums as $item)
                @php
                    $topic = ForumTopic::where('forum_id', $item->id)->first();
                    $user = User::find($item->user_id);
                @endphp

                @if ($topic && $topic->status == 0)
                    @continue
                @endif

                <div class="tables">
                    <div class="dates">
                        <div class="block_left">
                            <span>{{ $topic ? $topic->created_at : '' }}</span>
                            <span>{{ $topic ? ForumComment::where('forum_topic_id', $topic->id)->where('status', 1)->count() : 0 }} ответов</span>
                        </div>
                        <h4>Автор: {{ $user ? ($user->firstname . ' ' . $user->lastname) : 'Неизвестный автор' }}</h4>
                    </div>
                    <h3><a href="{{ $topic ? route('forum.showTopic', $topic->id) : '#' }}">{{ $topic ? $topic->name : '' }}</a></h3>
                    <small class="text-muted">{{ $topic ? ($topic->desc ?? '') : '' }}</small>
                    <h4 class="mob_h">Автор: {{ $user ? ($user->firstname . ' ' . $user->lastname) : 'Неизвестный автор' }}</h4>
                </div>
                    @if(auth()->user() && auth()->user()->group == "admin")

                    <p><button style="margin-left: 15px" onclick="window.location.href = '/forum/delete/{{$item->id}}'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg></button></p><br>
                    @endif
                @empty
                <div class="alert alert-info mb-0">{{ __('Отсутствуют') }}</div>
                @endforelse
            @endisset
        </div>
    </div>
</div>

@isset($search)
{{ $topics->links() }}
@else
{{ $forums->links() }}
@endisset

<script>
    let addCategory, addTopic

    document.addEventListener('DOMContentLoaded', () => {
        addCategory = new bootstrap.Modal('#addCategory')
        addTopic = new bootstrap.Dropdown('#addTopic')

        @error('name') addCategory.show() @enderror
        @error('status') addCategory.show() @enderror

        @error('topic_name') addTopic.show() @enderror
        @error('topic_desc') addTopic.show() @enderror
        @error('topic_text') addTopic.show() @enderror
        @error('topic_category') addTopic.show() @enderror
    })

    function editCategory(data) {
        const form = document.getElementById('addcat'),
            name = document.getElementById('name'),
            status = document.querySelector('select#status option[value="' + data.status + '"]'),
            button = document.querySelectorAll('#buttonAddOrUpdate span')

        form.action = data.action

        name.value = data.name
        status.selected = true

        button[0].classList.add('d-none')
        button[1].classList.remove('d-none')

        addCategory.show()
    }

    function resetForm() {
        const form = document.getElementById('addcat'),
            name = document.getElementById('name'),
            status = document.querySelector('select#status option[value="0"]'),
            button = document.querySelectorAll('#buttonAddOrUpdate span')

        form.action = '{{ route('forum.addCategory') }}'

        name.value = ''
        status.selected = true

        button[0].classList.remove('d-none')
        button[1].classList.add('d-none')
    }
</script>
@endsection
