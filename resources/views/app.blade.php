<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(!empty($keywords))
        <meta name="keywords" content="{{$keywords}}"/>
    @endif
    @if(!empty($description))
        <meta name="description" content="{{$description}}"/>
    @endif
    <title>{{ $title ?? 'АЧПП' }}</title>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Ассоциация частнопрактикующих психологов и психотерапевтов (АЧПП)' }}">
    <meta property="og:description" content="{{ $description ?? 'Пространство, объединяющее психологов и психотерапевтов для профессионального роста, обмена опытом и взаимной поддержки.' }}">
    <meta property="og:image" content="{{ asset('img/logo.svg') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'Ассоциация частнопрактикующих психологов и психотерапевтов (АЧПП)' }}">
    <meta property="twitter:description" content="{{ $description ?? 'Пространство, объединяющее психологов и психотерапевтов для профессионального роста, обмена опытом и взаимной поддержки.' }}">
    <meta property="twitter:image" content="{{ asset('img/logo.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Подключаем CSS для видеотеки v2 -->
    @if(request()->is('v2/video*') || request()->is('v2/videostream'))
    <link rel="stylesheet" href="{{ asset('v2/css/video.css') }}">
    @endif
    
    @yield('styles')
</head>
<body class="antialiased">
<div id="app">
    <div class="main-wrapper">
        @include('layouts.header-v2')
        <main class="main-content">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>
</div>




<div style="margin-top: 200px" class="modal fade" id="subscribeToClub" tabindex="-1" role="dialog" aria-labelledby="subscribeToClubLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribeToClubLabel">Предупреждение </h5>
            </div>
            <div class="modal-body">
                <form id="donationForm">
                    <div class="form-group">
                        <p>Чтобы вступить в клуб остался один шаг. Пожалуйста, авторизируйтесь.</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                <button type="button" onclick="window.location.href = 'https://appp-psy.ru/introduction'" class="btn btn-primary" id="registerButton">Регистрация</button>
            </div>
        </div>
    </div>
</div>





<!-- Модальное окно -->
<div style="margin-top: 200px" class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Подтверждение оплаты</h5>

            </div>
            <div class="modal-body">
                Вы уверены, что хотите оплатить сумму <span id="paymentAmount"></span> руб.?

                <br><br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="perms">
                    <label class="form-check-label" for="flexCheckChecked">
                        Я даю согласие на обработку персональных данных и принимаю условия <a target="_blank" href="https://docs.google.com/document/d/1OCAGMhzxFPxYg3qhvD95p76EnOMyGxCP/edit?usp=drivesdk&ouid=106143362002703295192&rtpof=true&sd=true">публичной оферты</a>
                    </label>
                </div>
                <br/>
                    <div class="form-check autofield">
                    <input class="form-check-input" type="checkbox" value="" id="perms2">
                    <label class="form-check-label" for="flexCheckChecked2">
                        Я даю согласие на автоматическое списание платы за подписку раз в месяц с баланса на сайте
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="confirmPayment">Подтвердить оплату</button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно -->
<!-- Модальное окно -->
<div style="margin-top: 200px" class="modal fade" id="authPaymentModal" tabindex="-1" role="dialog" aria-labelledby="authPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authPaymentModalLabel">Подтверждение оплаты</h5>
            </div>
            <div class="modal-body">
                <div id="content-block"></div>
                <br><br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="auth-perms">
                    <label class="form-check-label" for="auth-perms">
                         Я даю согласие на обработку персональных данных и принимаю условия <a target="_blank" href="https://docs.google.com/document/d/1OCAGMhzxFPxYg3qhvD95p76EnOMyGxCP/edit?usp=drivesdk&ouid=106143362002703295192&rtpof=true&sd=true">публичной оферты</a>
                    </label>
                </div>
                         <br/>
                  <div class="form-check autofield">
                    <input class="form-check-input" type="checkbox" id="auth-perms2">
                    <label class="form-check-label" for="auth-perms2">
                         Я даю согласие на автоматическое списание платы за подписку раз в месяц с баланса на сайте
                    </label>
                </div>
            </div>
            <input type="hidden" id="auth-perms2val" value="0" />
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="authConfirmPayment" disabled>Подтвердить оплату</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
<script>
    window.getUserAvatar = function(user) {
        // user: {id, phone, firstname, lastname, avatar}
        if (user.id && user.phone) {
            if (typeof md5 === 'function') {
                let hash = md5(user.id + user.phone);
                let url = '/img/avatars/' + hash + '.jpg';
                
                // Создаем временное изображение для проверки существования файла
                let img = new Image();
                img.onload = function() {
                    // Файл существует, ничего не делаем
                };
                img.onerror = function() {
                    // Файл не существует, заменяем на ui-avatars
                    let name = ((user.firstname || '') + ' ' + (user.lastname || '')).trim() || 'A';
                    let uiAvatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=random&color=fff&size=128';
                    
                    // Находим все img с этим src и заменяем
                    document.querySelectorAll('img[src="' + url + '"]').forEach(function(imgElement) {
                        imgElement.src = uiAvatarUrl;
                    });
                };
                img.src = url;
                
                return url;
            }
        }
        
        // Если нет id/phone или md5 не доступен, сразу возвращаем ui-avatars
        let name = ((user.firstname || '') + ' ' + (user.lastname || '')).trim() || 'A';
        return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=random&color=fff&size=128';
    }
</script>

<script src="{{asset('js/utils.js')}}"></script>
<script src="{{asset('js/main.js')}}"></script>

@stack('js')

<!-- Подключаем JS для видеотеки v2 -->
@if(request()->is('v2/video*') || request()->is('v2/videostream'))
<script src="{{ asset('v2/js/video.js') }}"></script>
@endif

</body>
</html>
