@extends('app', [

    'title' => $course->title,

    'keywords' => '', # Ключевые слова

    'description' => '' # Описание страницы

])



@section('content')

    <div class="bread_crumb">

        <div class="container">

            <ul>

                <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>

                <li><a href="{{ route('courses') }}">Курсы <span>—</span></a></li>

                <li title="{{ $course->course ? $course->course->title : $course->title ?? 'Курс' }}">

                    {{ $course->course ? mb_strimwidth($course->course->title, 0, 50, "...") : mb_strimwidth($course->title ?? 'Курс', 0, 50, "...") }}

                </li>

            </ul>

        </div>

    </div>

    <style>

        li {

            list-style: initial;

        }

    </style>

    <section class="block_metod">

        <div class="container">

            <div class="info">

                <div class="left_block_info">



                    <div class="img_block" style="text-align: justify">
                        @if($course->course && $course->course->image)
                         <img src="/images/{{$course->course->image}}" width='100%' alt="">
                        @endif
                    </div>

                    <br>

                    <h2>{{ $course->course->title ?? '' }}</h2>

                    <h2>{{ $course->section ?? '' }}</h2>

                    <p>{{ $course->title ?? '' }}</p>

                    <p>{{ $course->description ?? '' }}</p>

                    <div class="block_top">

                        <div class="work_time">

                            <img src="/img/img_cl.svg" alt="">

                            <p>{{ date('d.m.y',strtotime($course->date))." ".date('H:i',strtotime($course->start_time)) ?? "" }}</p>

                        </div>

                        {{--<div class="work_time">

                            <img src="/img/img_cl1.svg" alt="">

                            <p>{{ $course->times['read'] ?? "" }}</p>

                        </div>--}}

                    </div>

                    <div class="block_rice">

                        <p><b>Формат:</b> ZOOM</p>

                    </div>

                    <div class="block_rice">

                        <p>

                            <b>Стоимость:</b> 

                            @if($course->course && $course->course->practice != "0")

                                {{ $course->course->practice }} руб.

                            @else

                                {{ isset($product) && $product ? 'Подписка "'.$product->name.'"' : 'Подписка' }}

                            @endif

                        </p>

                    </div>

                    <div class="block_rice">

                        <p><b>Преподаватели:</b> {{$course->speakers ?? 'Не указаны'}}</p>

                    </div>

                    <div class="block_rice">



                        @if(!auth()->user())

                            {{--

                            <div class="join_a" id="auth">

                                <strong style="color: red">Чтобы получить доступ к контенту, нужно зарегистрироваться и оплатить подписку</strong>

                                <a href="/introduction" style="cursor: pointer" class="register">Зарегистрироваться</a>

                            </div>

                            --}}

                            <strong style="color: red;font-size: 18px">Чтобы получить доступ к контенту, нужно

                                зарегистрироваться и оплатить подписку</strong>

                            <div>

                                <div class="join_a" id="auth"><a href="/introduction" class="register">Зарегистрироваться</a>

                                </div>

                            </div>

                        @else

                            {{--@if(\App\Models\Subscription::where("user_id", auth()->user()->id)->first()->level>0)

                                @if(\App\Models\ParticipantActions::where('user_id',auth()->user()->id)->where('object_name','courses')->where('object_id',$course->id))

                                    <div class="join_a">

                                        <a style="cursor: pointer" href="{{$feedback->feedback}}">Ссылка на ZOOM </a>

                                    </div>

                                @endif

                            @endif--}}

                                                

          

                            @php 

                                $userSubscription = \App\Models\Subscription::where('user_id', auth()->user()->id)->first();

                                $userLevel = $userSubscription ? $userSubscription->level : 0;

                                

                                // Проверяем, является ли уровень подписки пробным (-1) или выше нуля

                                $hasAccess = ($userLevel == -1 || $userLevel > 0);

                                

                                // Для подписок выше нуля, но не пробных, проверяем разрешения

                                if ($userLevel > 0 && $userLevel != -1) {

                                    $productPermission = \App\Models\ProductPermission::join('products','products.id','=','product_permissions.product_id')

                                        ->where('products.level', $userLevel)

                                        ->first();

                                    

                                    $hasAccess = $productPermission && $productPermission->course;

                                }

                                // Новый код: получаем продукт с level=5

                                $level5Product = \App\Models\Product::where('level', 5)->first();

                            @endphp

                            

                            @if(Auth::check() && $hasAccess)

                                <div class="join_a">

                                    @if($feedback && isset($feedback->feedback))

                                        <a style="cursor: pointer" href="{{$feedback->feedback}}">Ссылка на ZOOM </a>

                                    @else

                                        <a style="cursor: pointer">Ссылка на ZOOM будет доступна позже</a>

                                    @endif

                                </div>

                            @else

                                <div class="join_a" id="low_level">

                                    <a href="/profile#subscription-container" style="cursor: pointer" id="join_course_low_level">Для записи на курс необходимо подключить подписку <strong>{{ isset($level5Product) && $level5Product ? $level5Product->name : 'подходящего уровня' }}</strong></a>

                                </div>

                            @endif

                        @endif





                        <!-- Модальное окно -->

                        <div class="modal fade" id="subscriptionModal" tabindex="-1"

                             aria-labelledby="subscriptionModalLabel" aria-hidden="true">

                            <div class="modal-dialog modal-dialog-centered">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title" id="subscriptionModalLabel">Необходима подписка</h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal"

                                                aria-label="Закрыть"></button>

                                    </div>

                                    <div class="modal-body">

                                        Для записи в курс вам нужно повысить уровень подписки. Уровень подписки

                                        можно повысить в <a href="/profile">личном кабинете</a>.

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                            Закрыть

                                        </button>

                                        <a href="/profile" class="btn btn-primary">Перейти в личный кабинет</a>

                                    </div>

                                </div>

                            </div>

                        </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>





                        <script>

                            $(document).ready(function () {



                                $("#low_level").on("click", function (e) {

                                    $("#subscriptionModal").modal("show")

                                })



                                $("#join_a").on("click", function (e) {

                                    debugger;

                                    e.preventDefault();



                                    $.ajaxSetup({

                                        headers: {

                                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                                        }

                                    });

                                    $.ajax({

                                        url: '/takePart/courses/{{$course->id}}', // URL вашего маршрута

                                        type: 'GET',

                                        //contentType: 'application/json',

                                        data: {},

                                        success: function (response) {

                                            debugger;

                                            window.location.href = '{{$course->feedback}}';

                                        },

                                        error: function (xhr) {

                                            alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки

                                        }

                                    });





                                })



                            })

                        </script>



                    </div>

                </div>





            </div>

            <div class="block_item">

                {!! $course->text ?? '' !!}

            </div>

            <hr>

            <style>/*

            .block_item p {

                position: relative;

                width: 100%; !* Занимает всю ширину родительского контейнера *!

                padding-bottom: 56.25%; !* Соотношение сторон 16:9 (высота / ширина) *!

                height: 0;

                overflow: hidden;

            }



            .block_item p iframe {

                position: absolute;

                top: 0;

                left: 0;

                width: 100%;

                height: 100%;

                border: 0;

            }*/

                iframe {

                    width: 100%;

                    height: 100%;

                    aspect-ratio: 16 / 9; /* Соотношение 16:9 */

                    border: none;

                }

            </style>

            @if(!empty($course->video))

                <video width="640" height="360" controls>

                    <source src="https://appp-psy.ru/images/{{$course->video}}" type="video/mp4">

                    Your browser does not support the video tag.

                </video>

            @else

                {{--<h3>Видео отсутствует</h3>--}}

            @endif



        </div>

    </section>

    <!--

    <section class="what_class_teach_you">

        <div class="container">

            <div class="teach_you">

                <div class="title_block">

                    <h2>Чему вас <span>научит курс</span></h2>

                </div>

                <div class="block_item">

                    <div class="row">

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>1</h3>

                                    <h4>Основы когнитивно- <br> поведенческой терапии</h4>

                                </div>

                                <p>Погружение в теоретические основы КПТ, изучение ключевых концепций и принципов.</p>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>2</h3>

                                    <h4>Оценка когнитивных <br>

                                        и поведенческих паттернов</h4>

                                </div>

                                <p>Узнайте, как проводить оценку мыслей, эмоций и поведения клиентов.</p>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>3</h3>

                                    <h4>Разработка индивидуальных <br> терапевтических стратегий</h4>

                                </div>

                                <p>Научитесь создавать персонализированные программы терапии для каждого клиента.</p>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>4</h3>

                                    <h4>Техники когнитивной <br> реструктуризации</h4>

                                </div>

                                <p>Изучите методы изменения негативных мыслей и установок.</p>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>5</h3>

                                    <h4>Работа с поведенческими <br> паттернами</h4>

                                </div>

                                <p>Освойте методы модификации нежелательного поведения.</p>

                            </div>

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <div class="item">

                                <div class="block_top">

                                    <h3>6</h3>

                                    <h4>Практические навыки <br>

                                        в применении КПТ</h4>

                                </div>

                                <p>Получите опыт работы с клиентами

                                    на основе реальных кейсов.</p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="course_speakers_block">

        <div class="container">

            <div class="course_block">

                <div class="title_block">

                    <h2><span>Спикеры</span> курса</h2>

                    <p>Опытные психологи и практикующие терапевты, готовые <br> поделиться своими знаниями и навыками.</p>

                </div>

                <div class="block_cpeakers">

                    <div class="swiper mySwiper3">

                        <div class="swiper-wrapper">

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Мария Зиновьева</h3>

                                        <p>Психолог, специалист

                                            по семейным и детско-родительским отношениям</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp1.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Григорий Беляков</h3>

                                        <p>Доктор психологических наук, преподаватель МГУ</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Мария Зиновьева</h3>

                                        <p>Психолог, специалист

                                            по семейным и детско-родительским отношениям</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp1.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Григорий Беляков</h3>

                                        <p>Доктор психологических наук, преподаватель МГУ</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Мария Зиновьева</h3>

                                        <p>Психолог, специалист

                                            по семейным и детско-родительским отношениям</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp1.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Григорий Беляков</h3>

                                        <p>Доктор психологических наук, преподаватель МГУ</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Мария Зиновьева</h3>

                                        <p>Психолог, специалист

                                            по семейным и детско-родительским отношениям</p>

                                    </div>

                                </div>

                            </div>

                            <div class="swiper-slide">

                                <div class="item">

                                    <img src="/img/img_sp1.png" alt="" class="img_sp">

                                    <div class="block_text">

                                        <h3>Григорий Беляков</h3>

                                        <p>Доктор психологических наук, преподаватель МГУ</p>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="swiper-pagination"></div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="training_works">

        <div class="container">

            <div class="training_block">

                <div class="title_block">

                    <h2>Как проходит <span>обучение</span></h2>

                </div>

                <div class="block_items">

                    <div class="items_group">

                        <div class="item first_item">

                            <h3>Теория</h3>

                            <p>Обучение состоит из видео и синхронных онлайн-сессий 2 раза в неделю. Видеозаписи и материалы будут доступны 1 год после завершения программы.</p>

                        </div>

                        <div class="item">

                            <h3>Обратная связь</h3>

                            <p>Вы будете общаться с преподавателями на синхронных сессиях и через куратора группы, задавать вопросы по программе и итоговому проекту.</p>

                        </div>

                    </div>

                    <div class="items_group">

                        <div class="item">

                            <h3>Практика</h3>

                            <p>После каждого модуля тестирование, а в конце программы экзамен — защита индивидуального проекта.</p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="diplomas_and_certificates">

        <div class="container">

            <div class="froup_sertificates">

                <div class="title_block">

                    <h2><span>Дипломы</span> и сертификаты</h2>

                    <p>Вы получите 2 диплома о профессиональной переподготовке, <br> а также Международный Сертификат.</p>

                </div>

                <div class="group_img">

                    <div class="row">

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas.png" alt="">

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas1.png" alt="">

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas2.png" alt="">

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas3.png" alt="">

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas4.png" alt="">

                        </div>

                        <div class="col-lg-4 col-md-6">

                            <img src="/img/diplomas5.png" alt="">

                        </div>

                    </div>

                </div>

                <div class="group_img mob">

                    <div class="swiper mySwiper4">

                        <div class="swiper-wrapper">

                            <div class="swiper-slide">

                                <img src="/img/diplomas.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas1.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas2.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas3.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas4.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas5.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas1.png" alt="">

                            </div>

                            <div class="swiper-slide">

                                <img src="/img/diplomas2.png" alt="">

                            </div>

                        </div>

                        <div class="swiper-pagination"></div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="feedbacks">

        <div class="container">

            <div class="feedbacks_block">

                <div class="title_block">

                    <h2>Отзывы на <span>частые вопросы</span></h2>

                </div>

                <div class="accordion" id="accordionExample">

                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">

                                Какой график обучения на платформе? Получится ли совмещать его с работой?

                            </button>

                        </h2>

                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">

                            <div class="accordion-body">

                                Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.

                            </div>

                        </div>

                    </div>

                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">

                                Сколько часов в неделю мне нужно будет уделять обучению на платформе?

                            </button>

                        </h2>

                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">

                            <div class="accordion-body">

                                Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.

                            </div>

                        </div>

                    </div>

                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">

                                Есть ли стажировки на программе?

                            </button>

                        </h2>

                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">

                            <div class="accordion-body">

                                Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.

                            </div>

                        </div>

                    </div>

                    <div class="accordion-item">

                        <h2 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">

                                Есть ли требования для поступления?

                            </button>

                        </h2>

                        <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#accordionExample">

                            <div class="accordion-body">

                                Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="block_connect">

        <div class="container">

            <div class="connecting">

                <h2>Напишите нам — мы <span>на связи</span>!</h2>

                <p>Если у вас есть вопросы или вам нужна дополнительная <br> информация, не стесняйтесь связаться с нами.</p>

                <div class="block_img_conn">

                    <div class="group_t">

                        <a href="#"><img src="/img/connect.svg" alt=""></a>

                        <p>Telegram</p>

                    </div>

                    <div class="group_t">

                        <a href="#"><img src="/img/connect1.svg" alt=""></a>

                        <p>WhatsApp</p>

                    </div>

                </div>

            </div>

        </div>



    </section>-->

    <style>

        table {

            width: 100%;

            margin-bottom: 20px;

            border: 1px solid #dddddd;

            border-collapse: collapse;

        }



        table th {

            font-weight: bold;

            padding: 5px;

            background: #efefef;

            border: 1px solid #dddddd;

        }



        table td {

            border: 1px solid #dddddd;

            padding: 5px;

        }

    </style>

@endsection

