@extends('app', [
    'title' => $blog->name,
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
@use('App\Models\User', 'User')
@use('App\Models\BlogContent', 'BlogContent')

@php
	if($blog->user_id != 0){
		$blogUser = User::findOrFail($blog->user_id);
	}else{
		$blogUser = '';
	}
    $content = BlogContent::findOrFail($blog->blog_content_id);
@endphp

<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('blog') }}">Блог <span>—</span></a></li>
            <li>{{ mb_strimwidth($blog->name, 0, 15, "...") }}</li>
        </ul>
    </div>
</div>
<section class="mental_health">
    <div class="container">
        <div class="health_block">
            <div class="title_block">
                <h2>{{ $blog->name }}</h2>
            </div>
            <div class="block_whyu">
                <p>Опубликовано: {{ $blog->created_at }}</p>
                <p>Контент проверен специалистом</p>
                <div class="groupp_commm">
                    <h4><img src="/img/img_bl1.svg" alt="">{{ $blog->time_read }}</h4>
                    <h4><img src="/img/img_bl.svg" alt="">{{ $blog->views }}</h4>
					@if(auth()->check())


						@if(\App\Models\Like::where([
							['user_id', '=', auth()->user()->id],
							['post_id', '=', $blog->id],
						])->exists())
							<h4><a href='/addlike/{{$blog->id}}'><img src="/img/like.png" width='16px' alt=""></a>{{\App\Models\Like::where('post_id', $blog->id)->get()->count()}}</h4>
						@else
							<h4><a href='/addlike/{{$blog->id}}'><img src="/img/like_1.png" width='16px' alt=""></a>{{\App\Models\Like::where('post_id', $blog->id)->get()->count()}}</h4>
						@endif

						@if(\App\Models\Dislike::where([
							['user_id', '=', auth()->user()->id],
							['post_id', '=', $blog->id],
						])->exists())
							<h4><a href='/adddislike/{{$blog->id}}'><img src="/img/dislike_1.png" width='16px' alt=""></a>{{\App\Models\Dislike::where('post_id', $blog->id)->get()->count()}}</h4>
						@else
							<h4><a href='/adddislike/{{$blog->id}}'><img src="/img/dislike.png" width='16px' alt=""></a>{{\App\Models\Dislike::where('post_id', $blog->id)->get()->count()}}</h4>
						@endif

					@else
						<p>Авторизируйтесь для добавления лайка!</p>
					@endif
                </div>

            </div>
        </div>
    </div>
</section>
<section class="block_creator">
    <div class="container">
        <div class="group_creator">
            <img src="/avatar/1" width="50px" height="50px" alt="" class="creator">
            <div class="block_right">
                <div class="block_top">
                    <div class="block_name">
                        <span>Автор</span>
						@if($blog->user_id != 0)
                        <h4>{{ $blogUser->firstname }} {{ $blogUser->lastname }}</h4>
						@else
							<h4>Admin</h4>
						@endif
                    </div>
                    {{--<a href="#">Подробнее</a>--}}
                </div>
                {{--
                <div class="block_psychologist">
                    <p>Психолог</p>
                    <img src="/img/tochka.svg" alt="">
                    <p>38 лет</p>
                    <img src="/img/tochka.svg" alt="">
                    <p>магистр</p>
                    <img src="/img/tochka.svg" alt="">
                    <p>стаж 12 лет</p>
                </div>
                <h5>Помогу понять причину тревожного состояния, страхов, панических атак. Помогу наладить пищевое поведение. Помогу найти опору <br> в жизненных ситуациях. Через трудные вопросы будем вместе искать истину, которая прячется внутри вас, исследовать ваши тревоги…</h5>
                --}}
            </div>
        </div>
        <div class="group_creator mob">
            <div class="block_top">
			@if($blog->user_id != 0)
                <img src="/avatar/{{ $blog->user_id }}" width="50px" height="50px" alt="" class="creator">
			@else
				<img src="/avatar/1" width="50px" height="50px" alt="" class="creator">
			@endif
                <div class="block_name">
                    <span>Автор</span>
					@if($blog->user_id != 0)
                    <h4>{{ $blogUser->firstname }} {{ $blogUser->lastname }}</h4>
					@else
					<h4>Admin</h4>
					@endif
                </div>
            </div>
            {{--
            <div class="block_psychologist">
                <p>Психолог</p>
                <img src="img/tochka.svg" alt="">
                <p>38 лет</p>
                <img src="img/tochka.svg" alt="">
                <p>магистр</p>
                <img src="img/tochka.svg" alt="">
                <p>стаж 12 лет</p>
            </div>
            <h5>Помогу понять причину тревожного состояния, страхов, панических атак. Помогу наладить пищевое поведение. Помогу найти опору <br> в жизненных ситуациях. Через трудные вопросы будем вместе искать истину, которая прячется внутри вас, исследовать ваши тревоги…</h5>
            <a href="#">Подробнее</a>
            --}}
        </div>
    </div>
