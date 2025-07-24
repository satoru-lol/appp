@extends('app', [
    'title' => 'Регулярные мероприятия',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li>Регулярные мероприятия</li>
        </ul>
    </div>
</div>
<section class="courses_block">
    <div class="container">
        <div class="title_block d-flex gap-2 align-items-center justify-content-md-between flex-wrap">
            <h2 class="m-0">Регулярные мероприятия</h2>
            <p class="m-0" style="font-size: 22px; color: #333; font-weight: 600; display: flex; align-items: center; white-space:nowrap;">( В&nbsp;разработке )</p>

        </div>
        @if(auth()->user() && auth()->user()->group == "admin" && $isPermittedAdd ||auth()->user() && auth()->user()->group == "administrator" && $isPermittedAdd)
        <div class="block_form_three my-0">
            <div class="form_group">
                <a href="{{ route('reg.add') }}">Добавить мероприятие</a>
            </div>
        </div>
        @endif
        <div class="block_items second_it">
            <div class="row">
			<h3><b>Мероприятия</b></h3>
			<p>1. Групповые супервизии (<b>Юрий Ионов, Александр Климов</b>). </p>
			<p>2. Интервизии (<b>Александр Никулин</b>). </p>
			<p>3. Клинические разборы (<b>Борис Ершов</b>). </p>
			<p>4. Авторские лекции. </p>
			<p>5. Группа тренировки технических приемов (<b>Мария Макарова, Анастасия Соловьева, Алина Верзилова, Тамара Вахрамеева</b>). </p>
			<p>6. Развитие профессиональной коммуникации (<b>Александр Никулин, Наталья Март</b>). </p>
			<p>7. Семейная терапия: тренинг навыков (<b>Наталья Гликман, Александр Климов, Азизбек Ходихужаев</b>).</p>
			<p>8. Балинтовская группа (<b>Полина Пантелеева, Дарья Костина, Роман Янтимиров</b>).</p>
			<p></p>
                @use('App\Models\User', 'User')

                @forelse ($blogs as $item)
                @php $blogUser = User::find($item->user_id, ['firstname', 'lastname']);
                @endphp
                <div class="col-lg-6">
                    <div class="item">
                        <img src="{{ asset('img/blog/'.$item->image) }}" alt="" class="img_st" style="height: 500px;object-fit: contain;">
                        <div class="block_text">
                            <span>{{ $item->created_at }}</span>
                            <h3><a href="{{ route('reg.show', $item->id) }}" class="bg-white text-primary justify-content-start btn">{{ $item->name }}</a></h3>
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
                        @if(Auth::check() && Auth::user()->group === 'admin')

                        <p><button style="margin-left: 15px" onclick="window.location.href = '/regmerop/delete/{{$item->id}}'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                </svg></button></p><br>
                            <p><button style="margin-left: 15px" onclick="window.location.href = '/regmerop/edit/{{$item->id}}'" href="/regmerop/edit/{{$item->id}}" type="button" class="btn btn-success" >Редактировать <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                    </svg></button></p><br>
                        @endif
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
