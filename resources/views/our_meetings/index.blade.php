@extends('app', [
    'title' => 'Наши встречи',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
                <li>Наши встречи</li>
            </ul>
        </div>
    </div>
    <section class="courses_block">
        <div class="container">
            <div class="title_block d-flex align-items-center justify-content-md-between">
                <h2>Наши встречи</h2> {{--<p style="font-size: 30px; color: #333; font-weight: 600; display: flex; align-items: center;">
                <!-- Иконка SVG -->
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#FFC107" viewBox="0 0 16 16" style="margin-right: 15px;">
                    <path d="M7.938 2.016a.13.13 0 0 1 .124 0l6.857 3.943c.04.023.073.06.098.101.024.04.038.086.04.134v7.944a.26.26 0 0 1-.138.228l-6.857 3.942a.13.13 0 0 1-.124 0L1.082 14.376a.26.26 0 0 1-.138-.228V6.193c0-.048.014-.094.04-.134.025-.041.058-.078.098-.101L7.938 2.016zM7.5 5v4h1V5h-1zm0 6v2h1v-2h-1z"/>
                </svg>
                В разработке
            </p>--}}

            </div>
            {{--@if(auth()->user() && auth()->user()->group == "admin" && $isPermittedAdd ||auth()->user() && auth()->user()->group == "administrator" && $isPermittedAdd)--}}
            <div style="">
                {{--<form action="" method="get">
                    @csrf
                    <div class="form-row">
                        <label>Укажите тип встречи</label>
                        <select class="form-control-sm justify-content-center float-end" name="type_meet" id="type_meet">
                            <option>Не выбран</option>
                            <option value="1">Онлайн</option>
                            <option value="2">Очно</option>
                        </select>
                        <input type="text" name="place" id="place" placeholder="Введите место встречи"
                               class="form-control-lg" style="display: none">
                    </div>
                    <br>
                    <div class="form-row">
                        <label>Укажите стоимость</label>
                        <select class="form-control-sm justify-content-center float-end" id="price" name="price">
                            <option>Не выбран</option>
                            <option value="free">Бесплатно</option>
                            <option value="paid">Платно</option>
                        </select>
                        <br>
                        <input type="number" name="from_price" id="from_price" placeholder="Цена от..."
                               class="form-control" style="display: none">
                        <br>
                        <input type="number" name="to_price" id="to_price" placeholder="Цена до..."
                               class="form-control" style="display: none">

                    </div>
                    <br>
                    <div class="form-row">
                        <label>Укажите дата начала</label>
                        <input type="date" name="date_start" class="float-end">
                    </div>
                    <br>
                    <div class="form-row">
                        <label>Укажите дата завершения</label>
                        <input type="date" name="date_end" class="float-end">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Поиск</button>
                </form>--}}
                <p>В этом разделе публикуются анонсы встреч, которые инициируются самими участниками нашего сообщества.
                    Эти собрания создаются для обмена опытом, обсуждения актуальных тем и совместного развития профессиональных навыков.</p>
                <strong>Обратите внимание! Если встреча проводится в виртуальном формате, выбор платформы и организация подключения остаются за инициатором. Ассоциация не предоставляет техническую поддержку таких мероприятий и не несет ответственности за их содержание.</strong>
            </div>

            <br><br>
            <div class="block_form_three my-0">
                <div class="form_group">
                    @if(auth()->user() && (\App\Models\Subscription::where('user_id',auth()->user()->id)->first()->level !== 0 || \App\Models\Subscription::where('user_id',auth()->user()->id)->first()->level == -1))
                        <button type="button" id="addMeet" class="btn btn-primary">Добавить встречу</button>
                    @endif

                </div>

                @if(!auth()->user())
                    <p style="margin-top: -30px;">Чтобы добавить встречу, нужно <a href="{{route('login')}}">войти</a> или <a href="{{route('introduction')}}">зарегистрироваться</a></p>
                @endif
            </div>


            <div style="margin-top: 200px" class="modal fade" id="confirmedAuth" tabindex="-1" role="dialog"
                 aria-labelledby="confirmedPanelLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmedPanelLabel">@if(auth()->user())
                                    Успешно
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                         class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                    </svg>
                                @else
                                    Предупреждение
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                         class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                    </svg>
                                @endif</h5>
                        </div>
                        <div class="modal-body">
                            <h4>Чтобы добавить встречу, нужно <button href="{{route('login')}}">войти</button> или <button href="{{route('introduction')}}">зарегистрироваться</button> <span></span></h4>
                        </div>
                        <div class="modal-footer">
                            @if(auth()->user())
                                <button type="button" onclick="window.location.reload()"
                                        style="background-color: #613482" class="btn btn-success">Закрыть
                                </button>
                            @else
                                <button type="button" style="background-color: #613482"
                                        onclick="window.location.href = 'https://appp-psy.ru/introduction'"
                                        class="btn btn-success">Зарегистрироваться
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{--
                    @endif
            --}}
            <div class="block_items second_it">
                <div class="row">
                    {{--<h3><b>Мероприятия</b></h3>
                    <p>1. Групповые супервизии (<b>Юрий Ионов, Александр Климов</b>). </p>
                    <p>2. Интервизии (<b>Александр Никулин</b>). </p>
                    <p>3. Клинические разборы (<b>Борис Ершов</b>). </p>
                    <p>4. Авторские лекции. </p>
                    <p>5. Группа тренировки технических приемов (<b>Мария Макарова, Анастасия Соловьева, Алина Верзилова, Тамара Вахрамеева</b>). </p>
                    <p>6. Развитие профессиональной коммуникации (<b>Александр Никулин, Наталья Март</b>). </p>
                    <p>7. Семейная терапия: тренинг навыков (<b>Наталья Гликман, Александр Климов, Азизбек Ходихужаев</b>).</p>
                    <p>8. Балинтовская группа (<b>Полина Пантелеева, Дарья Костина, Роман Янтимиров</b>).</p>
                    <p></p>--}}
                    @use('App\Models\User', 'User')

                    @forelse ($blogs as $item)
                        @php $blogUser = User::find($item->user_id, ['firstname', 'lastname']);
                        @endphp


                        <div class="col-lg-4 mb-3">
                            <div class="item">
                                <img src="{{ asset('img/blog/'.$item->image) }}" alt="" class="img_st">
                                <div class="block_text">

                                    <h3><b>{{ $item->name }}</b></h3>
                                    <h6><b>Дата:</b> {{$item->formattedDate}}</h6>
                                    <h6><b>Продолжителность:</b> 120 Минут</h6>
                                    <h6><b>Организатор: </b> {{ $item->fio }} <img src="/img/sms.svg" alt=""></h6>
                                    <h6><b>Формат: </b> {{ $item->format->format }}</h6>
                                    <h6><b>Кол-во участников: </b> {{ $item->quantity ?? 'Без ограничений' }}</h6>
                                    <h6><b>Стоимость: </b> {{ $item->amount ?? 'Бесплатно' }}</h6>

                                </div>
                                <div class="block_text">
                                    @if(Auth::check() && (Auth::user()->id === $item->user_id || Auth::user()->group === 'admin'))
                                        <a style="background-color: green;display: inline;" href="/ourMeetings/edit/{{$item->id}}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                 fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                <path fill-rule="evenodd"
                                                      d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    <a href="/ourMeetings/{{$item->id}}" style="display: inline;margin-left: 12px;margin-right: 12px;padding-left: 50px;padding-right: 50px">Подробнее</a>


                                    @if(Auth::check() && (Auth::user()->id === $item->user_id || Auth::user()->group === 'admin'))
                                        <a style="background-color: red;display: inline;" onclick="window.location.href = '/ourMeetings/delete/{{$item->id}}'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                            </svg>
                                        </a>


                                    @endif
                                </div>
                            </div>
                        </div>



                        {{--
                                            <div class="col-lg-6">
                                            <div class="item">
                                                <img src="{{ asset('img/blog/'.$item->image) }}" alt="" class="img_st" style="height: 500px;object-fit: contain;">
                                                <div class="block_text">
                                                    <span>{{ $item->date }}</span>
                                                    <h3><a href="{{ route('ourMeetings.show', $item->id) }}" class="bg-white text-primary justify-content-start btn">{{ $item->name }}</a></h3>
                                                    <div class="block_blog">
                                                        <div class="blog_information">
                                                            <img src="/img/img_bl.svg" alt="">
                                                            <p>{{ $item->views }}</p>
                                                        </div>--}}
                        {{--
                                                        <div class="blog_information">
                                                            <img src="/img/img_bl1.svg" alt="">
                                                            <p>{{ $item->date }}</p>
                                                        </div>--}}{{--

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
                                                            </div>--}}
                        {{--
                                                            <div class="blog_information">
                                                                <img src="/img/img_bl1.svg" alt="">
                                                                <p>{{ $item->date }}</p>
                                                            </div>--}}{{--

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

                                                <p><button style="margin-left: 15px" onclick="window.location.href = '/ourMeetings/delete/{{$item->id}}'" id="delete-course" href="/ourMeetings/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                        </svg></button></p><br>
                                                    <p><button style="margin-left: 15px" onclick="window.location.href = '/ourMeetings/edit/{{$item->id}}'" href="/ourMeetings/edit/{{$item->id}}" type="button" class="btn btn-success" >Редактировать <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                            </svg></button></p><br>
                                                @endif
                                            </div>
                                        </div>
                        --}}
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
    <div class="courses_block">
        <div class="container">
            <a href="{{route('ourMeetings_previous')}}">Прошедшие встречи</a>
        </div>

    </div>
    <script>
        let addCategory

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('addMeet').addEventListener('click', function () {
                fetch('/ourMeetings/add', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                }).then(response => {
                    if (response.status === 200) {
                        window.location.href = "https://appp-psy.ru/ourMeetings/add"
                    }
                })
                    .catch(error => console.error('Fetch error:', error));
            })
            document.getElementById('type_meet').addEventListener('change', function () {
                let type = document.getElementById('type_meet').value
                console.log('type', type)
                if (type === "1") {
                    document.getElementById('place').style.display = 'none'
                } else if (type === "2") {
                    document.getElementById('place').style.display = 'block'
                }
            })

            document.getElementById('price').addEventListener('change', function () {
                let price = document.getElementById('price').value
                console.log('price', price)
                if (price === "paid") {
                    document.getElementById('from_price').style.display = 'block'
                    document.getElementById('to_price').style.display = 'block'
                } else if (price === "free") {
                    document.getElementById('from_price').style.display = 'none'
                    document.getElementById('to_price').style.display = 'none'
                }
            })
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

            form.action = '{{ route('blog.category.add') }}'

            name.value = ''
            status.selected = true

            button[0].classList.remove('d-none')
            button[1].classList.add('d-none')
        }


    </script>
@endsection