</section>
<section class="block_text">
    <div class="container">
        <div class="text_group">
            {!! $content->text !!}
        </div>
        <div class="block_connect1">
            <h2>Поделись статьей с друзьями! </h2>
            <script src="https://yastatic.net/share2/share.js"></script>
            <div class="ya-share2" data-curtain data-size="l" data-shape="round" data-services="vkontakte,telegram,whatsapp"></div>
        </div>
        <div class="form_block_comment">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="block_form">
                            <div class="title_form">
                                <h2>Комментарии </h2>
								<ul>
									@foreach(\App\Models\BlogComment::where('blog_id', $blog->id)->where('id_com', 0)->get() as $bbc)
									<li style='padding-top: 20px'>
										<table>
											<tr>

											<td style='padding: 6px' valign='top'>
												<img src="/avatar/{{ $bbc->user_id }}" width="50px" height="50px" alt="" class="creator">
											</td><td>
										<b>{{\App\Models\User::where('id', $bbc->user_id)->first()->firstname}} {{\App\Models\User::where('id', $bbc->user_id)->first()->lastname}}</b><br />
											{{$bbc->comment}}<br />
											<small><i>{{$bbc->created_at}}</i></small><br />
											<a onclick='answ({{$bbc->id}})'>Ответить</a>
											<div class='sh_{{$bbc->id}} sh_' style='display: none'>
													@if(auth()->check())
													<form action="/addcomment" method='post'>
														@csrf
														<input type='hidden' name='id_post' value='{{$blog->id}}'>
														<input type='hidden' name='blog_content_id' value='{{$blog->blog_content_id}}'>
														<input type='hidden' name='id_com' value='{{$bbc->id}}'>


														<div class="item_form">
															<textarea name="comment" id="" placeholder="Текст комментария"></textarea>
														</div>
														<div class="form_btn">
															<button>Отправить</button>
														</div>
													</form>
													@else
														<p>Комментарии могут оставлять только авторизированные пользователи!</p>
													@endif
											</div>
												<ul>
													@foreach(\App\Models\BlogComment::where('id_com', $bbc->id)->get() as $bbcc)
													<li style='padding-top: 20px'>
													<table>
														<tr>

														<td style='padding: 6px' valign='top'>
															<img src="/avatar/{{ $bbcc->user_id }}" width="50px" height="50px" alt="" class="creator">
														</td><td>
														<b>{{\App\Models\User::where('id', $bbcc->user_id)->first()->firstname}} {{\App\Models\User::where('id', $bbcc->user_id)->first()->lastname}}</b><br />
															{{$bbcc->comment}}<br />
															<small><i>{{$bbcc->created_at}}</i></small><br />
														</td>
														</tr>
														</table>
													</li>
													@endforeach

											</ul>
											</td>
											</tr>
										</table>
									</li>
									@endforeach

								</ul>
                            </div>
							@if(auth()->check())
                            <form action="/addcomment" method='post'>
								@csrf
								<input type='hidden' name='id_post' value='{{$blog->id}}'>
								<input type='hidden' name='blog_content_id' value='{{$blog->blog_content_id}}'>
								<input type='hidden' name='id_com' value='0'>


                                <div class="item_form">
                                    <textarea name="comment" id="" placeholder="Текст комментария"></textarea>
                                </div>
                                <div class="form_btn">
                                    <button>Отправить</button>
                                </div>
                            </form>
							@else
								<p>Комментарии могут оставлять только авторизированные пользователи!</p>
							@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <?php
          $merchant_login = "APPP";
          $password_1 = "pn3Y0a3X1W1sWJlJxKWZ";
          $invid = auth()->user()->id;
          $description = "Пополнение баланса";
          $default_sum = "10";
          $signature_value = md5("$merchant_login::$invid:$password_1");
		  $IsTest = 1;
          print "<html><center><script language=JavaScript ".
            "src='https://auth.robokassa.ru/Merchant/PaymentForm/FormFLS.js?".
            "MerchantLogin=$merchant_login&DefaultSum=$default_sum&InvoiceID=$invid".
            "&Description=$description&SignatureValue=$signature_value&IsTest=$IsTest'></script></center></html>";
        ?>
    </div>
</section>
<script>
function answ(id){
	$('.sh_').hide();
	$('.sh_'+id).show();
}
</script>
@endsection
