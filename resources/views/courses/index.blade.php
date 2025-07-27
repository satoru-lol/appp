@use('App\Models\CourseCategory', 'CourseCategory')
@php
    $category = CourseCategory::find($course_category_id, ['name']);

    $title = 'Курсы';

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
                <li>Курсы</li>
            </ul>
        </div>
    </div>
    <style>
        /* Затемненный фон */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        /* Контент модалки */
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 600px;
            height: 450px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Закрыть кнопку */
        .close {
            margin-left: 540px;
            font-size: 24px;
            cursor: pointer;
        }

        /* Поля формы */
        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Кнопка отправки */
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            cursor: pointer;
            border-radius: 4px;
            background-color: #613482;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
    <section class="courses_block">
        <div class="container">
            <div class="block_courses mob">
                <div class="title_block" >
                    <h2><span>Курсы</span> ассоциации</h2>
                    <p>Обучение проводится опытными наставниками 
                        и практикующими психологами.</p>
                </div>
                <div class="block_form_three my-0">
                    @if(auth()->user() && auth()->user()->group == "admin" && $isPermittedAdd || auth()->user() && auth()->user()->group == "administrator" && $isPermittedAdd)
                        <div class="form_group">
                            <a href="{{ route('courses.add') }}">Добавить курс</a>
                        </div>
                    @endif
                </div>
                @if(Auth::check() && Auth::user()->group === 'admin')
                    {{--
                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/courses/add/'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-success" >Добавить курс  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                                            </svg></button></p>--}}

                @endif
                <div class="block_items">
                    <div class="row">

                        @if(count($courses) > 0)
                            @forelse ($courses as $item)
                                @if($item->is_hidden == false ||auth()->check() && auth()->user()->group == "admin")

                                    <div class="col-lg-4 mb-3">
                                        <div class="item">
                                            {{--                                        <img src="/images/{{ $item->image }}" alt="" class="img_sp">--}}
                                            <div class="block_text">

                                                <h3 style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;height:50px">
                                                    <b>{{ $item->course ? $item->course->title : ($item->title ?? 'Курс без названия') }}</b>
                                                </h3>
                                                <h3><b>{{ $item->section ?? '' }}</b></h3>
                                             <!--   <p><b>{{ $item->title ?? '' }}</b></p>
                                                <p><b>{{ $item->description ?? '' }}</b></p>-->
                                                
                                                @php
                                                                $differenceInSeconds = strtotime($item->end_time) - strtotime($item->start_time);
                                                                $hours = floor($differenceInSeconds/3600);
                                                                $minutes = floor(($differenceInSeconds % 3600) / 60);
                                                                if ($hours > 0 && $minutes == 0) {
                                                                        $result = "$hours " . ($hours == 1 ? 'час' : ($hours < 5 ? 'часа' : 'часов'));
                                                                } elseif ($hours > 0) {
                                                                    $result = "$hours " . ($hours == 1 ? 'час' : ($hours < 5 ? 'часа' : 'часов')) . " $minutes минут";
                                                                } else {
                                                                    $result = "$minutes минут";
                                                                }
                                                            @endphp
                                                            
                                                <p>
                                                                <img
                                                                    src="/img/img_cl.svg">{{ \Carbon\Carbon::parse($item->date)->format('d.m.Y') . ' ' . \Carbon\Carbon::parse($item->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($item->end_time)->format('H:i') }}

                                                            </p>
                                                                      <p><img src="/img/img_cl1.svg" alt="">{{ $result }}</p>
                                                <h6><img src="/img/sms.svg" alt="" style="margin-right:10px">{{ $item->speakers }} </h6>
                                                <a href="/courses/{{$item->id}}">Подробнее</a><br>
                                                @if(Auth::check() && Auth::user()->group === 'admin')
                                                    <a style="background-color: green"
                                                       href="/course/edit/{{$item->id}}">Редактировать
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                             fill="currentColor" class="bi bi-pencil-square"
                                                             viewBox="0 0 16 16">
                                                            <path
                                                                d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                            <path fill-rule="evenodd"
                                                                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <div
                                                    style="display: flex; justify-content: flex-start; margin-top: 15px">
                                                    @if(Auth::check())
                                                        <button type="button" class="btn btn-light" data-toggle="modal"
                                                                data-target="#donationModal" onclick="openModal()">Донат
                                                        </button>
                                                        <div id="donationModal" class="modal">
                                                            <input type="hidden" value="{{$item->course_id}}" id="club_id">
                                                            <div class="modal-content">
                                                                <span class="close"
                                                                      onclick="closeModal()">&times;</span>
                                                                <h2>Донат</h2>
                                                                <form id="donationForm">
                                                                    <label
                                                                        for="donationAmount">Сумма</label>
                                                                    <input type="number" id="donationAmount"
                                                                           placeholder="Введите сумму"
                                                                           required>
                                                                    <span style="color:gray;float: left; font-size: 12px;">Деньги будет списано с вашего счета на сайте</span>
                                                                    <br><br>
                                                                    <label for="donationReason">Комментарий</label>
                                                                    <textarea id="donationReason" rows="3"
                                                                              placeholder="Напишите комментарий"
                                                                              required></textarea>

                                                                    <button type="submit">Отправить</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if(Auth::check() && Auth::user()->group === 'admin')
                                                        <p>
                                                            <button style="margin-left: 15px"
                                                                    onclick="window.location.href = '/courses/delete/{{$item->id}}'"
                                                                    id="delete-course"
                                                                    href="/courses/delete/{{$item->id}}" type="button"
                                                                    class="btn btn-danger">Удалить
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                     height="16" fill="currentColor" class="bi bi-x-lg"
                                                                     viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                                </svg>
                                                            </button>
                                                        </p>

                                                        <p>
                                                            <button style="margin-left: 15px"
                                                                    onclick="window.location.href = '/hideCourse/{{$item->id}}/{{$item->is_hidden == false ? "hide" : "show"}}'"
                                                                    id="delete-course"
                                                                    href="/hideCourse/{{$item->id}}/hide" type="button"
                                                                    class="btn {{$item->is_hidden == false ? "btn-warning" : "btn-success"}} ">{{$item->is_hidden == false ? "Скрыть" : "Показывать"}}
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                     height="16" fill="currentColor"
                                                                     class="bi bi-eye-slash" viewBox="0 0 16 16">
                                                                    <path
                                                                        d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                                                                    <path
                                                                        d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                                                                    <path
                                                                        d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                                                                </svg>
                                                            </button>
                                                        </p>
                                                    @endif

                                                </div>


                                            </div>

                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="col-12 text-center">
                                    <div class="alert alert-warning">Курсы отсутствуют</div>
                                </div>
                            @endforelse
                        @else
                            <div class="col-12 text-center">
                                <div class="alert alert-warning">Курсы доступны только по подписке</div>
                            </div>
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
                                                            <h6><img src="/img/sms.svg" alt="" style="margin-right:10px">{{ $item->speakers }} </h6>
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
                {{--<div class="pagination-container">
                    {{ $courses->links('pagination::bootstrap-4') }} <!-- Используйте стандартный Bootstrap шаблон для пагинации -->
                </div>--}}

            </div>

        </div>

    </section>

    <script>
        let addCategory

        document.addEventListener('DOMContentLoaded', () => {
            addCategory = new bootstrap.Modal('#addCategory')
            @error('name') addCategory.show()
            @enderror
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

            form.action = '{{ route('courseCategoryAdd') }}'

            name.value = ''
            status.selected = true

            button[0].classList.remove('d-none')
            button[1].classList.add('d-none')
        }

        function openModal() {
            document.getElementById("donationModal").style.display = "flex";
        }

        function closeModal() {
            document.getElementById("donationModal").style.display = "none";
        }


        // Закрытие модалки при клике вне её
        window.onclick = function (event) {
            const modal = document.getElementById("donationModal");
            if (event.target === modal) {
                closeModal();
            }
        }

        document.addEventListener("DOMContentLoaded", function () {
            const form = document.getElementById("donationForm");

            if (form) { // Проверяем, найден ли элемент
                form.addEventListener("submit", function (event) {
                    event.preventDefault();
                    const amount = document.getElementById("donationAmount").value;
                    const reason = document.getElementById("donationReason").value;
                    if (!amount || amount <= 0) {
                        alert("Введите сумму больше 0!");
                        return;
                    }
                    const club_id = document.getElementById('club_id').value
                    // Данные для отправки
                    const donationData = {
                        amount: amount,
                        reason: reason,
                        course_id: club_id,
                    };
                    fetch('{{ route('donat') }}', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(donationData)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(`Спасибо за донат на сумму ${amount} руб.!\nПричина: ${reason}`);
                                document.getElementById("donationForm").reset(); // Очищаем форму
                            } else {
                                alert("Ошибка при отправке доната!");
                            }
                        })
                        .catch(error => {
                            console.error("Ошибка при отправке запроса:", error);
                            alert("Не удалось отправить донат. Попробуйте позже.");
                        });
                    closeModal();
                });
            } else {
                console.error("Ошибка: форма donationForm не найдена.");
            }
        });
    </script>
@endsection
