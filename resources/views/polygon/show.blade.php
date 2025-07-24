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
            <li><a href="{{ route('courses') }}">Полигон <span>—</span></a></li>
            <li>{{ $course->title }}</li>
        </ul>
    </div>
</div>

<section class="block_metod">
    <div class="container">
        <div class="info">
            <div class="left_block_info">

                <div class="img_block" style="text-align: justify">
                    <img src="/images/{{$course->image}}" width='100%' alt="">
                </div><br>
                <h2>{{ $course->title ?? '' }}</h2>
                <div class="block_top">
                    <div class="work_time">
                        <img src="/img/img_cl.svg" alt="">
                        <p>{{ $course->times['start'] ?? "" }}</p>
                    </div>
                    <div class="work_time">
                        <img src="/img/img_cl1.svg" alt="">
                        <p>{{ $course->times['read'] ?? "" }}</p>
                    </div>
                </div>
                <div class="block_rice">
				<p><b>Формат:</b> {{$course->theory}}</p>
				</div>
					<div class="block_rice">
				<p><b>Стоимость:</b> {{$course->practice != "0" ? $course->practice." руб." : $product->name." подписка"}} </p>
				</div>
				<div class="block_rice">
				<p><b>Преподаватели:</b> {{$course->speakers}}</p>
				</div>
                <div class="block_rice">

                   {{-- <div class="join_a">
                        <a href="--}}{{--/courses/{{$course->id}}/subscribe--}}{{--{{$course->feedback}}">Записаться на курс </a>
                    </div>
--}}
                </div>
            </div>


        </div>
        <div class="block_item">
			{!! $course->text ?? '' !!}
		</div><hr>
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
                        <p>Обучение состоит из видео и синхронных онлайн-сессий 2 раза в неделю. Видеозаписи и материалы будут доступны 1 год после завершения программы.</p>
                    </div>
                    <div class="item">
                        <h3>Обратная связь</h3>
                        <p>Вы будете общаться с преподавателями на синхронных сессиях и через куратора группы, задавать вопросы по программе и итоговому проекту.</p>
                    </div>
                </div>
                <div class="items_group">
                    <div class="item">
                        <h3>Практика</h3>
                        <p>После каждого модуля тестирование, а в конце программы экзамен — защита индивидуального проекта.</p>
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
                <h2><span>Дипломы</span> и сертификаты</h2>
                <p>Вы получите 2 диплома о профессиональной переподготовке, <br> а также Международный Сертификат.</p>
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
                            Какой график обучения на платформе? Получится ли совмещать его с работой?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Сколько часов в неделю мне нужно будет уделять обучению на платформе?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Есть ли стажировки на программе?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                            Есть ли требования для поступления?
                        </button>
                    </h2>
                    <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Синхронные онлайн-занятия будут проходить 2 раза в неделю: вечером в будние дни и утром по субботам. Практика в тройках и работа с супервизором будет проходить 1−2 раза в неделю по будням или выходным. Также вы будете изучать заранее записанные материалы курса в удобном вам режиме, совмещая обучение на платформе с работой и личной жизнью. Все видео будут доступны и по окончании курса, так что вы сможете освежить свои знания в любой момент.
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
            <h2>Напишите нам — мы <span>на связи</span>!</h2>
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
