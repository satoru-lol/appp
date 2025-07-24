@extends('app')

@section('content')
    <section class="home" style='position: relative;overflow: hidden;'>
        <div class="container">
            <div class="home_block" >
                <div class="row">
                    <div class="col-lg-7">
                        <div class="text_block">
                            <h1><span>Сообщество экспертов</span> психологической поддержки</h1>
                            <p class="citate">Мы верим в силу совместных усилий.<br> Присоединяйтесь
                                к ассоциации,<br> где каждый играет роль в<br> росте и развитии.</p>
                            @guest
                                <a href="{{ route('introduction') }}">Присоединиться</a>
                            @endguest
                        </div>
                    </div>

                    <style>
                        @media (max-width: 900px) {
                            .home .home_block .block_img img {
                                margin-left: 0;
                                width: 45%;
                                margin-top: 37px;
                            }
                        }
                    </style>
                    <div class="col-lg-5">
                        <div class="">
                            <img class="klimov" src="/img/klimov.webp" alt="" style="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="who_we">
        <div class="container">
            <div class="block_who_we">
                <h2>Кто мы<span></span>?</h2>
                <p>Ассоциация частнопрактикующих психологов и психотерапевтов (АЧПП) – пространство, объединяющее психологов и психотерапевтов, реализующих свои услуги в формате частной практики.<br><br>

                    Цель Ассоциации: всесторонняя помощь психологам и психотерапевтам в открытии и развитии частной практики.<br><br>

                    Ключевые задачи Ассоциации:<br>
                    ✔️ Содействие в профессиональном развитии специалистов.<br>
                    ✔️ Содействие в профессиональном самоопределении специалистов, включая понимание:<br>
                    - собственных профессиональных оснований для работы;<br>
                    - проблем, с которыми специалист готов работать;<br>
                    - «своих» и «не своих» клиентов;<br>
                    ✔️ Содействие в профессиональном самоописании и самопредъявлении, включая создание текста о специалисте, создание контент-плана, выбора площадки для размещения своих статей, аудио и видео материалов.<br>
                    ✔️ Содействие в продвижении и привлечении клиентов.<br>
                    ✔️ Содействие в расширении системы научно-непротиворечивого психологического просвещения населения о современных психологических и психотерапевтических представлениях о различных сторонах жизни.</p>
            </div>
        </div>
    </section>

    @use('App\Models\User', 'User')
    @use('App\Models\SpecialistCategory', 'SpecCategory')
    @use('Carbon\Carbon', 'Carbon')

    <section class="specialists">
        <div class="container">
            {{--<div class="specialists_block">
                <div class="title_block">
                    <h2>Специалисты <span>ассоциации</span></h2>
                    <p>Каждый прошел строгий отбор и имеет высокие стандарты <br> качества работы.</p>
                </div>
                <div class="block_items" style='background-image: none'>
                    <div class="row">
                        @forelse ($specialists as $item)
                            @php  $user = User::find($item->user_id)->first();
                    $category = SpecCategory::find($item->specialist_category_id)->first();
                    $category['name'] = ucfirst(strtolower($category->name));
                            @endphp
                            <div class="col-lg-3 col-md-6  mb-3">
                                <div class="item">
                                    <img src="/avatar/{{ $item->user_id }}" alt="" class="img_sp">
                                    <div class="block_text">
                                        <h3>{{ $user->firstname.' '.$user->lastname }}</h3>
                                        <div class="block_inf gap-2">
                                            <span class="me-0">стаж {{ $item->experience }} лет</span>
                                            <img src="/img/tochka.svg" alt="">
                                            <span>{{ $item->location }}</span>
                                        </div>
                                        <div class="block_min">
                                            <p>{{ $item->prices['online'] }}</p>
                                            <img src="/img/sms.svg" alt="">
                                            <span>/≈ {{ $item->time['online'] }} мин</span>
                                        </div>
                                        <a href="/specialists/{{ $item->id }}">Подробнее</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center">
                                <div class="alert alert-warning">Специалисты отсутствуют</div>
                            </div>
                        @endforelse
                    </div>
                    <div class="oll_specialists">
                        <a href="/specialists">Все специалисты</a>
                    </div>
                </div>
                <div class="block_items mob" >
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            @forelse ($specialists as $item)
                                <div class="swiper-slide">
                                    <div class="item">
                                        <img src="/avatar/{{ $item->user_id }}" alt="" class="img_sp">
                                        <div class="block_text">
                                            <h3>{{ $user->firstname.' '.$user->lastname }}</h3>
                                            <div class="block_inf">
                                                <span>стаж {{ $item->experience }} лет</span>
                                                <span>{{ $item->location }}</span>
                                            </div>
                                            <div class="block_min">
                                                <p>{{ $item->prices['online'] }}</p>
                                                <img src="/img/sms.svg" alt="">
                                                <span>/≈ {{ $item->time['online'] }} мин</span>
                                            </div>
                                            <a href="/specialists/{{ $item->id }}">Подробнее</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="swiper-slide text-center">
                                    <div class="alert alert-warning">Специалисты отсутствуют</div>
                                </div>
                            @endforelse
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>--}}

