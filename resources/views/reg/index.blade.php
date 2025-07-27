@extends('app', [
    'title' => 'Полигон',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li>Полигон</li>
        </ul>
    </div>
</div>
<section class="courses_block">
    <div class="container">
        <div class="title_block d-flex align-items-center justify-content-md-between">
            <h2>Полигон</h2>

        </div>
        <div class="block_form_three my-0">
            <div class="form_group">
                <a href="{{ route('blog.add') }}">Добавить блог</a>
            </div>
        </div>
        <div class="block_tabs second_block_tabs">

            <a href="{{ route('blog') }}" class="{{ Request::url() === route('blog') ? 'active' : '' }}">ВСЕ</a>
            @forelse ($categories as $item)
            @php $active = Request::url() === route('blog.category', $item->id) ? 'active' : '';
            @endphp

            @if ($user && $user->group === 'admin')
            <div class="dropdown">
                <a href="javascript:;" class="{{ $active }}" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $item->name }}</a>
                <ul class="dropdown-menu">
                    <li>
                        <div class="dropdown-item d-flex align-items-center gap-3" onclick="location.href = '{{ route('blog.category', $item->id) }}'" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link" viewBox="0 0 16 16">
                                <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9q-.13 0-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z" />
                                <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4 4 0 0 1-.82 1H12a3 3 0 1 0 0-6z" />
                            </svg>
                            <span>Перейти</span>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item d-flex align-items-center gap-3" type="button" data-bs-toggle="modal" data-bs-target="#addCategory" onclick="editCategory({name: '{{ $item->name }}', status: {{ $item->status }}, action: '{{ route('blog.category.update', $item->id) }}' })">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                            </svg>
                            <span>Редактировать</span>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-item btn btn-danger d-flex align-items-center gap-3" type="button" onclick="confirm('Вы уверены, что хотите удалить данную категорию? Все курсы связанные с данной категорией будут удалены') ? location.href = '{{ route('blog.category.destroy', $item->id) }}' : ''">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                            </svg>
                            <span>Удалить</span>
                        </div>
                    </li>
                </ul>
            </div>
            @else
            <a href="{{ route('blog.category', $item->id) }}" class="{{ $active }}">{{ $item->name }}</a>
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
        <div class="block_items second_it">
            <div class="row">
			<p><b>Проект «Полигон»</b> — это творческая площадка Портала для психологов и психотерапевтов для специалистов (в области психологии, клинической психологии, психотерапии, психиатрии, наркологии и смежных дисциплин), желающих попробовать себя в преподавательской деятельности.

<p>Для участия в «Полигоне» Вам необходимо:</p>
<p>1. Иметь профильное образование.</p>
<p>2. Иметь стаж научной либо практической работы по специальности не менее трех лет (включая частный прием).</p>
<p>3. Подготовить список тем и дисциплин, которые Вы готовы преподавать.</p>
<p>4. Подготовить описание вебинара, который Вы хотели бы провести в рамках проекта «Полигон».</p>
<p>5. Подать заявку на почту client@appp-psy.ru и прислать документы и материалы согласно требованиям к участнику (документы об образовании, документы, подтверждающие стаж работы, список тем и дисциплин, материалы по вебинару).</p>
<p>6. Согласовать и подготовить план и презентацию для проведения вебинара по выбранной теме в объеме 2 ак.ч.</p>
<p>Портал для психологов и психотерапевтов имеет право отказать в реализации каких-либо тем на свое усмотрение, в таком случае Вам предложат заменить тему вебинара.
</p>
<p>Проект «Полигон» будет проходить каждые три месяца с учетом поступивших заявок и будет реализован в форме открытого цикла вебинаров для всех желающих. Для каждого запуска «Полигона» предварительно будет публиковаться рекомендуемая тема вебинаров. Однако участник вправе подать заявку, не соответствующую рекомендуемой тематике. Слушатели вебинаров будут иметь возможность оценить каждого участника по ряду параметров (открытый рейтинг), а также оставить отзывы (критические отзывы публично распространяться не будут, но могут быть предоставлены участнику по запросу в качестве обратной связи).
</p>
<p>Тройка участников с лучшим рейтингом получит денежные призы. Участники с лучшим рейтингом могут претендовать на статус преподавателей отдельных тем или образовательных программ Портала для психологов и психотерапевтов на возмездной основе после прохождения собеседования.”
</p>
<p></p>
                @use('App\Models\User', 'User')

                @forelse ($blogs as $item)
                @php $blogUser = User::find($item->user_id, ['firstname', 'lastname']);
                @endphp
                <div class="col-lg-6">
                    <div class="item">
                        <img src="{{ asset('img/blog/'.$item->image) }}" alt="" class="img_st" style="height: 250px;object-fit: contain;">
                        <div class="block_text">
                            <span>{{ $item->created_at }}</span>
                            <h3><a href="{{ route('blog.show', $item->id) }}" class="bg-white text-primary justify-content-start btn">{{ $item->name }}</a></h3>
                            <div class="block_blog">
                                <div class="blog_information">
                                    <img src="/img/img_bl.svg" alt="">
                                    <p>{{ $item->views }}</p>
                                </div>
                                <div class="blog_information">
                                    <img src="/img/img_bl1.svg" alt="">
                                    <p>{{ $item->time_read }}</p>
                                </div>
                                <div class="blog_information">
									@if(isset($blogUser->firstname))
                                    <p>{{ $blogUser->firstname }}
									@endif
									@if(isset($blogUser->lastname))
									{{ $blogUser->lastname }}</p>
									@endif
                                </div>
                            </div>
                            <div class="block_blog mob">
                                <div class="group_inf">
                                    <div class="blog_information">
                                        <img src="/img/img_bl.svg" alt="">
                                        <p>{{ $item->views }}</p>
                                    </div>
                                    <div class="blog_information">
                                        <img src="/img/img_bl1.svg" alt="">
                                        <p>{{ $item->time_read }}</p>
                                    </div>
                                </div>
                                <div class="blog_information">

                                    @if(isset($blogUser->firstname))
                                    <p>{{ $blogUser->firstname }}
									@endif
									@if(isset($blogUser->lastname))
									{{ $blogUser->lastname }}</p>
									@endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        {{ __('Отсутствуют') }}
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

{{ $blogs->links() }}

<script>
    let addCategory

    document.addEventListener('DOMContentLoaded', () => {
        addCategory = new bootstrap.Modal('#addCategory')

        @error('name') addCategory.show() @enderror
        @error('status') addCategory.show() @enderror
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

        form.action = '{{ route('blog.category.add') }}'

        name.value = ''
        status.selected = true

        button[0].classList.remove('d-none')
        button[1].classList.add('d-none')
    }
</script>
@endsection
