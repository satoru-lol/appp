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
                <li><a href="{{ route('ourMeetings') }}">Наши встречи <span>—</span></a></li>
                <li title="{{$blog->name}}">{{ mb_strimwidth($blog->name, 0, 50, "...") }}</li>
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
                        <h4><img src="/img/img_bl.svg" alt="">{{ $blog->views }}</h4>
                        @if(auth()->check())

                            @if(\App\Models\Like::where([
                                ['user_id', '=', auth()->user()->id],
                                ['post_id', '=', $blog->id],
                            ])->exists())
                                <h4><a href='/addlike/{{$blog->id}}'><img src="/img/like.png" width='16px'
                                                                          alt=""></a>{{\App\Models\Like::where('post_id', $blog->id)->get()->count()}}
                                </h4>
                            @else
                                <h4><a href='/addlike/{{$blog->id}}'><img src="/img/like_1.png" width='16px'
                                                                          alt=""></a>{{\App\Models\Like::where('post_id', $blog->id)->get()->count()}}
                                </h4>
                            @endif

                            @if(\App\Models\Dislike::where([
                                ['user_id', '=', auth()->user()->id],
                                ['post_id', '=', $blog->id],
                            ])->exists())
                                <h4><a href='/adddislike/{{$blog->id}}'><img src="/img/dislike_1.png" width='16px'
                                                                             alt=""></a>{{\App\Models\Dislike::where('post_id', $blog->id)->get()->count()}}
                                </h4>
                            @else
                                <h4><a href='/adddislike/{{$blog->id}}'><img src="/img/dislike.png" width='16px' alt=""></a>{{\App\Models\Dislike::where('post_id', $blog->id)->get()->count()}}
                                </h4>
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="creator_card">
                @if ($blog->image)
                    <div class="img_block" style="justify-content: center; display: flex; padding-top: 10px">
                        <img src="{{ asset('img/blog/'.$blog->image) }}" style="width: 70%; border-radius: 15px"
                             class="blog_image" alt="Blog Image">
                    </div>
                @endif
                <div class="info_block">
                    @if (!empty($blog) && is_null($blog->image))
                    <div class="avatar_block">
                        <img src="/img/avatar.png" class="avatar" alt="Avatar">
                    </div>
                    @endif
                    <div class="top_section">
                        <div class="details">
                            <div class="detail_item">
                                <p><strong>Формат:</strong> {{$format->format}}</p>
                            </div>
                            @if($blog->amount == "free")
                                <div class="detail_item">
                                    <p><strong>Потребуется оплата:</strong> Бесплатно </p>
                                </div>
                            @else
                                <div class="detail_item">
                                    {{--
                                                                    <p><strong>Потребуется оплата:</strong> {{$blog->amount != "0" ? $blog->amount." руб." : $product->name." подписка"}} </p>
                                    --}}
                                    <p><strong>Потребуется оплата:</strong> {{$blog->amount ?? 'Бесплатно'}} </p>

                                </div>
                            @endif
                            <div class="detail_item">
                                <p><strong>ФИО организатора:</strong> {{$blog->fio}}</p>
                            </div>
                        </div>
                        <div class="work_time">
                            <p>{{--<img src="/img/img_cl.svg" class="clock_icon" alt="Clock Icon">--}}
                                <b>Дата: </b> {{ $formattedDate }}</p>
                        </div>
                        <div class="work_time">
                            <p>{{--<img src="/img/img_cl.svg" class="clock_icon" alt="Clock Icon">--}}<b>Продолжителность: </b>
                                120 минут</p>
                        </div>
                        <div class="work_time">
                            @if(!empty($blog) && $blog->format_id == 1)
                                <p><b><a href="{{$blog->feedback}}">Ссылка на встречу</a></b></p>
                            @elseif(!empty($blog) && $blog->format_id == 2)
                                <p><b>Место встречи:</b> {{$blog->feedback}}</p>
                            @endif
                        </div>
                    </div>
                    @if(auth()->user())
                        @if(!empty(\App\Models\ParticipantActions::where("object_name", "meeting")->where("object_id", $blog->id)->where("user_id", auth()->user()->id)->first()->user_id))
                            <div class="action_buttons">
                                <button class="btn join_btn" style="background-color: red"
                                        onclick="window.location.href = '/takePart/delete/{{$blog->id}}/{{auth()->user()->id}}'">
                                    Отменить участие
                                </button>
                            </div>
                        @elseif(auth()->user()->id != $blog->user_id && \App\Models\Subscription::where('user_id',auth()->user()->id)->first()->level !== 0)
                            <div class="action_buttons">
                                <button class="btn join_btn" id="join_btn">Принять участие</button>
                            </div>
                        @endif
                    @else
                        <h3>Авторизируйтесь, чтобы принять участие</h3>
                    @endif

                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                $("#join_btn").on("click", function (e) {
                    $("#participantCheck").modal("show");
                })
                $("#closeModal").on("click", function (e) {
                    debugger;
                    $("#participantCheck").modal("hide");
                })

            })

        </script>
    </section>
    <div style="margin-top: 200px" class="modal fade" id="participantCheck" tabindex="-1" role="dialog"
         aria-labelledby="participantCheckLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participantCheckLabel">Подтверждение</h5>
                </div>
                <div class="modal-body">
                    <h3>Подтвердите участие</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal">Закрыть</button>
                    <button type="button" class="btn btn-primary"
                            onclick="window.location.href='/takePart/meeting/{{$blog->id}}'">Подтвердить
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Основные стили для контейнера */
        .block_creator {
            padding: 40px 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 1200px;
            width: 100%;
        }

        /* Стили для карточки */
        .creator_card {
            display: flex;
            flex-direction: column;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .creator_card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Стили для блока с изображением */
        .img_block {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .blog_image {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Стили для блока с информацией */
        .info_block {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .avatar_block {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid #ddd;
        }

        /* Информационный блок */
        .top_section {
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 15px;
        }

        .detail_item {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .detail_item p {
            margin: 0;
            color: #333;
        }

        .work_time {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
        }

        .clock_icon {
            width: 20px;
            margin-right: 10px;
        }

        /* Кнопки действия */
        .action_buttons {
            display: flex;
            justify-content: center;
        }

        .join_btn {
            background-color: #613482;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }

        .join_btn:hover {
            background-color: #613482;
            color: white;
            transform: scale(1.05);
        }

        /* Мобильная версия */
        @media (max-width: 768px) {
            .creator_card {
                flex-direction: column;
            }

            .avatar_block {
                margin-bottom: 15px;
            }
        }


    </style>
    <section class="block_text">
        <div class="container">
            <div class="text_group">
                {!! $content->text !!}
            </div>
            <div class="card border-0 shadow-sm rounded">
                <div style="background-color: #613482" class="card-header text-white rounded-top">
                    <h4 class="mb-0">Участники встречи</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Имя</th>
                                <th>Фамилия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\ParticipantActions::where("object_name", "meeting")->where("object_id", $blog->id)->get() as $participant)
                                @php $user = User::where("id", $participant->user_id)->first(); @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->firstname }}</td>
                                    <td>{{ $user->lastname }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <style>
                /* Кастомные стили для заголовка карточки */
                .custom-header {
                    background-color: #613482;
                    color: #ffffff;
                    border-radius: .375rem .375rem 0 0;
                }

                /* Кастомные стили для таблицы */
                .custom-table {
                    border-collapse: separate;
                    border-spacing: 0;
                }

                .table-hover tbody tr:hover {
                    background-color: #f0f2f5;
                }

                .table th,
                .table td {
                    vertical-align: middle;
                }

                .table thead th {
                    background-color: #f8f9fa;
                    color: #495057;
                }

                .table-bordered {
                    border: 1px solid #dee2e6;
                }

                .table-bordered thead th,
                .table-bordered tbody td {
                    border: 1px solid #dee2e6;
                }

                .table-striped tbody tr:nth-of-type(odd) {
                    background-color: #f9f9f9;
                }

                /* Кастомные стили для кнопок */
                .btn {
                    border-radius: .375rem;
                }

                .btn-primary {
                    background-color: #613482;
                    border-color: #613482;
                }

                .btn-primary:hover {
                    background-color: #4f2b70;
                    border-color: #4f2b70;
                }

            </style>

            @if(!empty($blog->video))
                <video width="640" height="360" controls>
                    <source src="https://appp-psy.ru/images/{{$blog->video}}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            @else
                {{--<h3>Видео отсутствует</h3>--}}
            @endif
            <hr>
            <div class="block_connect1">
                <h2>Поделись статьей с друзьями! </h2>
                <script src="https://yastatic.net/share2/share.js"></script>
                <div class="ya-share2" data-curtain data-size="l" data-shape="round"
                     data-services="vkontakte,telegram,whatsapp"></div>
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
                                                            <img src="/avatar/{{ $bbc->user_id }}" width="50px"
                                                                 height="50px" alt="" class="creator">
                                                        </td>
                                                        <td>
                                                            <b>{{\App\Models\User::where('id', $bbc->user_id)->first()->firstname}} {{\App\Models\User::where('id', $bbc->user_id)->first()->lastname}}</b><br/>
                                                            {{$bbc->comment}}<br/>
                                                            <small><i>{{$bbc->created_at}}</i></small><br/>
                                                            <a style="cursor: pointer" onclick='answ({{$bbc->id}})'>Ответить</a>
                                                            @if(auth()->check() && auth()->user()->group == "admin")
                                                                <a style="color: red; cursor: pointer"
                                                                   onclick='window.location.href = "/delEnt/{{$bbc->id}}/blog_comments"'>Удалить</a>
                                                            @endif
                                                            <div class='sh_{{$bbc->id}} sh_' style='display: none'>
                                                                @if(auth()->check())
                                                                    <form action="/addcomment" method='post'>
                                                                        @csrf
                                                                        <input type='hidden' name='id_post'
                                                                               value='{{$blog->id}}'>
                                                                        <input type='hidden' name='blog_content_id'
                                                                               value='{{$blog->blog_content_id}}'>
                                                                        <input type='hidden' name='id_com'
                                                                               value='{{$bbc->id}}'>


                                                                        <div class="item_form">
                                                                            <textarea name="comment" id=""
                                                                                      placeholder="Текст комментария"></textarea>
                                                                        </div>
                                                                        <div class="form_btn">
                                                                            <button>Отправить</button>
                                                                        </div>
                                                                    </form>
                                                                @else
                                                                    <p>Комментарии могут оставлять только
                                                                        авторизированные пользователи!</p>
                                                                @endif
                                                            </div>
                                                            <ul>
                                                                @foreach(\App\Models\BlogComment::where('id_com', $bbc->id)->get() as $bbcc)
                                                                    <li style='padding-top: 20px'>
                                                                        <table>
                                                                            <tr>

                                                                                <td style='padding: 6px' valign='top'>
                                                                                    <img src="/avatar/{{ $bbcc->user_id }}"
                                                                                         width="50px" height="50px"
                                                                                         alt="" class="creator">
                                                                                </td>
                                                                                <td>
                                                                                    <b>{{\App\Models\User::where('id', $bbcc->user_id)->first()->firstname}} {{\App\Models\User::where('id', $bbcc->user_id)->first()->lastname}}</b><br/>
                                                                                    {{$bbcc->comment}}<br/>
                                                                                    <small><i>{{$bbcc->created_at}}</i></small><br/>
                                                                                    @if(auth()->check() && auth()->user()->group == "admin")
                                                                                        <a style="color: red; cursor: pointer"
                                                                                           onclick='window.location.href = "/delEnt/{{$bbcc->id}}/blog_comments"'>Удалить</a>
                                                                                    @endif
                                                                                    <a style="cursor: pointer"
                                                                                       onclick='answ({{$bbcc->id}})'>Ответить</a>
                                                                                    <div class='sh_{{$bbcc->id}} sh_'
                                                                                         style='display: none'>
                                                                                        @if(auth()->check())
                                                                                            <form action="/addcomment"
                                                                                                  method='post'>
                                                                                                @csrf
                                                                                                <input type='hidden'
                                                                                                       name='id_post'
                                                                                                       value='{{$blog->id}}'>
                                                                                                <input type='hidden'
                                                                                                       name='blog_content_id'
                                                                                                       value='{{$blog->blog_content_id}}'>
                                                                                                <input type='hidden'
                                                                                                       name='id_com'
                                                                                                       value='{{$bbcc->id}}'>


                                                                                                <div class="item_form">
                                                                                                    <textarea
                                                                                                            name="comment"
                                                                                                            id=""
                                                                                                            placeholder="Текст комментария"></textarea>
                                                                                                </div>
                                                                                                <div class="form_btn">
                                                                                                    <button>Отправить
                                                                                                    </button>
                                                                                                </div>
                                                                                            </form>
                                                                                    </div>
                                                                                    <ul>
                                                                                        @foreach(\App\Models\BlogComment::where('id_com', $bbcc->id)->get() as $bbccc)
                                                                                            <li style='padding-top: 20px'>
                                                                                                <table>
                                                                                                    <tr>

                                                                                                        <td style='padding: 6px'
                                                                                                            valign='top'>
                                                                                                            <img src="/avatar/{{ $bbccc->user_id }}"
                                                                                                                 width="50px"
                                                                                                                 height="50px"
                                                                                                                 alt=""
                                                                                                                 class="creator">
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <b>{{\App\Models\User::where('id', $bbccc->user_id)->first()->firstname}} {{\App\Models\User::where('id', $bbccc->user_id)->first()->lastname}}</b><br/>
                                                                                                            {{$bbccc->comment}}
                                                                                                            <br/>
                                                                                                            <small><i>{{$bbcc->created_at}}</i></small><br/>
                                                                                                            @if(auth()->user()->group == "admin")
                                                                                                                <a style="color: red; cursor: pointer"
                                                                                                                   onclick='window.location.href = "/delEnt/{{$bbccc->id}}/blog_comments"'>Удалить</a>
                                                                                                            @endif
                                                                                                            <a style="cursor: pointer"
                                                                                                               onclick='answ({{$bbcc->id}})'>Ответить</a>
                                                                                                            <div class='sh_{{$bbccc->id}} sh_'
                                                                                                                 style='display: none'>
                                                                                                                @if(auth()->check())
                                                                                                                    <form action="/addcomment"
                                                                                                                          method='post'>
                                                                                                                        @csrf
                                                                                                                        <input type='hidden'
                                                                                                                               name='id_post'
                                                                                                                               value='{{$blog->id}}'>
                                                                                                                        <input type='hidden'
                                                                                                                               name='blog_content_id'
                                                                                                                               value='{{$blog->blog_content_id}}'>
                                                                                                                        <input type='hidden'
                                                                                                                               name='id_com'
                                                                                                                               value='{{$bbccc->id}}'>


                                                                                                                        <div class="item_form">
                                                                                                                            <textarea
                                                                                                                                    name="comment"
                                                                                                                                    id=""
                                                                                                                                    placeholder="Текст комментария"></textarea>
                                                                                                                        </div>
                                                                                                                        <div class="form_btn">
                                                                                                                            <button>
                                                                                                                                Отправить
                                                                                                                            </button>
                                                                                                                        </div>
                                                                                                                    </form>

                                                                                                            @else
                                                                                                            @endif
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </li>
                                                                                        @endforeach

                                                                                    </ul>
                                                                                    @else
                                                                                    @endif
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

        </div>
    </section>
    <script>
        function answ(id) {
            $('.sh_').hide();
            $('.sh_' + id).show();
        }

    </script>
@endsection