{{--
    <section class="courses">
        <div class="container">
            <div class="block_courses mob">
                <div class="title_block">
                    <h2>Курсы ассоциации<span></span> </h2>
                    <p>Обучение проводится опытными наставниками <br>
                        и практикующими психологами.</p>
                </div>
                @if(Auth::check() && Auth::user()->group === 'admin')
--}}
{{--
                <p><button style="margin-left: 15px" onclick="window.location.href = '/courses/add/'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-success" >Добавить курс  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-plus-fill" viewBox="0 0 16 16">
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M8.5 7v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 1 0"/>
                        </svg></button></p>--}}{{--


                @endif
                <div class="block_items" >
                    <div class="swiper mySwiper1">
                        <div class="swiper-wrapper">
                            @forelse ($courses as $item)
                                @php $user = User::find($item->user_id);
                                @endphp
                                <div class="swiper-slide">
                                    <div class="item">
                                        <img src="/images/{{ $item->image }}" alt="" class="img_sp">
                                        <div class="block_text">

                                            <h3><b>{{ $item->title }}</b></h3>
                                            <p><img src="/img/img_cl.svg" alt="">{{ json_decode($item->times)->start }}</p>
                                            <p><img src="/img/img_cl1.svg" alt="">{{ json_decode($item->times)->read }}</p>
                                            <h6>{{ $item->speakers }} <img src="/img/sms.svg" alt=""></h6>
                                            <a href="/courses/{{$item->id}}">Подробнее</a><br>
                                            @if(Auth::check() && Auth::user()->group === 'admin')
                                                <a style="background-color: green" href="/course/edit/{{$item->id}}">Редактировать <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
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
                            @empty
                                <div class="swiper-slide text-center">
                                    <div class="alert alert-warning">Курсы отсутствуют</div>
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="block_items mob">
                    <div class="swiper mySwiper1">
                        <div class="swiper-wrapper">
                            @forelse ($courses as $item)
                                @php $user = User::find($item->user_id);
                                @endphp
                                <div class="swiper-slide">
                                    <div class="item">
                                        <img src="/images/{{ $item->image }}" alt="" class="img_sp">
                                        <div class="block_text">

                                            <h3><b>{{ $item->title }}</b></h3>
                                            <p><img src="/img/img_cl.svg" alt="">{{ json_decode($item->times)->start }}</p>
                                            <p><img src="/img/img_cl1.svg" alt="">{{ json_decode($item->times)->read }}</p>
                                            <h6>{{ $item->speakers }} <img src="/img/sms.svg" alt=""></h6>
                                            <a href="/courses/{{$item->id}}">Подробнее</a><br>
                                            @if(Auth::check() && Auth::user()->group === 'admin')
                                                <a style="background-color: green" href="/course/edit/{{$item->id}}">Редактировать <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
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
                            @empty
                                <div class="swiper-slide text-center">
                                    <div class="alert alert-warning">Курсы отсутствуют</div>
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="oll_courses">
                    <a href="/courses">Все курсы</a>
                </div>
            </div>
        </div>
    </section>
--}}
    <section class="third_levels" id="third_levels">
        <div class="container">
            <div class="levels">
              {{--  <div class="title_block">
                    <h2>3 уровня подписки<span></span></h2>
                </div>--}}
               {{-- <div class="items">
                    <div class="row" style="display: flex; justify-content: space-evenly">
                        @foreach(\App\Models\Product::all() as $product)
                            <div class="col-lg-6" style="margin-top: 15px">
                                <div class="item">
                                    <h3>{{$product->name}}</h3>
                                    <p>Цена: {{$product->price}} руб.</p>
                                    <button class="btn btn-success pay-button" data-amount="{{$product->price}}" data-id="{{$product->id}}">Оплатить</button><br><br>
                                    <p>{{$product->description}}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>--}}
					<section class="who_we">
        <div class="container">
            <div class="block_who_we" style="text-align: left">
                <h2 >Участие в Ассоциации позволит вам<span></span> </h2>
                <p>	<b style="font-size: 20px; font-weight: bold">1. Регулярно получать уникальный, интересный и актуальный контент.</b><br /><br />
                    <b style="font-size: 20px; font-weight: bold">2. Общаться с коллегами на профессиональные и личные темы, находить партнеров, друзей, единомышленников.</b> <br /><br />
                    <b style="font-size: 20px; font-weight: bold">3. Развиваться как специалист:</b> <br /><br />
				✔ участвовать в супервизионных и интервизионных группах от Ассоциации; <br />

						✔ учиться на специальных тематических вебинарах Ассоциации; <br />

						✔ общаться в группах единомышленников для совместной рефлексии профессионального опыта и тренировки технических приемов отдельных направлений и методов психотерапии; <br />

						✔ попробовать себя в роли преподавателя с дальнейшими перспективами проведения собственных программ от Ассоциации и Института; <br />

						✔ найти для себя учителя либо самому выступить в роли учителя для начинающих коллег; <br />

						✔ публиковать свои научные труды и практические кейсы на ресурсах Ассоциации; <br />

						✔ выступать в качестве приглашенных гостей на круглых столах, в дискуссиях по конкретным направлениям и публиковаться на ресурсах Ассоциации; <br />

						✔ принимать участие в составе коллектива авторов в издании ежемесячного/квартального журнала Ассоциации; <br />

						✔ принимать участие в составе коллектива авторов в создании учебной и научной литературы по направлениям. <br />
						<br />	<b style="font-size: 20px; font-weight: bold">4. Получать трафик и запросы на проведение консультации, создавая уникальный контент, популяризирующий те или иные аспекты психологического и психотерапевтического знания.
                    </b><br /><br />
                    <b style="font-size: 20px; font-weight: bold">5. Приятно проводить время на очных встречах (чаепитие, бар, фотосессии, книжные и киноклубы, туристические походы).</b>
 <br /><br />
                    <b style="font-size: 20px; font-weight: bold">6. Участвовать в волонтерских и социальных проектах Ассоциации.</b>

				</p>
			</div>
        </div>
    </section>


                </div>
            </div>
        </div>
    </section>
    {{--<section class="join_us">
        <div class="container">
            <div class="join_block">
                <h2><br><br><br><br><br></h2>
                <a  href="/club">Подробнее о клубах</a>
            </div>
        </div>
    </section>--}}

  <!--  <section class="blog_news">
        <div class="container">
            <div class="news_block">
                <div class="title_block">
                    <h2>Блог и новости<span></span></h2>
                    <p>Интересные статьи, видеоуроки по психологии и новости <br> Ассоциации.</p>
                </div>
                <div class="block_items">
                    <div class="row">
                        @if (auth()->check()
                            ? ($roleContent = \App\Models\RoleContent::where('value', 'Blog')->first()) && 
                              ($subscription = \App\Models\Subscription::where('user_id', auth()->user()->id)->first()) && 
                              $roleContent->level <= $subscription->level
                            : ($roleContent = \App\Models\RoleContent::where('value', 'Blog')->first()) && 
                              $roleContent->level === 0)
                            @forelse ($blogs as $item)
                                @php $blogUser = User::find($item->user_id, ['firstname', 'lastname']);
                                @endphp
                            <br>
                                <div class="col-lg-4 mb-5">
                                    <div class="item">
                                        <img src="{{ asset('img/blog/'.$item->image) }}" alt="" class="img_st" style="width: 100%;object-fit: contain;">
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
                                                    <p style="display: inline; cursor: pointer;" data-toggle="modal" data-target="#donationModal" >Донат</p>
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

                                                </div>
                                            </div>
                                        </div>
                                        @if(Auth::check() && Auth::user()->group === 'admin')

                                            <p><button style="margin-left: 15px" onclick="window.location.href = '/regmerop/delete/{{$item->id}}'" id="delete-course" href="/courses/delete/{{$item->id}}" type="button" class="btn btn-danger" >Удалить  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                                                    </svg></button></p><br>
                                            <p><button style="margin-left: 15px" onclick="window.location.href = '/blog/edit/{{$item->id}}'" id="edit-blog" href="/blog/edit/{{$item->id}}" type="button" class="btn btn-success" >Редактировать  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                                    </svg></button></p>

                                        @endif
                                        <br>


                                </div>
                    </div>

                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">
                                {{ __('Отсутствуют') }}
                            </div>
                        </div>
                    @endforelse

                    @else

                        <div class="col-12">
                            <div class="alert alert-warning">
                                Контент доступен только по подписке
                            </div>
                        </div>

                    @endif
                </div>
            </div>
            <div class="more_articles">
                <a href="/blog">Больше статей</a>
            </div>
        </div>
        </div>
    </section>-->
    @guest
        <section class="block_connect">
            <div class="container">
                <div class="connecting">
                    <h2><span>Присоединяйтесь</span> к нам</h2>
                    <p>Регистрируйтесь на сайте, чтобы получить доступ к услугам <br> наших психологов, участвовать в дискуссиях и мероприятиях <br>
                        и быть в курсе последних новостей и событий ассоциации.</p>
                    <a href="https://appp-psy.ru/introduction" class="register">Зарегистрироваться</a>
                    <div class="custom-card">
                        <img src="/img/reg-photo.webp" alt="Beautiful Image">
                    </div>
                    <style>
                        /* Основной стиль карточки */
                        .custom-card {
                            max-width: 100%;
                            margin: 20px auto;
                            border-radius: 15px;
                            overflow: hidden;
                            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                            transition: transform 0.3s ease, box-shadow 0.3s ease;
                        }

                        /* Изображение карточки */
                        .custom-card img {
                            width: 100%;
                            height: auto;
                            border-radius: 15px;
                            display: block;
                        }

                        /* Дополнительные стили для небольших экранов */
                        @media (min-width: 768px) {
                            .custom-card {
                                max-width: 70%;
                            }
                        }

                        @media (min-width: 1024px) {
                            .custom-card {
                                max-width: 50%;
                            }
                        }

                    </style>
                    <h2>Напишите нам — мы <span>на связи</span>!</h2>
                    <p>Если у вас есть вопросы или вам нужна дополнительная <br> информация, не стесняйтесь связаться с нами.</p>
                    <div class="block_img_conn">

                      {{--  <div class="group_t">
                            <a target="_blank" href="https://t.me/+WqnwojGKWjJkNDAy"><img src="/img/connect.svg" alt=""></a>
                            <p>Telegram</p>
                        </div>--}}{{--
                        <div class="group_t">
                            <a target="_blank" href="https://vk.com/associacia_chpp"><img src="/img/connect1.svg" alt=""></a>
                            <p>WhatsApp</p>
                        </div>--}}
                    </div>
                </div>
            </div>
        </section>
    @endguest
@endsection
