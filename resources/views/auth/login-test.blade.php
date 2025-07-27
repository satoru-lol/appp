@extends('app', [
    'title' => 'Авторизация',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')

    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="info-fill" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>

    <form action="{{ route('login-auth') }}" method="POST">
        @csrf

        <div class="login_in">
            <div class="container">
                @session('phone_confirm')


                <section class="third_levels" id="third_levels">
                    <div class="container">
                        <div class="levels">
                            @php
                                $subscription = \App\Models\Subscription::where("user_id", session("user_id"))->first();
                            @endphp
                            <script>
                                $(document).ready(function() {
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const subscriptionLevel = {{$subscription->level}};
                                        const codeInputBlock = document.getElementById('code-input-block');
                                        const subscriptionBlock = document.getElementById('subscription-block');

                                        if (subscriptionLevel == 0) {
                                            // Показываем блок подтверждения кода
                                            codeInputBlock.style.display = 'block';
                                            subscriptionBlock.style.display = 'none';
                                        } else if (subscriptionLevel > 0) {
                                            // Показываем только блок подтверждения SMS
                                            codeInputBlock.style.display = 'block';
                                            subscriptionBlock.style.display = 'none';
                                        }
                                    });


                                    $('#confirm-code-button').click(function(e) {
                                        e.preventDefault(); // Предотвращаем отправку формы

                                        var code = $('#send-code').val();
                                        $.ajax({
                                            url: '{{ route("confirmCode") }}', // Укажите правильный маршрут для подтверждения кода
                                            type: 'POST',
                                            data: {
                                                code: code,
                                                user_id: {{session("user_id")}},
                                                _token: '{{ csrf_token() }}' // Токен CSRF для безопасности
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    // Если код подтвержден успешно, возможно, нужно обновить информацию или перезагрузить страницу
                                                    // Например, скрыть блок с кодом и показать информацию о подписке
                                                    $('#code-input-block').hide();
                                                    $('#subscription-block').show();
                                                } else {
                                                    alert(response.message)
                                                    // Показываем сообщение об ошибке
                                                    $('#error-message').show();
                                                    $('#error-text').text(response.message);
                                                }
                                            },
                                            error: function(xhr) {
                                                // Обработка ошибок
                                                $('#error-message').show();
                                                $('#error-text').text('Произошла ошибка. Попробуйте снова.');
                                            }
                                        });
                                    });
                                });


                            </script>
                            @if(!empty($subscription->user_id) && $subscription->level == 0)
                                <!-- Сначала блок для подтверждения кода -->
                                <div id="code-input-block">
                                    <form action="{{ route('confirmCode') }}" method="POST">
                                        @csrf
                                        <div class="title">
                                            <h3>Введите код из SMS</h3>
                                            <p>Отправлен на номер <span>{{ session('phone') }}</span></p>
                                        </div>
                                        <div class="input_mask">
                                            <input type="tel" name="phone" value="{{ session('phone') }}" class="d-none">
                                            <input type="tel" name="phone_confirm" id="send-code" required>

                                            @error('phone_confirm')
                                            <div class="mt-3">
                                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                    <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                    <div>
                                                        {{ $message }}
                                                    </div>
                                                </div>
                                            </div>
                                            @enderror

                                            @session('phone_confirm')
                                            <div class="mt-3">
                                                <div class="alert alert-{{ session('class') ?? 'danger' }} {{ session('hidden') ? 'd-none' : 'd-flex' }} align-items-center" role="alert">
                                                    <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                    <div>
                                                        {{ $value }}
                                                    </div>
                                                </div>
                                            </div>
                                            @endsession
                                            <button id="confirm-code-button">Подтвердить</button>
                                            <p>Код действителен в течение 10 минут. Можно переслать через 30 секунд</p>
                                        </div>
                                    </form>
                                </div>

                                <!-- Блок с подпиской появится после подтверждения кода -->
                                <div id="subscription-block" style="display: none;">
                                    <div class="subscription-block">
                                        <div class="title">
                                            <h3>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM8 13c-2.5 0-4.5-2-4.5-5S5.5 3 8 3s4.5 2 4.5 5-2 5-4.5 5z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5z"/>
                                                </svg>
                                                Покупка подписки для доступа в личный кабинет
                                            </h3>
                                        </div>
                                        {{--<p>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                            </svg>
                                            Для завершения верификации вашего аккаунта остался всего 1 шаг: получите подписку "Наблюдатель" всего за 1 руб.
                                        </p>--}}
                                    </div>

                                    <section class="third_levels" id="third_levels">
                                        <div class="container">
                                            <div class="levels">
                                                <div class="title_block">
                                                    <h2>{{count(\App\Models\Product::where("visible", true)->get())}} уровня подписки<span></span></h2>
                                                </div>
                                                <div class="items">
                                                    <div class="row" style="display: flex; justify-content: space-evenly">
                                                        @foreach(\App\Models\Product::all() as $product)
                                                            @if($product->visible)
                                                                <div class="col-lg-6" style="margin-top: 15px">
                                                                    <div class="item">
                                                                        <h3>{{$product->name}}</h3>
                                                                        <p>Цена: {{$product->price}} руб.</p>
                                                                        <button class="btn btn-success auth-pay-button" type="button" data-user-id="{{session("user_id")}}" data-amount="{{$product->price}}" data-first-week-amount="{{$product->first_week_price}}" data-id="{{$product->id}}">Оплатить</button><br><br>
                                                                        <p>{{$product->description}}</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                </div>
                        </div>

                        @elseif(!empty($subscription->user_id) && $subscription->level > 0)
                            <!-- Только блок подтверждения кода -->
                            <div id="code-input-block">
                                <form action="{{ route('login') }}" method="POST">
                                    @csrf
                                    <div class="title">
                                        <h3>Введите код из SMS</h3>
                                        <p>Отправлен на номер <span>{{ session('phone') }}</span></p>
                                    </div>
                                    <div class="input_mask">
                                        <input type="tel" name="phone" value="{{ session('phone') }}" class="d-none">
                                        <input type="tel" name="phone_confirm" id="send-code" required>

                                        @error('phone_confirm')
                                        <div class="mt-3">
                                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                <div>
                                                    {{ $message }}
                                                </div>
                                            </div>
                                        </div>
                                        @enderror

                                        @session('phone_confirm')
                                        <div class="mt-3">
                                            <div class="alert alert-{{ session('class') ?? 'danger' }} {{ session('hidden') ? 'd-none' : 'd-flex' }} align-items-center" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                                                <div>
                                                    {{ $value }}
                                                </div>
                                            </div>
                                        </div>
                                        @endsession
                                        <button type="submit">Подтвердить</button>
                                        <p>Код действителен в течение 10 минут. Можно переслать через 30 секунд</p>
                                    </div>
                                </form>
                            </div>

                        @else
                            <!-- Если нет подписки, выводим информацию о подписке -->
                            <div id="subscription-info-block">
                                <div class="subscription-info">
                                    <!-- Информация о подписке -->
                                    <p>Пожалуйста, оформите подписку, чтобы получить доступ к контенту.</p>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            </section>

            @else

                <div class="title">
                    <h3 style="font-size: 25px">Вход для зарегистрированных специалистов</h3>
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <p>Введите Эл. почту и пароль аккаунта</p>
                </div>
                <div class="input_mask">
                    {{--<input type="tel" id="mobile" name="phone" value="{{ old('phone') }}">--}}
                    <div class="form-group">

                        <label class="form-label" for="mail">Введите Эл. Почту</label>
                        <input type="email" id="mail" class="form-control" name="mail" placeholder="Введите Эл. Почту"><br>
                        <label class="form-label" for="password">Введите пароль</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Введите пароль">
                        <p style="font-size: 10px">Используйте отправленный на почту пароль или тот который вы создали</p>

                    </div>
                    @session('email_error')
                    <div class="mt-3">
                        <div class="alert alert-{{ session('class') ?? 'danger' }} {{ session('hidden') ? 'd-none' : 'd-flex' }} align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            <div>
                                {{ $value }}
                            </div>
                        </div>
                    </div>
                    @endsession
                    @session('phone_error')
                    <div class="mt-3">
                        <div class="alert alert-{{ session('class') ?? 'danger' }} {{ session('hidden') ? 'd-none' : 'd-flex' }} align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            <div>
                                {{ $value }} <a href="https://appp-psy.ru/introduction">Зарегистрируйтесь</a>
                            </div>
                        </div>
                    </div>
                    @endsession
                    @error('phone')
                    <div class="mt-3">
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Danger:" width="15px" height="15px"><use xlink:href="#exclamation-triangle-fill"/></svg>
                            <div>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                    @enderror
                    <p>Забыли пароль? <a href="/password/reset">Восстановить</a></p>

                    <button type="submit">Войти</button>


                    <p>Нажимая кнопку «Войти» вы даете согласие <a href="#">с политикой конфиденциальности</a></p>
                </div>
                @endsession
        </div>
        </div>
        </section>
    </form>


@endsection
