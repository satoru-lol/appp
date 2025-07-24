@use('App\Models\CourseCategory', 'CourseCategory')
@php
    $category = CourseCategory::find($course_category_id, ['name']);

    $title = 'Полигон';

    if($category)
        $title .= ' по категории '.$category->name;
@endphp
@extends('app', [
    'title' => $title,
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li><a href="{{ route ('home') }}">Главная <span>—</span></a></li>
                <li>Полигон</li>
            </ul>
        </div>
    </div>

    <section class="courses_block">
        <div class="container">
            <div class="block_courses mob">
                <div class="title_block">
                    <h2><span>Полигон</span></h2>
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
                </div>

                <div class="block_form_three my-0" style="display: block; align-content: center">
                    @if(auth()->user() && auth()->user()->group == "admin" && $isPermittedAdd || auth()->user() && auth()->user()->group == "administrator" && $isPermittedAdd)
                        <div class="form_group">
                            <a style="font-size: 15px;" href="{{ route('polygon.show.add') }}">Добавить полигон</a>
                        </div>
                    @endif
                        <div class="form_group">
                            <a style="font-size: 15px;" href="#" data-toggle="modal" @if(auth()->user()) id="sendToMail" @else  @endif data-target="#confirmedPanel">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Подать заявку<br/>на участие в полигоне</a>
                        </div>
                </div>
                @if(Auth::check() && Auth::user()->group === 'admin')
                    {{--
                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/courses/add/'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-success" >Добавить курс  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                                            </svg></button></p>--}}

                @endif
                <script>
                    $(".closeModal").on("click", function () {
                        $('#confirmedPanel').modal('hide');
                    })
                </script>
                <div style="margin-top: 200px" class="modal fade" id="confirmedPanel" tabindex="-1" role="dialog" aria-labelledby="confirmedPanelLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmedPanelLabel">@if(auth()->user()) Успешно <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                    </svg> @else Предупреждение <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                    </svg>  @endif</h5>
                            </div>
                            <div class="modal-body">
                                <h4>@if(auth()->user()) Ваша заявка отправлена @else Чтобы подать заявку на участие в полигоне, Вам нужно зарегистрироваться @endif<span></span></h4>
                            </div>
                            <div class="modal-footer">
                                @if(auth()->user()) <button type="button" onclick="window.location.reload()" style="background-color: #613482" class="btn btn-success">Закрыть</button> @else <button type="button" style="background-color: #613482" onclick="window.location.href = 'https://appp-psy.ru/introduction'" class="btn btn-success">Зарегистрироваться</button> @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="block_items" >
                    <div class="row">

                        @if (auth()->check())
                            @forelse ($courses as $item)
                                @if($item->is_hidden == false ||auth()->check() && auth()->user()->group == "admin")

                                <div class="col-lg-4 mb-3">
                                    <div class="item">
                                        <img src="/images/{{ $item->image }}" alt="" class="img_sp">
                                        <div class="block_text">

                                            <h3><b>{{ $item->title }}</b></h3>
                                            <p><img src="/img/img_cl.svg" alt="">{{ json_decode($item->times)->start }}</p>
                                            <p><img src="/img/img_cl1.svg" alt="">{{ json_decode($item->times)->read }}</p>
                                            <h6>{{ $item->speakers }} <img src="/img/sms.svg" alt=""></h6>
                                            <a href="/polygon/{{$item->id}}">Подробнее</a><br>
                                            @if(Auth::check() && Auth::user()->group === 'admin')
                                                <a style="background-color: green" href="/polygon/edit/{{$item->id}}">Редактировать <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg></a>
                                            @endif
                                            <div style="display: flex; justify-content: flex-start; margin-top: 15px">
                                                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#donationModal">Донат</button>

                                                @if(Auth::check() && Auth::user()->group === 'admin')
                                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/courses/delete/{{$item->id}}'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                            </svg></button></p>

                                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/hideCourse/{{$item->id}}/{{$item->is_hidden == false ? "hide" : "show"}}'" id="delete-course" href="/hideCourse/{{$item->id}}/hide" type="button" class="btn {{$item->is_hidden == false ? "btn-warning" : "btn-success"}} " >{{$item->is_hidden == false ? "Скрыть" : "Показывать"}}  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
                                                                <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                                                                <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                                                                <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                                                            </svg></button></p>
                                                @endif

                                            </div>


                                        </div>

                                    </div>
                                </div>
                                @endif
                            @empty
                                {{--<div class="col-12 text-center">
                                    <div class="alert alert-warning">Полигоны отсутствуют</div>
                                </div>--}}
                            @endforelse
                        @else
                           {{-- <div class="col-12 text-center">
                                <div class="alert alert-warning">Курсы доступны только по подписке</div>
                            </div>--}}
                        @endif
                    </div>
                </div>
{{--
                <div class="block_items mob">
                    <div class="swiper mySwiper1">
                        <div class="swiper-wrapper">
                            @forelse ($courses as $item)
                                @php $user = \App\Models\User::find($item->user_id);
                                @endphp
                                <div class="swiper-slide">
                                    <div class="item">
                                        <img src="/images/{{ $item->image }}" alt="" class="img_sp">
                                        <div class="block_text">

                                            <h3><b>{{ $item->title }}</b></h3>
                                            <p><img src="/img/img_cl.svg" alt="">{{ json_decode($item->times)->start }}</p>
                                            <p><img src="/img/img_cl1.svg" alt="">{{ json_decode($item->times)->read }}</p>
                                            <h6>{{ $item->speakers }} <img src="/img/sms.svg" alt=""></h6>
                                            <a href="/courses/{{$item->id}}">Подробнее</a>

                                            <div style="display: flex; justify-content: flex-start; margin-top: 15px">
                                                <button type="button" class="btn btn-light" data-toggle="modal" data-target="#donationModal">Донат</button>

                                                @if(Auth::check() && Auth::user()->group === 'admin')
                                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/courses/delete/{{$item->id}}'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                            </svg></button></p>
                                                @endif

                                            </div>


                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="swiper-slide text-center">
                                    <div class="alert alert-warning">Курсы отсутствуют</div>
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
--}}
            </div>
        </div>

    </section>
    {{ $courses->links() }}

    <script>
        let addCategory

        document.addEventListener('DOMContentLoaded', () => {
            addCategory = new bootstrap.Modal('#addCategory')
            @error('name') addCategory.show() @enderror
            @error('status') addCategory.show() @enderror
        })

        function editCategory(data)
        {
            const form = document.getElementById('addcat'),
                name = document.getElementById('name'),
                status = document.querySelector('select#status option[value="'+data.status+'"]'),
                button = document.querySelectorAll('#buttonAddOrUpdate span')

            form.action = data.action

            name.value = data.name
            status.selected = true

            button[0].classList.add('d-none')
            button[1].classList.remove('d-none')

            addCategory.show()
        }

        function resetForm()
        {
            const form = document.getElementById('addcat'),
                name = document.getElementById('name'),
                status = document.querySelector('select#status option[value="0"]'),
                button = document.querySelectorAll('#buttonAddOrUpdate span')

            form.action = '{{ route('courseCategoryAdd') }}'

            name.value = ''
            status.selected = true

            button[0].classList.remove('d-none')
            button[1].classList.add('d-none')
        }

        $("#sendToMail").on("click", function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/send/mail', // URL вашего маршрута
                type: 'POST',
                data: {},
                success: function(response) {
                    //alert("success")//$("#editProductForm").html(response); // Обработка успешного ответа
                },
                error: function(xhr) {
                    alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                }
            });
        })
    </script>
@endsection
