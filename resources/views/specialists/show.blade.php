@use('App\Models\User', 'User')
@use('App\Models\SpecialistCategory', 'SpecialistCategory')
@use('App\Models\SpecialistReview', 'SpecialistReview')
@use('App\Models\Blog', 'Blog')
@use('Carbon\Carbon', 'Carbon')

@php
	
    $specialistUser = User::findOrFail($specialist->user_id, ['firstname', 'lastname']);
    $category = SpecialistCategory::find($specialist->specialist_category_id)->first();
    $specialist->prices = $specialist->prices;
    $specialist->time = $specialist->time;
    $category['name'] = ucfirst(strtolower($category->name));

    $articles = Blog::where('user_id', $specialist->user_id)->where('blog_category_id', 2)->where('status', 1)->get();

    if ($user && $user->group === 'admin')
        $reviews = SpecialistReview::where('specialist_id', $specialist->id)->orderBy('created_at')->get();
    else
        $reviews = SpecialistReview::where('specialist_id', $specialist->id)->orderBy('created_at')->get();
@endphp

@extends('app', [
    'title' => 'Специалист - '.($specialistUser->firstname.' '.$specialistUser->lastname),
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])
@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('specialists') }}">Все специалисты <span>—</span></a></li>
            <li>{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</li>
        </ul>
    </div>
</div>

<section class="block_about_phychologist">
    <div class="container">
        <div class="about_block">
            <div class="row">
                <div class="col-lg-4">
                    <div class="block_img">
                        <h2 class="name_phy">{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</h2>
                        <img src="/avatar/{{ $specialist->user_id }}" width='100%' alt="">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="block_right">
                        <div class="block_left_big_text">
                            <h2 class="des_h2">{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</h2>
                            <div class="block_psychologist">
                                <p>{{ $specialist->name }}</p>
                                <img src="/img/tochka.svg" alt="">
                                <p>{{ Carbon::parse($specialist->birthday)->age }} лет</p>
                                <img src="/img/tochka.svg" alt="">
                                <p>{{ $specialist->degree }}</p>
                                <img src="/img/tochka.svg" alt="">
                                <p>стаж {{ $specialist->experience }} лет</p>
                            </div>
                            <div class="span_block">
                                <div class="group_sen">
                                    <span>Стоимость онлайн:</span>
                                    <div class="about_sen">
                                        <h4>{{ $specialist->prices['online'] }}</h4>
                                        <img src="/img/sms.svg" alt="">
                                        <h5>/≈ {{ $specialist->time['online'] }} мин</h5>
                                    </div>
                                </div>
                                <div class="group_sen">
                                    <span>Стоимость личного приема:</span>
                                    <div class="about_sen">
                                        <h4>{{ $specialist->prices['reception'] }}</h4>
                                        <img src="/img/sms.svg" alt="">
                                        <h5>/≈ {{ $specialist->time['reception'] }} мин</h5>
                                    </div>
                                </div>
                            </div>
                            @if($specialist->free_time !== 0)
                            <div class="block_clock">
                                <img src="/img/clock.svg" alt="">
                                <span>Первая {{ $specialist->free_time }}-минутная консультация бесплатно</span>
                            </div>
                            @endif
                            <p><span>{{ $specialist->location }}</span>.</p>
                            <p><span>Формат работы:</span> аудио, видео, чат</p>
                            <p><span>Язык общения:</span> русский</p>
                        </div>
                        {{--
                        <div class="block_right_mini_text">
                            <div class="block_name">
                                <a href="#">Бонусная программа</a>
                                <a href="#">Подписка</a>
                            </div>
                        </div>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="about_me_block">
    <div class="container">
        <div class="about_text">
            <div class="left_text">
                <h3>Коротко о себе:</h3>
                <p>{{ $specialist->about }}</p>
            </div>
            <a href="javascript:;"><img src="/avatar/{{ $specialist->user_id }}" alt=""></a>
        </div>
    </div>
</section>
<section class="commends_spess">
    <div class="container">
        <div class="block_cpmmend">
            <div class="title_block">
                <h2>Отзывы <span>клиентов</span></h2>
            </div>
            <div class="items">
                <div class="row">
                    @forelse ($reviews as $item)
                    @php $reviewUser = User::find($item->user_id);
                    @endphp
                    <div class="col-lg-6">
                        <div class="item">
                            <div class="block_top">
                                <div class="block_left_about_women">
                                    <img src="/avatar/{{ $item->user_id }}" alt="" class="women">
                                    <div class="block_commends_womens">
                                        <span></span>
                                        <h3>{{ $reviewUser->firstname }} {{ $reviewUser->lastname }}</h3>
                                        <div class="bottom_right_block">
                                            {{--
                                            <img src="/img/icon_right.svg" alt="">
                                            <h5><span>5 000</span> /≈ 50 </h5>
                                            --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="stars_block">
                                    <p>Отлично</p>
                                    <div class="stars">
                                        @for($i = 0; $i <= $item->grade; $i++)
                                        <img src="/img/star.svg" alt="">
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="text_center">
                                <p>{{ $item->text }}</p>
                            </div>
                            <div class="block_bottom">
                                <span>{{ $item->created_at }}</span>
                                {{--
                                <h6><img src="/img/checked.svg" alt="">Прием подтвержден</h6>
                                <a href="#">Подробнее о специалисте</a>
                                --}}
                            </div>
                        </div>
                        <div class="item mob">
                            <div class="block_top">
                                <div class="block_left_about_women">
                                    <img src="/avatar/{{ $item->user_id }}" alt="" class="women">
                                    <div class="block_commends_womens">
                                        <span></span>
                                        <h3>{{ $reviewUser->firstname }} {{ $reviewUser->lastname }}</h3>
                                        <div class="bottom_right_block">
                                            {{--
                                            <img src="/img/icon_right.svg" alt="">
                                            <h5><span>5 000</span> /≈ 50 </h5>
                                            --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text_center">
                                <p>{{ $item->text }}</p>
                            </div>
                            <div class="block_bottom">
                                <span>25.01.2024</span>
                                {{--
                                <h6><img src="/img/checked.svg" alt="">Прием подтвержден</h6>
                                --}}
                            </div>
                            <div class="stars_block">
                                <p>Отлично</p>
                                <div class="stars">
                                    @for($i = 0; $i <= $item->grade; $i++)
                                    <img src="/img/star.svg" alt="">
                                    @endfor
                                </div>
                            </div>
                            {{--
                            <a href="#">Подробнее о специалисте</a>
                            --}}
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            {{ __('Отзывы отсутствуют') }}
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<section class="specialist_articless">
    <div class="container">
        <div class="articless">
            <div class="title_block">
                <h2><span>Статьи</span> специалиста</h2>
            </div>
            <div class="block_items">
                <div class="row">
                    @forelse ($articles as $item)
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
                                        <p>{{ $user->firstname }} {{ $user->lastname }}</p>
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
                                        <p>{{ $specialistUser->firstname }} {{ $specialistUser->lastname }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            {{ __('Статьи отсутствуют') }}
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection