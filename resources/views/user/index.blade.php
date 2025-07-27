@extends('app', [
'title' => 'Профиль пользователя',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])
@php
    use Carbon\Carbon;

    $now = Carbon::now()->startOfDay(); 
    $expiredAt = $subscription->expired_at ? Carbon::parse($subscription->expired_at)->startOfDay() : null;
    $daysLeft = $expiredAt ? $now->diffInDays($expiredAt, false) : null;
@endphp

@section('content')
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
                <li>Профиль пользователя</li>
            </ul>
        </div>
    </div>
    
    <div class="container-xl px-4 mt-4">
        <div class="row gap-3 gap-lg-0">
            <div class="col-xl-4 d-flex justify-content-center" style="margin-top: 80px">
                {{--<form id="upload_image" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" onchange="this.form.submit()" class="d-none">

                    <div class="d-flex flex-column align-items-center border-0 shadow btn btn-outline-primary p-1">
                        <img src="avatar/{{ $user->id }}" class="card-img-top rounded" alt="Avatar"
                             style="max-height: 265px; object-fit: contain; width: auto;" onclick="document.getElementById('image').click()">
                        <div class="btn p-0 btn-primary text-white mt-1"
                             onclick="document.getElementById('image').click()">
                            Загрузить
                        </div>
                    </div>
                    <style>
                        .d-flex.btn:hover{
                            background-color: #fff;
                        }
                    </style>

                    <!-- Кнопка для удаления фото -->
                    <div class="d-flex justify-content-center mt-2">
                        <button type="button" onclick="removeAvatar(event)" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
                            Удалить фото
                        </button>

                    </div>
                    <div class="d-flex flex-column mt-5">
                        <p style="color: grey">
                            Сюда вы можете загрузить личное фото.
                            На изображении не должно быть надписей. Допускаются файлы формата JPG или PNG не более 2 мб.
                            Если у вас возникли проблемы с загрузкой фотографии, воспользуйтесь советами из справки по сайту.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    @if ($error == 'Не удалось загрузить image.')
                                        <li>Не удалось загрузить изображение. Максимальный размер 2MB.</li>
                                    @else
                                        <li>{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>--}}
                

                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" class="d-none">

                    <div class="d-flex flex-column align-items-center border-0 shadow btn btn-outline-primary p-1">
                        <img src="avatar/{{ $user->id }}" class="card-img-top rounded" alt="Avatar"
                             style="max-height: 265px; object-fit: contain; width: auto;">
                        <div class="btn p-0 btn-primary text-white mt-1"
                             onclick="document.getElementById('image').click()">
                            Загрузить
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-2">
                        <button type="button" onclick="removeAvatar(event)" class="btn btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-x-lg" viewBox="0 0 16 16">
                                <path
                                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                            </svg>
                            Удалить фото
                        </button>

                    </div>

                    <div class="d-flex flex-column mt-5">
                        <p style="color: grey">
                            Сюда вы можете загрузить личное фото.
                            На изображении не должно быть надписей. Допускаются файлы формата JPG или PNG не более 2 мб.
                            Если у вас возникли проблемы с загрузкой фотографии, воспользуйтесь советами из справки по
                            сайту.
                        </p>
                    </div>
                    <!-- Ошибка -->
                    <div id="file-error" class="alert alert-danger d-none">
                        ❌ Не удалось загрузить изображение. Максимальный размер 2MB.
                    </div>
                </form>
                <style>
                    .d-flex.btn:hover {
                        background-color: #fff;
                        cursor: default;
                    }
                </style>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    document.getElementById('image').addEventListener('change', function () {
                        const file = this.files[0];
                        const maxSize = 2 * 1024 * 1024; // 2MB
                        const errorDiv = document.getElementById('file-error');

                        if (file && file.size > maxSize) {
                            errorDiv.classList.remove('d-none'); // Показываем ошибку
                            // successDiv.classList.add('d-none');// Прячем успешное сообщение
                            return;
                        }

                        // Формируем данные для отправки
                        let formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('_method', 'PUT');
                        formData.append('image', file);

                        // Отправляем AJAX-запрос
                        $.ajax({
                            url: "{{ route('profileUpload') }}",
                            type: "POST",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                console.log(response);
                                console.log('here')
                                window.location.reload();
                            },
                            error: function (xhr) {
                                errorDiv.classList.remove('d-none'); // Показываем ошибку
                                errorDiv.innerHTML = "❌ Ошибка загрузки: " + xhr.responseJSON.message;
                            }
                        });
                    });
                </script>
            </div>
            <div class="col-xl-8">
                <div class="card mb-4 shadow border-0">
                    <div class="card-header bg-white">Детали аккаунта</div>
                           @if($errors->has('balance'))
                         <div class="alert alert-danger m-2">
                             {{ $errors->first('balance') }}
                      </div>
                    @endif
                    <div class="card-body">
                        <form action="{{ route('profileSave') }}" method="POST">
                            @csrf

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="phone">Номер телефона</label>
                                    <input class="form-control" id="phone" name="phone" type="tel"
                                           value="{{ $user->phone }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="email">Почта</label>
                                    <input class="form-control" id="email" type="email" value="{{ $user->email }}"
                                           disabled>
                                </div>
                            </div>

                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputFirstName">Имя</label>
                                    <input class="form-control @error('firstname') is-invalid @enderror"
                                           name="firstname" id="inputFirstName" type="text"
                                           placeholder="Введите ваше имя" value="{{ $user->firstname ?? '' }}">
                                    @error('firstname')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputLastName">Фамилия</label>
                                    <input class="form-control @error('lastname') is-invalid @enderror" name="lastname"
                                           id="inputLastName" type="text" placeholder="Введите вашу фамилию"
                                           value="{{ $user->lastname ?? '' }}">
                                    @error('lastname')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>


                            @session('success')
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-check2" viewBox="0 0 16 16">
                                        <path
                                            d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0"/>
                                    </svg>

                                    {{ $value }}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                            </div>
                            @endsession

                            <button class="btn btn-success" style="background-color: #613482" type="submit">Сохранить
                            </button>
                        </form>
                        <br>
                        <a href="/password/reset/{{csrf_token()}}/{{$user->email}}" class="new_pass">Создать новый
                            пароль</a>
                        <br>
                        <br>
                
                    </div>

                    <style>
                        /* Основной стиль для кнопки сброса пароля */
                        .new_pass {
                            display: inline-block;
                            font-weight: 500;
                            color: #ffffff;
                            text-align: center;
                            vertical-align: middle;
                            user-select: none;
                            background-color: #007bff;
                            border: 1px solid #007bff;
                            border-radius: 0.5rem;
                            padding: 0.2rem 0.6rem;
                            font-size: 1rem;
                            transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
                            text-decoration: none;
                        }

                        .new_pass:hover {
                            background-color: #0056b3;
                            border-color: #004085;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        }

                        .new_pass:focus, .new_pass:active {
                            background-color: #004085;
                            border-color: #003366;
                            outline: none;
                            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
                        }

                        /* Основной контейнер подписки */
                        #subscription-container {
                            position: relative;
                            margin: 20px 0;
                        }

                        /* Стили для метки */
                        .custom-label {
                            display: flex;
                            align-items: center;
                            font-size: 1rem;
                            font-weight: 600;
                            color: #333;
                        }

                        /* Иконка в метке */
                        .custom-label svg {
                            margin-left: 10px;
                            fill: #007bff;
                        }

                        /* Поле ввода подписки */
                        .form-control.custom-subscription {
                            cursor: pointer;
                            background-color: #f8f9fa;
                            border: 1px solid #ced4da;
                            border-radius: 4px;
                            padding: 10px;
                            font-size: 1rem;
                            transition: all 0.3s ease;
                        }

                        .form-control.custom-subscription:hover {
                            border-color: #007bff;
                        }

                        /* Контейнер для выбора подписки */
                        .subscription-select-container {
                            display: none; /* Скрыт по умолчанию */
                            margin-top: 10px;
                        }

                        /* Опция подписки */
                        .subscription-option {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            padding: 10px;
                            border: 1px solid #e9ecef;
                            border-radius: 4px;
                            background-color: #ffffff;
                            margin-bottom: 10px;
                            transition: background-color 0.3s ease;
                            cursor: pointer;
                        }

                        .subscription-option.current-subscription {
                            background-color: #e7f1ff;
                            border-color: #007bff;
                        }

                        /* Кнопки */
                        .btn {
                            display: flex;
                            align-items: center;
                            padding: 5px 10px;
                            border-radius: 4px;
                            border: none;
                            cursor: pointer;
                            transition: background-color 0.3s ease;
                        }

                        .btn-sm {
                            font-size: 0.875rem;
                        }

                        /*   .btn-danger {
                               background-color: #dc3545;
                               color: white;
                           }

                           .btn-danger:hover {
                               background-color: #c82333;
                           }
       */
                        .btn-primary {
                            background-color: #007bff;
                            color: white;
                        }

                        .btn-primary:hover {
                            background-color: #0056b3;
                        }

                        /* Иконки в кнопках */
                        .btn svg {
                            margin-right: 5px;
                            fill: currentColor;
                        }

                        /* Основной контейнер для баланса */
                        #balance-container {
                            margin: 20px 0;
                        }

                        /* Стили для метки баланса */
                        .custom-label {
                            display: flex;
                            align-items: center;
                            font-size: 1rem;
                            font-weight: 600;
                            color: #333;
                            margin-bottom: 5px;
                        }

                        /* Иконка в метке */
                        .custom-label svg {
                            margin-left: 10px;
                            fill: #007bff;
                        }

                        /* Поле ввода для баланса */
                        .form-control.custom-balance {
                            background-color: #f8f9fa;
                            border: 1px solid #ced4da;
                            border-radius: 8px;
                            padding: 10px;
                            font-size: 1rem;
                            font-weight: 500;
                            color: #333;
                            text-align: center;
                            cursor: not-allowed;
                            transition: all 0.3s ease;
                        }

                        .form-control.custom-balance:disabled {
                            background-color: #e9ecef;
                            border-color: #adb5bd;
                            cursor: not-allowed;
                        }

                        /* Добавление современных стилей и эффекта на hover для поля ввода */
                        .form-control.custom-balance:hover:not(:disabled) {
                            border-color: #007bff;
                            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
                        }
                        .custom-payment-form iframe{
                            min-height:150px !important;
                        }
                    </style>

                    <div class="card-body custom-card-body">
                    <div class="row gx-3 mb-2 custom-payment-form iframe-container">
                                <div class="col-md-4">
                                        <?php

                                        $merchant_login = "APPP";
                                        $password_1 = "LFuWhwWF2H63Uaf3Wwz1";
                                        $description = auth()->user()->id;
                                        $default_sum = "1";
                                        $Shp_user = auth()->user()->id;
                                        $signature_value = md5("$merchant_login::$invID:$password_1:Shp_user=$Shp_user");
                                        $IsTest = 0;
                                        print "<html><script language=JavaScript " .
                                            "src='https://auth.robokassa.ru/Merchant/PaymentForm/FormFLS.js?" .
                                            "MerchantLogin=$merchant_login&DefaultSum=$default_sum&InvoiceID=$invID" .
                                            "&Description=$description&SignatureValue=$signature_value&IsTest=$IsTest&Shp_user=$Shp_user'></script></html>";
                                        ?>
                                </div>
                                <div class="modal fade" id="exampleModal" tabindex="-1"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">QR</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body d-flex justify-content-center align-items-center">
                                                    <?php

                                                    $merchant_login = "APPP";
                                                    $password_1 = "LFuWhwWF2H63Uaf3Wwz1";
                                                    $description = auth()->user()->id;
                                                    $default_sum = "1";
                                                    $Shp_user = auth()->user()->id;
                                                    $signature_value = md5("$merchant_login:$default_sum:$invID:$password_1:Shp_user=$Shp_user");
                                                    $IsTest = 0;
                                                    $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$merchant_login&OutSum=$default_sum&InvoiceID=$invID&SignatureValue=$signature_value&Shp_user=$Shp_user";

                                                    $png = SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url);

                                                    ?>

                                                <a href="{{$url}}" target="_blank">{{$png}}</a>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Закрыть
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-4 ml-4 marginResize">
                                    <button type="button" style="padding: 15px;" class="btn btn-primary"
                                            data-bs-toggle="modal" data-bs-target="#exampleModal">
                                        Оплата через QR
                                    </button>
                                </div>

                                {{--
                            <form method = "POST"  action = "https://auth.robokassa.ru/Merchant/Index.aspx">
                                <input type = "hidden" name = "MerchantLogin" value = "APPP">
                                <input type = "hidden" name = "InvoiceID" value = "115">
                                <input type = "hidden" name = "Description" value = "Оплата подписки">
                                <input type = "hidden" name = "SignatureValue" value = "{{md5("APPP:100:115:LFuWhwWF2H63Uaf3Wwz1")}}">
                                <input type = "hidden" name = "OutSum" value = "100">
                                <input type = "hidden" name = "Recurring" value = "true">
                                <input type = "submit" value = "Оплатить">
                            </form>--}}

                                @session('pay_error')
                                <div class="alert alert-danger alert-dismissible custom-alert" role="alert">
                                    <div>{{ $value }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                </div>
                                @endsession

                                @session('pay_success')
                                <div class="alert alert-success alert-dismissible custom-alert" role="alert">
                                    <div>{{ $value }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                </div>
                                @endsession
                            </div>
                        <div class="row gx-3 mb-3">
                            
                            <div class="col-md-6">
                                <label class="custom-label" for="balance">
                                    Ваш баланс
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-cash-coin" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/>
                                        <path
                                            d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/>
                                        <path
                                            d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/>
                                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>
                                    </svg>
                                </label>
                                <input class="form-control custom-balance" id="balance" type="text"
                                       value="{{ $user->balance == NULL ? 0 : $user->balance }} руб." readonly disabled>
                                       
                                
                                    
                            </div>
                            <!-- Современное предупреждение -->

                            <br>
                            <div class="col-md-6" id="subscription-container" style="margin-top: 0px">
                                <label class="custom-label" for="subscription">
                                    Уровень подписки
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-postcard" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                              d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm7.5.5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0zM2 5.5a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5M10.5 5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zM13 8h-2V6h2z"/>
                                    </svg>
                                </label>

                                <input style="cursor: pointer;" class="form-control custom-subscription"
                                       id="subscription" type="text" value="{{ $subscriptionTxt ?? 'Нет подписки' }}" readonly>

                                <div id="subscriptionSelectContainer" class="subscription-select-container">
                                    @foreach($products as $product)
                                    @if($product->level != 0 && $product->level != 7) {{-- Скрываем переходную подписку (level 7) --}}
                                        <div
                                            class="subscription-option {{ $product->current_subscription ? 'current-subscription' : '' }}"
                                            data-product="{{$product->slug}}" data-level="{{$product->level}}">
                                          <div>
                                                <div>{{ $product->name }} 
                                                    @if($product->level != 0)
                                                <b
                                                    style="font-size: 9px">{{ !empty($product->price) ? $product->price." Руб." : "" }}
                                                </b>
                                                @endif
                                            </div>
                                                    @if(isset($product->description))
                                                   <div style="font-size:11px">{{$product->description}}</div>
                                                   @endif
                                          </div>
                                            @if($product->current_subscription == true)
                                                @if ($product->level !== -1 && $product->level != 0)
                                                    <button class="btn btn-danger btn-sm buy-btn"
                                                            data-product="{{$product->slug}}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                             fill="currentColor" class="bi bi-x-circle"
                                                             viewBox="0 0 16 16">
                                                            <path
                                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                            <path
                                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                        </svg>
                                                        Отменить
                                                    </button>
                                                @endif
                                            @else
                                                @if ($product->visible == true && $product->level == 1)
                                                    <button class="btn btn-success auth-pay-button" type="button"
                                                            data-user-id="{{auth()->user()->id}}"
                                                            data-amount="{{$product->price}}"
                                                            data-level="{{$product->level}}"
                                                            data-first-week-amount="{{$product->first_week_price}}"
                                                            data-id="{{$product->id}}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                             fill="currentColor" class="bi bi-credit-card"
                                                             viewBox="0 0 16 16">
                                                            <path
                                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                                            <path
                                                                d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                                        </svg>
                                                        Подключить
                                                    </button>
                                                @else
                                                    <button class="btn btn-success auth-pay-button disabled" type="button" disabled>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                             fill="currentColor" class="bi bi-credit-card"
                                                             viewBox="0 0 16 16">
                                                            <path
                                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z"/>
                                                            <path
                                                                d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                                                        </svg>
                                                        Подключить
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                        @endif
                                    @endforeach
                                    
                        
                                </div>

                                <style>
                                    .custom-subscription {
                                        background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="blue" class="bi bi-chevron-down" viewBox="0 0 16 16"%3E%3Cpath fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"%3E%3C/path%3E%3C/svg%3E') no-repeat right 10px center;
                                        padding-right: 25px; /* Отступ для текста */
                                        cursor: pointer;
                                    }

                                    .expire-text {
                                        color: #555555; /* Нейтральный серый цвет */
                                        font-weight: bold; /* Жирный шрифт */
                                        font-size: 14px; /* Размер шрифта */
                                        padding-left: 5px; /* Отступ слева */
                                        font-style: italic; /* Курсив */
                                    }

                                    .expire-text .fas.fa-clock {
                                        margin-right: 5px; /* Отступ справа от иконки до текста */
                                        color: #888888; /* Цвет иконки */
                                    }
                                    
                                    /* Стиль для неактивных кнопок */
                                    .btn.disabled, .btn:disabled {
                                        opacity: 0.5;
                                        cursor: not-allowed;
                                    }
                                </style>
      @if ($daysLeft >= 0 && $daysLeft != null && $subscription->level != 1 && $subscription->level != -1) {{-- Исключаем счетчик для пробной подписки --}}
                                                <div class="alert alert-success mt-2">
                                                    Осталось <strong>{{ $daysLeft }}</strong> дней 
                                                     {{ $subscription->auto ? 'до следующего списания' : 'до конца подписки' }} 
                                                </div>
                                    
                                            @endif
                                @if(!empty($expireTxt))
                                    <span class="expire-text">
            <i class="fas fa-clock"></i> <!-- Иконка часов -->
            ({{ $expireTxt }})
        </span>
                                @endif

{{--                                <div class="form-check mt-3" {{$subscription->level == "0" ? "hidden" : ""}}>--}}
{{--                                    <input class="form-check-input" name="cancelSubscriptionCheck"--}}
{{--                                           @if(!empty(auth()->user()) && auth()->user()->auto == true) checked="checked"--}}
{{--                                           @else @endif type="{{$subscription->level == "0" ? "hidden" : "checkbox"}}"--}}
{{--                                           id="cancelSubscriptionCheck">--}}
{{--                                    <label class="form-check-label" for="cancelSubscriptionCheck">--}}
{{--                                        Автоматическое списание денег--}}
{{--                                    </label>--}}
{{--                                </div>--}}
                            </div>

                            <!--@if ($subscription->level == "0")-->
                            <!--    <div>-->
                                    <!-- Современное предупреждение -->
                            <!--        <div class="alert alert-warning d-flex align-items-center mt-3 p-3" role="alert"-->
                            <!--             style="font-size: 13px;background-color: #fff4e5; color: #856404; border: 1px solid #ffeeba; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">-->
                            <!--            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"-->
                            <!--                 fill="currentColor" class="bi bi-exclamation-triangle me-3"-->
                            <!--                 viewBox="0 0 16 16" style="flex-shrink: 0; color: #856404;">-->
                            <!--                <path-->
                            <!--                    d="M8.93.629a.5.5 0 0 1 .14.178l6.5 11a.5.5 0 0 1-.14.629l-6.5 4a.5.5 0 0 1-.86-.5L8.2 11H4.8l-.93 1.68a.5.5 0 0 1-.86.5l-6.5-4a.5.5 0 0 1-.14-.629l6.5-11a.5.5 0 0 1 .86-.5L7.8 5.92l.93-1.68a.5.5 0 0 1 .86-.5zM8 4.5a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 1 0v-2a.5.5 0 0 0-.5-.5zm0 4a.5.5 0 0 0-.5.5v.5a.5.5 0 0 0 1 0v-.5a.5.5 0 0 0-.5-.5z"/>-->
                            <!--            </svg>-->
                            <!--            <div>-->
                            <!--                <strong>Важно:</strong> при покупке подписки с вашей карты будут-->
                            <!--                автоматически списываться ежемесячные платежи. Пожалуйста, ознакомьтесь с-->
                            <!--                <b><a style="text-decoration: dashed"-->
                            <!--                      href="https://appp-psy.ru/#third_levels">условиями подписки</a></b>-->
                            <!--                перед подтверждением.-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--@endif-->


                            <!-- Модальное окно для подтверждения отмены подписки -->
                            <div class="modal fade cancellationModal" id="cancellationModal" tabindex="-1"
                                 aria-labelledby="cancellationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="cancellationModalLabel">Подтверждение отмены
                                                подписки</h5>
                                        </div>
                                        <div class="modal-body">
                                        <div>    Вы уверены, что хотите отменить подписку? Вы потеряете доступ ко всем
                                            специальным курсам, мероприятиям, клубам и другому контенту. <br/>
                                            <span class="text-danger">При отмене деньги за подписку не возвращаются</span>
                                        </div>
                                           @if($subscription->level == 6 && auth()->user()->balance >= $final_pr)
                                     
                                            <form method="POST" action="/changeToHigher" id="upgradeForm">
                                                @csrf
                                                   <div class="mt-4 d-flex p-2" style="background: rgb(184 218 202)">
                                                       <div>  Вы можете перейти на подписку за {{$prem_pr}} руб, заплатив {{ $final_pr}} руб</div>
                                                      <button type="submit" class="btn btn-success upgrade-btn" >
                                                        Перейти
                                                       </button>
                                                        </div>
                                            </form>
                                            
                                       
                                        @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary cancel-subs">Назад</button>
                                            <button type="button" class="btn btn-primary" id="confirmCancellationBtn">
                                                Подтвердить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Модальное окно для подтверждения покупки -->
                            <div class="modal fade" id="confirmationModal" tabindex="-1"
                                 aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Подтверждение
                                                покупки</h5>
                                        </div>
                                        <div class="modal-body">
                                            Вы уверены, что хотите купить подписку <span id="productName"></span>?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary cancel-subs"
                                                    data-bs-dismiss="modal">Отмена
                                            </button>


                                                <?php
                                                $invoiceId = auth()->user()->id;
                                                ?>{{--
                                        <form method = "POST"  action = "https://auth.robokassa.ru/Merchant/Index.aspx">
                                            <input type = "hidden" name = "MerchantLogin" value = "APPP">
                                            <input type = "hidden" name = "InvoiceID" value = "{{$invoiceId}}">
                                            <input type = "hidden" name = "Description" value = "Оплата подписки">
                                            <input type = "hidden" name = "SignatureValue" value = "{{md5("APPP:100:$invoiceId:LFuWhwWF2H63Uaf3Wwz1")}}">
                                            <input type = "hidden" name = "OutSum" value = "100">
                                            <input type = "hidden" name = "Recurring" value = "true">
                                        </form>--}}


                                            <form method="post" action="/pay" id="payForm">
                                                @csrf
                                                <input type="hidden" name="product_id" id="productId" value="">
                                                <button type="submit" class="btn btn-primary" id="">Купить</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Модальное окно для понижения уровня подписки -->
                            <div class="modal fade" id="downgradeModal" tabindex="-1"
                                 aria-labelledby="downgradeModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="downgradeModalLabel">Подтверждение понижения
                                                уровня</h5>
                                        </div>
                                        <div class="modal-body">
                                            Вы уверены, что хотите понизить уровень подписки? Вы потеряете доступ к
                                            части специальных курсов, мероприятий, клубов и другого контента.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary cancel-subs"
                                                    data-bs-dismiss="modal">Отмена
                                            </button>
                                            <button type="button" class="btn btn-primary" id="confirmDowngradeBtn">
                                                Подтвердить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Модальное окно для ввода кода подтверждения -->
                            <div class="modal fade" id="verificationModal" tabindex="-1"
                                 aria-labelledby="verificationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="verificationModalLabel">Введите код
                                                подтверждения</h5>
                                        </div>
                                        <div class="modal-body">
                                            Пожалуйста, введите код подтверждения, отправленный на ваш телефон.
                                            <input type="text" class="form-control mt-2" id="verificationCode"
                                                   placeholder="Код подтверждения">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary cancel-subs"
                                                    data-bs-dismiss="modal">Отмена
                                            </button>
                                            <button type="button" class="btn btn-primary" id="confirmVerificationBtn">
                                                Подтвердить
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <style>
                                .subscription-select-container {
                                    border: 1px solid #ced4da;
                                    border-radius: 4px;
                                    background-color: #ffffff;
                                    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.125);
                                    padding: 10px;
                                    max-height: 200px;
                                    overflow-y: auto;
                                }

                                .subscription-option {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    padding: 5px;
                                    border-bottom: 1px solid #e9ecef;
                                }

                                .subscription-option:last-child {
                                    border-bottom: none;
                                }

                                .subscription-option span {
                                    font-size: 14px;
                                }

                                .subscription-option .btn {
                                    margin-left: 10px;
                                }

                                /* Анимация для эффекта нажатия */
                                .subscription-option {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    padding: 5px;
                                    border-bottom: 1px solid #e9ecef;
                                    transition: transform 0.2s ease, background-color 0.2s ease;
                                }

                                .subscription-option:active {
                                    transform: scale(0.98);
                                    background-color: #f0f0f0;
                                }

                                /* Анимация появления селектора */
                                .subscription-select-container {
                                    border: 1px solid #ced4da;
                                    border-radius: 4px;
                                    background-color: #ffffff;
                                    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.125);
                                    padding: 10px;
                                    max-height: 200px;
                                    overflow-y: auto;
                                    opacity: 0;
                                    transform: scale(0.9);
                                    transition: opacity 0.3s ease, transform 0.3s ease;
                                }

                                .subscription-select-container.show {
                                    opacity: 1;
                                    transform: scale(1);
                                }

                                /* Анимация скрытия селектора */
                                .subscription-select-container.hide {
                                    opacity: 0;
                                    transform: scale(0.9);
                                }

                                /* Стиль для выделения текущей подписки */
                                .subscription-option.current-subscription {
                                    background-color: #d1ecf1; /* Цвет фона для выделения */
                                    border-color: #bee5eb; /* Цвет границы для выделения */
                                    color: #0c5460; /* Цвет текста для выделения */
                                }
                            </style>
                            <style>
                                /* Основной стиль для overlay */
                                .overlay {
                                    position: fixed;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    background: rgba(0, 0, 0, 0.5); /* Полупрозрачный черный фон */
                                    display: none; /* Скрыть по умолчанию */
                                    justify-content: center;
                                    align-items: center;
                                    z-index: 9999; /* Выше всего */
                                }

                                .overlay .spinner {
                                    border: 8px solid #f3f3f3; /* Серый фон */
                                    border-top: 8px solid #3498db; /* Синий цвет */
                                    border-radius: 50%;
                                    width: 50px;
                                    height: 50px;
                                    animation: spin 1s linear infinite;
                                }

                                @keyframes spin {
                                    0% {
                                        transform: rotate(0deg);
                                    }
                                    100% {
                                        transform: rotate(360deg);
                                    }
                                }
                            </style>
                            <div id="loadingOverlay" class="overlay">
                                <div class="spinner"></div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    // Проверка, если ширина экрана 768 пикселей или меньше
                                    function adjustIframeWidth() {
                                        var iframe = document.querySelector('.iframe-container iframe');
                                        if (iframe) {
                                            if (window.matchMedia("(max-width: 768px)").matches) {
                                                iframe.style.width = '450px'; // или 300px, если нужно фиксированное значение
                                            } else {
                                                iframe.style.width = '650px';
                                            }
                                        }
                                    }

                                    // Call the function to adjust iframe width on page load
                                    adjustIframeWidth();

                                    // Call the function to adjust iframe width on window resize
                                    window.addEventListener('resize', adjustIframeWidth);

                                    // debugger;
                                    // document.getElementById("cancelSubscriptionCheck").addEventListener("click", function () {
                                    //     const overlay = document.getElementById('loadingOverlay');
                                    //     overlay.style.display = 'flex';
                                    //
                                    //     debugger;
                                    //     $.ajaxSetup({
                                    //         headers: {
                                    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    //         }
                                    //     });
                                    //     $.ajax({
                                    //         url: '/cancelMonthPay', // URL вашего маршрута
                                    //         type: 'POST',
                                    //         data: {},
                                    //         success: function (response) {
                                    //             if (response) {
                                    //                 window.location.reload()
                                    //             } else {
                                    //                 alert('Ошибка отмены.');
                                    //             }
                                    //         },
                                    //         error: function (xhr) {
                                    //             alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                                    //         }
                                    //     });
                                    // });

                                    const subscriptionInput = document.getElementById('subscription');
                                    const subscriptionSelectContainer = document.getElementById('subscriptionSelectContainer');
                                    const subscriptionContainer = document.getElementById('subscription-container');
                                    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                                    const downgradeModal = new bootstrap.Modal(document.getElementById('downgradeModal'));
                                    const cancellationModal = new bootstrap.Modal(document.getElementById('cancellationModal'));
                                    const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
                                    const productNameSpan = document.getElementById('productName');
                                    const confirmPurchaseBtn = document.getElementById('confirmPurchaseBtn');
                                    const confirmDowngradeBtn = document.getElementById('confirmDowngradeBtn');
                                    const confirmCancellationBtn = document.getElementById('confirmCancellationBtn');
                                    const confirmVerificationBtn = document.getElementById('confirmVerificationBtn');
                                    const verificationCodeInput = document.getElementById('verificationCode');

                                    let selectedProduct = '';
                                    let selectedProductLevel = 0;
                                    const currentLevel = {{ $currentSubscriptionLevel }}; // Замените на реальный уровень текущей подписки

                                    document.addEventListener('click', function (event) {
                                        if (event.target && event.target.id === 'confirmCancellationBtn') {
                                            debugger
                                        }
                                    });

                                    $(".cancel-subs").on("click", function (e) {
                                        window.location.reload();
                                    })

                                    // Функция для переключения на селектор
                                    function showSelect() {
                                        subscriptionInput.style.display = 'none';
                                        subscriptionSelectContainer.style.display = 'block';
                                        setTimeout(() => {
                                            subscriptionSelectContainer.classList.add('show');
                                        }, 10); // Небольшая задержка для применения анимации
                                    }

                                    // Функция для переключения обратно на инпут
                                    function hideSelect() {
                                        subscriptionSelectContainer.classList.remove('show');
                                        subscriptionSelectContainer.classList.add('hide');
                                        setTimeout(() => {
                                            subscriptionSelectContainer.style.display = 'none';
                                            subscriptionSelectContainer.classList.remove('hide');
                                        }, 300); // Длительность анимации должна соответствовать CSS
                                        subscriptionInput.style.display = 'block';
                                    }

                                    // Обработка клика на инпут
                                    subscriptionInput.addEventListener('click', showSelect);

                                    // Обработка клика вне селектора
                                    document.addEventListener('click', function (event) {
                                        const isClickInside = subscriptionContainer.contains(event.target);
                                        if (!isClickInside) {
                                            hideSelect();
                                        }
                                    });

                                    // Обработка клика на кнопку "Купить" и "Отменить"
                                    subscriptionSelectContainer.addEventListener('click', function (event) {
                                        debugger;
                                        if (event.target.classList.contains('buy-btn')) {
                                            event.target.classList.add('btn-clicked'); // Добавляем класс для анимации

                                            setTimeout(() => {
                                                event.target.classList.remove('btn-clicked'); // Убираем класс после анимации
                                            }, 200); // Длительность анимации должна соответствовать CSS

                                            selectedProduct = $(event.target).data('product');
                                            $("#productId").val(selectedProduct);
                                            $("input[name='OutSum']").val($(".buy-btn").data('price'));
                                            $("input[name='Description']").val($(".buy-btn").data('name'));
                                            $("input[name='SignatureValue']").val("{{md5("APPP:750:$invoiceId:LFuWhwWF2H63Uaf3Wwz1")}}");


                                            //
                                            selectedProductLevel = parseInt(event.target.parentElement.getAttribute('data-level'));
                                            const productName = event.target.previousElementSibling.textContent.trim();
                                            productNameSpan.textContent = productName;

                                            if (event.target.classList.contains('btn-danger')) {
                                                cancellationModal.show();
                                            } else if (selectedProductLevel > currentLevel) {
                                                confirmationModal.show();
                                            } else {
                                                downgradeModal.show();
                                            }
                                        }
                                    });

                                    /*  // Обработка подтверждения покупки
                                      confirmPurchaseBtn.addEventListener('click', function() {

                                          // Здесь можно добавить логику для выполнения покупки
                                         // alert(`Вы купили подписку ${selectedProduct}`);
                                          confirmationModal.hide();
                                          //hideSelect();
                                          window.location.reload();
                                          subscriptionInput.value = document.querySelector(`.subscription-option[data-product="${selectedProduct}"] span`).textContent;
                                      });*/

                                    // Обработка подтверждения отмены подписки
                                    confirmCancellationBtn.addEventListener('click', function () {
                                        debugger;
                                        cancellationModal.hide();
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        $.ajax({
                                            url: '/cancelSubscribe', // URL вашего маршрута
                                            type: 'POST',
                                            success: function (response) {
                                                if (response) {
                                                    window.location.reload();
                                                    // subscriptionInput.value = document.querySelector(`.subscription-option[data-product="${selectedProduct}"] span`).textContent;
                                                } else {
                                                    alert('Ошибка подтверждения.');
                                                }
                                            },
                                            error: function (xhr) {
                                                alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                                            }
                                        });
                                        // verificationModal.show();
                                    });

                                    // Обработка подтверждения понижения уровня
                                    confirmDowngradeBtn.addEventListener('click', function () {
                                        downgradeModal.hide();
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        $.ajax({
                                            url: '/verify-code', // URL вашего маршрута
                                            type: 'POST',
                                            data: {},
                                            success: function (response) {
                                                if (response) {
                                                    subscriptionInput.value = document.querySelector(`.subscription-option[data-product="${selectedProduct}"] span`).textContent;
                                                } else {
                                                    alert('Ошибка подтверждения.');
                                                }
                                            },
                                            error: function (xhr) {
                                                alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                                            }
                                        });
                                        verificationModal.show();
                                    });

                                    // Обработка подтверждения ввода кода
                                    confirmVerificationBtn.addEventListener('click', function () {
                                        const verificationCode = verificationCodeInput.value.trim();
                                        $.ajaxSetup({
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            }
                                        });
                                        $.ajax({
                                            url: '/verify-code/success', // URL вашего маршрута
                                            type: 'POST',
                                            data: {
                                                code: verificationCode
                                            },
                                            success: function (response) {
                                                if (response) {
                                                    // Здесь можно добавить логику для проверки кода подтверждения
                                                    window.location.reload();
                                                    verificationModal.hide();
                                                    hideSelect();
                                                    subscriptionInput.value = document.querySelector(`.subscription-option[data-product="${selectedProduct}"] span`).textContent;
                                                } else {
                                                    alert('Код подтверждения не правильный.');
                                                }
                                            },
                                            error: function (xhr) {
                                                alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                                            }
                                        });


                                    });
                                });
                            </script>

                        </div>

                    </div>
                    <style>
                        .transaction-card {
                            border-radius: 15px;
                            overflow: hidden;
                            border: none;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            transition: box-shadow 0.3s ease, transform 0.3s ease;
                        }

                        .transaction-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                        }

                        .transaction-card .card-title h3 {
                            font-size: 1.5rem;
                            font-weight: 600;
                            color: #613482;
                            margin: 1rem;
                        }

                        .transaction-card .table {
                            border-collapse: separate;
                            border-spacing: 0;
                        }

                        .transaction-card .table thead th {
                            background-color: #f8f9fa;
                            color: #613482;
                            font-weight: 600;
                            border-bottom: 2px solid #e9ecef;
                        }

                        .transaction-card .table tbody tr:nth-child(odd) {
                            background-color: #f9f9f9;
                        }

                        .transaction-card .table tbody tr:nth-child(even) {
                            background-color: #ffffff;
                        }

                        .transaction-card .table tbody tr:hover {
                            background-color: #f1f1f1;
                        }

                        .transaction-card .table td, .transaction-card .table th {
                            vertical-align: middle;
                            padding: 1rem;
                            border: 1px solid #dee2e6;
                        }

                        .transaction-card .status-success {
                            color: green;
                            font-weight: bold;
                        }

                        .transaction-card .status-failed {
                            color: red;
                            font-weight: bold;
                        }

                        .transaction-card .status-pending {
                            color: #ff8b17;
                            font-weight: bold;
                        }

                        .transaction-card .alert-warning {
                            border-radius: 0;
                            margin: 0;
                            padding: 0.5rem 1rem;
                        }
                    </style>
                    <div class="tabs">
                        <ul class="tab-list">
                            <li class="tab active" data-tab="tab-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-cash-coin" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/>
                                    <path
                                        d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/>
                                    <path
                                        d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/>
                                    <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>
                                </svg>
                                Транзакции
                            </li>
                            @if (auth()->user() && auth()->user()->group == "admin")

                                <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-piggy-bank" viewBox="0 0 16 16">
                                        <path
                                            d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0m1.138-1.496A6.6 6.6 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 0 0 .286-.958A7.6 7.6 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962"/>
                                        <path fill-rule="evenodd"
                                              d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069q0-.218-.02-.431c.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a1 1 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.74.74 0 0 0-.375.562c-.024.243.082.48.32.654a2 2 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595M2.516 6.26c.455-2.066 2.667-3.733 5.448-3.733 3.146 0 5.536 2.114 5.536 4.542 0 1.254-.624 2.41-1.67 3.248a.5.5 0 0 0-.165.535l.66 2.175h-.985l-.59-1.487a.5.5 0 0 0-.629-.288c-.661.23-1.39.359-2.157.359a6.6 6.6 0 0 1-2.157-.359.5.5 0 0 0-.635.304l-.525 1.471h-.979l.633-2.15a.5.5 0 0 0-.17-.534 4.65 4.65 0 0 1-1.284-1.541.5.5 0 0 0-.446-.275h-.56a.5.5 0 0 1-.492-.414l-.254-1.46h.933a.5.5 0 0 0 .488-.393m12.621-.857a.6.6 0 0 1-.098.21l-.044-.025c-.146-.09-.157-.175-.152-.223a.24.24 0 0 1 .117-.173c.049-.027.08-.021.113.012a.2.2 0 0 1 .064.199"/>
                                    </svg>
                                    Платежи участников
                                </li>
                            @endif
                            <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-person-arms-up" viewBox="0 0 16 16">
                                    <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                                    <path
                                        d="m5.93 6.704-.846 8.451a.768.768 0 0 0 1.523.203l.81-4.865a.59.59 0 0 1 1.165 0l.81 4.865a.768.768 0 0 0 1.523-.203l-.845-8.451A1.5 1.5 0 0 1 10.5 5.5L13 2.284a.796.796 0 0 0-1.239-.998L9.634 3.84a.7.7 0 0 1-.33.235c-.23.074-.665.176-1.304.176-.64 0-1.074-.102-1.305-.176a.7.7 0 0 1-.329-.235L4.239 1.286a.796.796 0 0 0-1.24.998l2.5 3.216c.317.316.475.758.43 1.204Z"/>
                                </svg>
                                Мои встречи
                            </li>
                            <!--<li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-4">-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"-->
                            <!--         class="bi bi-camera-reels" viewBox="0 0 16 16">-->
                            <!--        <path d="M6 3a3 3 0 1 1-6 0 3 3 0 0 1 6 0M1 3a2 2 0 1 0 4 0 2 2 0 0 0-4 0"/>-->
                            <!--        <path-->
                            <!--            d="M9 6h.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 7.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 16H2a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm6 8.73V7.27l-3.5 1.555v4.35zM1 8v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1"/>-->
                            <!--        <path d="M9 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6M7 3a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>-->
                            <!--    </svg>-->
                            <!--    Видеотека-->
                            <!--</li>-->
                            <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-postcard" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                          d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm7.5.5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0zM2 5.5a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5M10.5 5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zM13 8h-2V6h2z"/>
                                </svg>
                                Мои курсы
                            </li>
                            <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-buildings" viewBox="0 0 16 16">
                                    <path
                                        d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                    <path
                                        d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/>
                                </svg>
                                Мои клубы
                            </li>
                            @if (auth()->user() && auth()->user()->group == "admin")

                                <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-7">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-people" viewBox="0 0 16 16">
                                        <path
                                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                    </svg>
                                    Пользователи
                                </li>
                                <li style="margin-top: 3px; width: 50%" class="tab" data-tab="tab-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-passport" viewBox="0 0 16 16">
                                        <path
                                            d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6M6 8a2 2 0 1 1 4 0 2 2 0 0 1-4 0m-.5 4a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1z"/>
                                        <path
                                            d="M3.232 1.776A1.5 1.5 0 0 0 2 3.252v10.95c0 .445.191.838.49 1.11.367.422.908.688 1.51.688h8a2 2 0 0 0 2-2V4a2 2 0 0 0-1-1.732v-.47A1.5 1.5 0 0 0 11.232.321l-8 1.454ZM4 3h8a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1"/>
                                    </svg>
                                    Подписки
                                </li>

                            @endif


                        </ul>

                        <div class="tab-content active" id="tab-1">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card">
                                    <div class="card-title">
                                        <h3>Транзакции
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                      d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/>
                                                <path
                                                    d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/>
                                                <path
                                                    d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/>
                                                <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Сумма</th>
                                                    <th scope="col">Действие</th>
                                                    <th scope="col">Согласие автосписания</th>
                                                    <th scope="col">Дата оплаты</th>
                    
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($mergedDataPaginated as $key => $transaction)
                                                    <tr>
                                                        <th scope="row">{{$key+1}}</th>
                                                        
                                                        <td>
                                                                     @if($transaction->product)
                                                                             {{$transaction->price}}
                                                                             @if($transaction->price)
                                                                                                                                         <svg xmlns="http://www.w3.org/2000/svg"
                                                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                 fill="#000000" height="15px" width="15px" version="1.1"
                                                                 id="Layer_1" viewBox="0 0 440 440"
                                                                 xml:space="preserve">
<g>
    <path
        d="M232.522,242.428c63.913,0,115.91-54.382,115.91-121.227C348.432,54.37,296.435,0,232.522,0H120.568v282.428h-29v30h29V440   h30V312.428h101.955v-30H150.568v-40H232.522z M150.568,30h81.955c47.371,0,85.91,40.912,85.91,91.201   c0,50.303-38.539,91.227-85.91,91.227h-81.955V30z"/>
</g>
</svg>
                                                                             @endif
                                                                     @else
                                                                             {{$transaction->sum}}
                                                                             
                                                                                       @if($transaction->sum)
                                                                                                                                         <svg xmlns="http://www.w3.org/2000/svg"
                                                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                 fill="#000000" height="15px" width="15px" version="1.1"
                                                                 id="Layer_1" viewBox="0 0 440 440"
                                                                 xml:space="preserve">
<g>
    <path
        d="M232.522,242.428c63.913,0,115.91-54.382,115.91-121.227C348.432,54.37,296.435,0,232.522,0H120.568v282.428h-29v30h29V440   h30V312.428h101.955v-30H150.568v-40H232.522z M150.568,30h81.955c47.371,0,85.91,40.912,85.91,91.201   c0,50.303-38.539,91.227-85.91,91.227h-81.955V30z"/>
</g>
</svg>
                                                                             @endif
                                                                     @endif
                                                                     
                                                    

                                                        </td>
                                                        @if($transaction->product)
                                                 
                                                                @if($transaction->action == 'cancel')
                                                             <td><span class="text-danger">Отмена</span> </br> <span style="font-size:14px">{{$transaction->product->name}}</span></td>
                                                            @else
                                                                  <td ><span class="text-success">Подключена</span> </br> <span style="font-size:14px">{{$transaction->product->name}}</span></td>
                    
                                                            @endif
                                                        @else
                                                           <td>{{!empty($transaction->product_name) ? $transaction->product_name : "Пополнение счета"}}</td>
                                                        @endif
                                                        
                                                          @if($transaction->product && $transaction->action != 'cancel')
                                                             <td>{{$transaction->auto ? 'Включено' : 'Без автосписания'}}</td>
                                                          @else
                                                             <td></td>
                                                          @endif
                                                     
                                                
                                                        <td>{{$transaction->created_at}}</td>
                                                <!--        <td class="@if($transaction->state == 'failed')-->
                                                <!--    status-failed-->
                                                <!--@elseif($transaction->state == 'success')-->
                                                <!--    status-success-->
                                                <!--@elseif($transaction->state == 'pending')-->
                                                <!--    status-pending-->
                                                <!--@endif">-->
                                                <!--            @if($transaction->state == 'failed')-->
                                                <!--                Ошибка-->
                                                <!--            @elseif($transaction->state == 'success')-->
                                                <!--                Успешно-->
                                                <!--            @elseif($transaction->state == 'pending')-->
                                                <!--                Ожидание-->
                                                <!--            @endif-->
                                                <!--        </td>-->
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-warning" role="alert">
                                                                Транзакций нет
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <div class="d-flex justify-content-center mt-4">
                                                {{ $mergedDataPaginated->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-content" id="tab-2">
                            @if (auth()->user() && auth()->user()->group == "admin")
                                <div class="container-xl px-4 mt-4">
                                    <div class="transaction-card card">
                                        <div class="card-title">
                                            <h3>Платежи участников
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-piggy-bank" viewBox="0 0 16 16">
                                                    <path
                                                        d="M5 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0m1.138-1.496A6.6 6.6 0 0 1 7.964 4.5c.666 0 1.303.097 1.893.273a.5.5 0 0 0 .286-.958A7.6 7.6 0 0 0 7.964 3.5c-.734 0-1.441.103-2.102.292a.5.5 0 1 0 .276.962"/>
                                                    <path fill-rule="evenodd"
                                                          d="M7.964 1.527c-2.977 0-5.571 1.704-6.32 4.125h-.55A1 1 0 0 0 .11 6.824l.254 1.46a1.5 1.5 0 0 0 1.478 1.243h.263c.3.513.688.978 1.145 1.382l-.729 2.477a.5.5 0 0 0 .48.641h2a.5.5 0 0 0 .471-.332l.482-1.351c.635.173 1.31.267 2.011.267.707 0 1.388-.095 2.028-.272l.543 1.372a.5.5 0 0 0 .465.316h2a.5.5 0 0 0 .478-.645l-.761-2.506C13.81 9.895 14.5 8.559 14.5 7.069q0-.218-.02-.431c.261-.11.508-.266.705-.444.315.306.815.306.815-.417 0 .223-.5.223-.461-.026a1 1 0 0 0 .09-.255.7.7 0 0 0-.202-.645.58.58 0 0 0-.707-.098.74.74 0 0 0-.375.562c-.024.243.082.48.32.654a2 2 0 0 1-.259.153c-.534-2.664-3.284-4.595-6.442-4.595M2.516 6.26c.455-2.066 2.667-3.733 5.448-3.733 3.146 0 5.536 2.114 5.536 4.542 0 1.254-.624 2.41-1.67 3.248a.5.5 0 0 0-.165.535l.66 2.175h-.985l-.59-1.487a.5.5 0 0 0-.629-.288c-.661.23-1.39.359-2.157.359a6.6 6.6 0 0 1-2.157-.359.5.5 0 0 0-.635.304l-.525 1.471h-.979l.633-2.15a.5.5 0 0 0-.17-.534 4.65 4.65 0 0 1-1.284-1.541.5.5 0 0 0-.446-.275h-.56a.5.5 0 0 1-.492-.414l-.254-1.46h.933a.5.5 0 0 0 .488-.393m12.621-.857a.6.6 0 0 1-.098.21l-.044-.025c-.146-.09-.157-.175-.152-.223a.24.24 0 0 1 .117-.173c.049-.027.08-.021.113.012a.2.2 0 0 1 .064.199"/>
                                                </svg>
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <!-- Поисковая строка -->
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="user-search-query"
                                                       placeholder="Введите телефон или почту">
                                                <button class="btn btn-primary" id="user-search-button" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                                        <path
                                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                                    </svg>
                                                    Поиск
                                                </button>
                                            </div>

                                            <!-- Отображение данных о пользователе и транзакциях -->
                                            <div id="user-info" class="mt-4"></div>

                                            <!-- Кнопка выгрузки данных в Excel -->

                                        </div>
                                    </div>
                                </div>

                                <script>
                                    $(document).ready(function () {

                                        $('#user-search-button').on('click', function () {
                                            const overlay = document.getElementById('loadingOverlay');
                                            overlay.style.display = 'flex';
                                            var query = $('#user-search-query').val();

                                            if (query.length === 0) {
                                                alert('Введите телефон или почту');
                                                overlay.style.display = 'none';
                                                return;
                                            }

                                            // Отправка AJAX-запроса
                                            $.ajax({
                                                url: '/admin/search-user',  // Маршрут для поиска
                                                type: 'GET',
                                                data: {query: query},
                                                success: function (response) {
                                                    overlay.style.display = 'none';
                                                    $('#user-info').html(response);
                                                },
                                                error: function (xhr) {
                                                    overlay.style.display = 'none';
                                                    $('#user-info').html('<div class="alert alert-danger">Произошла ошибка при поиске.</div>');
                                                }
                                            });
                                        });
                                    });

                                </script>

                            @endif

                        </div>
                        <div class="tab-content" id="tab-3">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card">
                                    <div class="card-title">
                                        <h3>Мои встречи
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-person-arms-up" viewBox="0 0 16 16">
                                                <path d="M8 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                                                <path
                                                    d="m5.93 6.704-.846 8.451a.768.768 0 0 0 1.523.203l.81-4.865a.59.59 0 0 1 1.165 0l.81 4.865a.768.768 0 0 0 1.523-.203l-.845-8.451A1.5 1.5 0 0 1 10.5 5.5L13 2.284a.796.796 0 0 0-1.239-.998L9.634 3.84a.7.7 0 0 1-.33.235c-.23.074-.665.176-1.304.176-.64 0-1.074-.102-1.305-.176a.7.7 0 0 1-.329-.235L4.239 1.286a.796.796 0 0 0-1.24.998l2.5 3.216c.317.316.475.758.43 1.204Z"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Название</th>
                                                    <th scope="col">Ссылка встречи</th>
                                                    <th scope="col">Дата</th>
                                                    <th scope="col">Действия</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse(\App\Models\ParticipantActions::where("user_id", auth()->user()->id)->where("object_name", "meeting")->paginate(5) as $key => $action)
                                                    @php $ourMeeting = \App\Models\Blog::where("id", $action->object_id)->first(); @endphp
                                                    @if(isset($ourMeeting))
                                                        <tr>
                                                            <th scope="row">{{$key+1}}</th>

                                                            <td>{{$ourMeeting->name}}</td>
                                                            <td><a target="_blank"
                                                                   href="/ourMeetings/{{$ourMeeting->id}}">Ссылка</a>
                                                            </td>
                                                            <td>{{$action->created_at}}</td>
                                                            <td>
                                                                <button
                                                                    onclick="window.location.href = '/takePart/delete/{{$ourMeeting->id}}/{{auth()->user()->id}}'"
                                                                    class="btn btn-danger ">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                         height="16" fill="currentColor"
                                                                         class="bi bi-x-octagon" viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"/>
                                                                        <path
                                                                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                                    </svg>
                                                                    Отменить
                                                                </button>
                                                            </td>

                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-warning" role="alert">
                                                                Встреч нет
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <div class="d-flex justify-content-center mt-4">
                                                {{ \App\Models\ParticipantActions::where("user_id", auth()->user()->id)
                                                    ->where("object_name", "meeting")
                                                    ->paginate(5)
                                                    ->appends(['meeting_page' => request()->input('meeting_page')])
                                                    ->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       <!-- <div class="tab-content" id="tab-4">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card modern-card">
                                    <div class="card-title">
                                        <h3 class="modern-title">Видеотека
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-camera-reels" viewBox="0 0 16 16">
                                                <path
                                                    d="M6 3a3 3 0 1 1-6 0 3 3 0 0 1 6 0M1 3a2 2 0 1 0 4 0 2 2 0 0 0-4 0"/>
                                                <path
                                                    d="M9 6h.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 7.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 16H2a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm6 8.73V7.27l-3.5 1.555v4.35zM1 8v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1"/>
                                                <path
                                                    d="M9 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6M7 3a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="video-buttons-container" class="btn-container">
                                            <a href="{{ $subscription->level != 0 ? route('video.detail', ['id' => 1]) : 'javascript:void(0)'   }}"
                                            onclick="{{ $subscription->level == 0 ? 'alert(\'Видеотека доступна только по подписке\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Всё о развитии и продвижении частной практики
                                            </a>
                                            <a href="{{ $subscription->level != 0 ? route('video.detail', ['id' => 2]) : 'javascript:void(0)'   }}"
                                              onclick="{{ $subscription->level == 0 ? 'alert(\'Видеотека доступна только по подписке\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Всё о профессиональной подготовке психолога к частной практике
                                            </a>
                                            <a href="{{ $subscription->level != 0 ? route('video.detail', ['id' => 3]) : 'javascript:void(0)'   }}"
                                              onclick="{{ $subscription->level == 0 ? 'alert(\'Видеотека доступна только по подписке\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Фрагменты с курсов Портала ДПО
                                            </a>
                                            @php
                                                $categoryVideo = \App\Models\Category::whereNull('parent_id')->get();
                                            @endphp

                                            @foreach($categoryVideo as $cat)
                                                <a           onclick="{{ $subscription->level == 0 ? 'alert(\'Видеотека доступна только по подписке\')' : '' }}" href="{{ $subscription->level != 0 ? route('show.category', ['id' => $cat->id]) : 'javascript:void(0)' }}"
                                                   class="btn btn-brand modern-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-fast-forward"
                                                         viewBox="0 0 16 16">
                                                        <path
                                                            d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                        <path
                                                            d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    </svg>
                                                    {{$cat->name}}
                                                </a>

                                                {{--<a href="{{ route('video.detail', ['id' => 5]) }}"
                                                   class="btn btn-brand modern-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                        <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                        <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    </svg>
                                                    КУРСЫ
                                                </a>--}}
                                            @endforeach
                                            {{--   <!-- Дополнительные кнопки, которые будут скрыты -->
                                               <a href="{{ route('video.detail', ['id' => 4]) }}" class="btn btn-brand modern-btn hidden-btn">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                       <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                       <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                   </svg>
                                                   Скрытая кнопка
                                               </a>
                                               <a  href="{{ route('video.detail', ['id' => 5]) }}" class="btn btn-brand modern-btn hidden-btn">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                       <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                       <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                   </svg>
                                                   Скрытая кнопка
                                               </a>--}}
                                        </div>
                                        <button id="show-more-btn" class="btn btn-brand mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                            </svg>
                                            Смотреть все
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>-->

                        <div class="tab-content" id="tab-5">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card">
                                    <div class="card-title">
                                        <h3>Мои курсы
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-postcard" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                      d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm7.5.5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0zM2 5.5a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 0 1H2.5a.5.5 0 0 1-.5-.5M10.5 5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zM13 8h-2V6h2z"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Название</th>
                                                    <th scope="col">Ссылка курса</th>
                                                    <th scope="col">Дата</th>
                                                    {{--                                                <th scope="col">Действия</th>--}}
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $subscription = \App\Models\Subscription::where('user_id', auth()->user()->id)->first();
                                                        $productPermission = \App\Models\ProductPermission::join('products', 'products.id', '=', 'product_permissions.product_id')
                                                            ->where('products.level', '=', $subscription ? $subscription->level : null)
                                                            ->first();
                                                    @endphp
                                     @if ($subscription && $productPermission && $productPermission->course)

                                                    @forelse(\App\Models\Course::where('id','>',0)->get() as $key => $action)

                                                        @php



                                                            $course = $action;
                                                        @endphp



                                                        <tr>
                                                            <th scope="row">{{$key+1}}</th>
                                                            <td>{{$course->title}}</td>
                                                            <td><a target="_blank"
                                                                   href="{{$course->feedback}}">Ссылка</a>
                                                            </td>
                                                            @php
                                                                $today = \Carbon\Carbon::now()->format('Y-m-d');


                                                                $date = \App\Models\CourseContent::where('course_id',$course->id)->where('date','>=',$today)->orderBy('date')->first();
                                                            @endphp
                                                            <td>
                                                                @if($date)
                                                                    {{ date('H:i', strtotime($date->start_time)) . ' ' . date('d.m.Y', strtotime($date->date)) }}
                                                                @else
                                                                    <span style="color: #aaa;">Нет расписания</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td>{{$course->times['training'] ?? ""}}</td> --}}
                                                            {{--<td><button onclick="window.location.href = '/takePart/delete/{{$course->id}}/{{auth()->user()->id}}'" class="btn btn-danger "><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-octagon" viewBox="0 0 16 16">
                                                                        <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"/>
                                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                                    </svg> Отменить</button></td>--}}

                                                        </tr>

                                                    @empty
                                                        <tr>
                                                            <td colspan="6">
                                                                <div class="alert alert-warning" role="alert">
                                                                    Курсы доступны только по подписке {{\App\Models\Product::where('level',5)->first() ? \App\Models\Product::where('level',5)->first()->name : ''}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                @else
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-warning" role="alert">
                                                                Курсы доступны только по подписке {{\App\Models\Product::where('level',5)->first() ? \App\Models\Product::where('level',5)->first()->name : ''}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif

                                                </tbody>
                                            </table>
                                            {{--                                        <div class="d-flex justify-content-center mt-4">--}}
                                            {{--                                            {{ \App\Models\ParticipantActions::where("user_id", auth()->user()->id)--}}
                                            {{--                                                ->where("object_name", "courses")--}}
                                            {{--                                                ->paginate(5)--}}
                                            {{--                                                ->links('pagination::bootstrap-4') }}--}}
                                            {{--                                        </div>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="tab-content" id="tab-6">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card">
                                    <div class="card-title">
                                        <h3>Мои клубы
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-buildings" viewBox="0 0 16 16">
                                                <path d="M14.763.075A.5.5 0 0 1 15 .5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V10a.5.5 0 0 1 .342-.474L6 7.64V4.5a.5.5 0 0 1 .276-.447l8-4a.5.5 0 0 1 .487.022M6 8.694 1 10.36V15h5zM7 15h2v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.309l-7 3.5z"/>
                                                <path d="M2 11h1v1H2zm2 0h1v1H4zm-2 2h1v1H2zm2 0h1v1H4zm4-4h1v1H8zm2 0h1v1h-1zm-2 2h1v1H8zm2 0h1v1h-1zm2-2h1v1h-1zm0 2h1v1h-1zM8 7h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zM8 5h1v1H8zm2 0h1v1h-1zm2 0h1v1h-1zm0-2h1v1h-1z"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Название клуба</th>
                                                    <th scope="col">Ближайшее занятие</th>
                                                    <th scope="col">Ссылка на Zoom</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php $index = 0; @endphp
                                                @if ($subscription && $productPermission && $productPermission->club)
                                                    @if (isset($clubs))
                                                        @forelse($clubs as $c)
                                                            @php $index++; @endphp
                                                            <tr>
                                                                <th scope="row">{{$index}}</th>
                                                                <td><a href="/club/{{$c->id}}" target="_blank">{{$c->title}}</a></td>
                                                                <td>
                                                                    @if($c->clubDates)
                                                                        {{ $c->clubDates->start_time . ' ' . date('d.m.Y', strtotime($c->clubDates->date)) }}
                                                                    @else
                                                                        Нет запланированных занятий
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($c->feedback)
                                                                        <a href="{{$c->feedback}}" target="_blank" class="btn btn-sm btn-primary">Zoom</a>
                                                                    @else
                                                                        Ссылка появится позже
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4">
                                                                    <div class="alert alert-warning" role="alert">
                                                                        Клубы доступны только по подписке {{\App\Models\Product::where('level',5)->first() ? \App\Models\Product::where('level',5)->first()->name : '' }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    @else
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="alert alert-warning" role="alert">
                                                                    Клубы доступны только по подписке {{\App\Models\Product::where('level',5)->first() ? \App\Models\Product::where('level',5)->first()->name : ''}}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td colspan="4">
                                                            <div class="alert alert-warning" role="alert">
                                                                Клубы доступны только по подписке {{\App\Models\Product::where('level',5)->first() ? \App\Models\Product::where('level',5)->first()->name : ''}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content" id="tab-7">
                            <div class="container-xl px-4 mt-4">
                                <div class="user-management-card card">
                                    <br>
                                    <div class="card-title">
                                        <h3>Пользователи (Доступ <b>{{$authedRole}}</b>)</h3>
                                    </div>
                                    <div class="card-body">
                                        <button type="button" class="btn btn-dark" id="addAdmin">
                                            Добавить администратора
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-person-gear" viewBox="0 0 16 16">
                                                <path
                                                    d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m.256 7a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1zm3.63-4.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
                                            </svg>
                                        </button>
                                        <br><br>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Эл. Почта</th>
                                                    <th scope="col">Роль</th>
                                                    <th scope="col">Телефон</th>
                                                    <th scope="col">Изменить роль</th>
                                                    <th scope="col">Изменить доступ</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($users as $key => $user)
                                                    <tr>
                                                        <th scope="row">{{$key+1}}</th>
                                                        <td>{{$user->email}}</td>
                                                        <td>{{$user->role}}</td>
                                                        <td>{{$user->phone}}</td>
                                                        <td>
                                                            <button id="editRoleBtn" data-user-id="{{$user->id}}"
                                                                    class="btn btn-primary editRoleBtn" type="button">
                                                                Изменить роль
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <button data-user-id="{{$user->id}}"
                                                                    class="btn btn-success editPerms" type="button">
                                                                Изменить доступ
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">
                                                            <div class="alert alert-warning" role="alert">
                                                                Пользователей нет
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center mt-4">
                                                {{ $users->appends(['users_page' => request()->input('users_page')])->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-content" id="tab-8">
                            <div class="container-xl px-4 mt-4">
                                <div class="subscriptions-card card">
                                    <br>
                                    <div class="card-title">
                                        <h3>Подписки</h3>
                                    </div>
                                    <div class="card-body">
                                        <button type="button" class="btn btn-success" id="addProduct">
                                            Добавить подписку
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-plus-square-dotted"
                                                 viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 0q-.25 0-.487.048l.194.98A1.5 1.5 0 0 1 2.5 1h.458V0zm2.292 0h-.917v1h.917zm1.833 0h-.917v1h.917zm1.833 0h-.916v1h.916zm1.834 0h-.917v1h.917zm1.833 0h-.917v1h.917zM13.5 0h-.458v1h.458q.151 0 .293.029l.194-.981A2.5 2.5 0 0 0 13.5 0m2.079 1.11a2.5 2.5 0 0 0-.69-.689l-.556.831q.248.167.415.415l.83-.556zM1.11.421a2.5 2.5 0 0 0-.689.69l.831.556c.11-.164.251-.305.415-.415zM16 2.5q0-.25-.048-.487l-.98.194q.027.141.028.293v.458h1zM.048 2.013A2.5 2.5 0 0 0 0 2.5v.458h1V2.5q0-.151.029-.293zM0 3.875v.917h1v-.917zm16 .917v-.917h-1v.917zM0 5.708v.917h1v-.917zm16 .917v-.917h-1v.917zM0 7.542v.916h1v-.916zm15 .916h1v-.916h-1zM0 9.375v.917h1v-.917zm16 .917v-.917h-1v.917zm-16 .916v.917h1v-.917zm16 .917v-.917h-1v.917zm-16 .917v.458q0 .25.048.487l.98-.194A1.5 1.5 0 0 1 1 13.5v-.458zm16 .458v-.458h-1v.458q0 .151-.029.293l.981.194Q16 13.75 16 13.5M.421 14.89c.183.272.417.506.69.689l.556-.831a1.5 1.5 0 0 1-.415-.415zm14.469.689c.272-.183.506-.417.689-.69l-.831-.556c-.11.164-.251.305-.415.415l.556.83zm-12.877.373Q2.25 16 2.5 16h.458v-1H2.5q-.151 0-.293-.029zM13.5 16q.25 0 .487-.048l-.194-.98A1.5 1.5 0 0 1 13.5 15h-.458v1zm-9.625 0h.917v-1h-.917zm1.833 0h.917v-1h-.917zm1.834-1v1h.916v-1zm1.833 1h.917v-1h-.917zm1.833 0h.917v-1h-.917zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                                            </svg>
                                        </button>
                                        <br><br>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Подписка</th>
                                                    <th scope="col">Цена</th>
                                                    <th scope="col">Цена за первую неделю</th>
                                                    <th scope="col">Уровень</th>
                                                    <th scope="col">Действия</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @forelse(\App\Models\Product::paginate(5) as $key => $product)
                                                    @if($product->level != -1)
                                                        <tr>
                                                            <th scope="row">{{$key+1}}</th>
                                                            <td>{{$product->name}}</td>
                                                            <td>{{$product->price}}</td>
                                                            <td>{{!empty($product->first_week_price) ? $product->first_week_price : "Цена не указана"}}</td>
                                                            <td>{{$product->level}}</td>
                                                            <td style="display: flex">
                                                                <button type="button" data-product-id="{{$product->id}}"
                                                                        class="btn btn-success editProductBtn">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                         height="16" fill="currentColor"
                                                                         class="bi bi-pencil-square"
                                                                         viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path>
                                                                        <path fill-rule="evenodd"
                                                                              d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"></path>
                                                                    </svg>
                                                                </button>

                                                                @if($product->visible == true)
                                                                    <button style="margin-left: 5px" type="button"
                                                                            data-product-id="{{$product->id}}"
                                                                            class="btn btn-warning"
                                                                            onclick="window.location.href='/product/hide/{{$product->id}}'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             width="16" height="16" fill="currentColor"
                                                                             class="bi bi-eye-slash"
                                                                             viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                                                                            <path
                                                                                d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                                                                            <path
                                                                                d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                                                                        </svg>
                                                                    </button>
                                                                @else
                                                                    <button style="margin-left: 5px" type="button"
                                                                            data-product-id="{{$product->id}}"
                                                                            class="btn btn-info"
                                                                            onclick="window.location.href='/product/hide/{{$product->id}}'">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                             width="16" height="16" fill="currentColor"
                                                                             class="bi bi-eye" viewBox="0 0 16 16">
                                                                            <path
                                                                                d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                                            <path
                                                                                d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                                        </svg>
                                                                    </button>
                                                                @endif


                                                                <!--<button style="margin-left: 5px" type="button"-->
                                                                <!--        data-product-id="{{$product->id}}"-->
                                                                <!--        class="btn btn-danger"-->
                                                                <!--        onclick="window.location.href='/product/delete/{{$product->id}}'">-->
                                                                <!--    <svg xmlns="http://www.w3.org/2000/svg" width="16"-->
                                                                <!--         height="16" fill="currentColor"-->
                                                                <!--         class="bi bi-trash3" viewBox="0 0 16 16">-->
                                                                <!--        <path-->
                                                                <!--            d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>-->
                                                                <!--    </svg>-->
                                                                <!--</button>-->
                                                            </td>
                                                        </tr>
                                                    @endif

                                                @empty
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="alert alert-warning" role="alert">
                                                                Подписок нет
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>

                                            <div class="d-flex justify-content-center mt-4">
                                                {{ \App\Models\Product::paginate(5)->appends(['product_page' => request()->input('product_page')])->links('pagination::bootstrap-4') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                       <div style="margin:20px">
                            <strong>Поделитесь нашим сайтом!</strong>
                            <p>
                                <a href="https://api.whatsapp.com/send?text=https://appp-psy.ru"
                                   target="_blank"
                                   class="whatsapp-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" style="color: #25D366;" width="48"
                                         height="48" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                        <path
                                            d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                                    </svg>
                                </a>
                                <a href="https://t.me/share/url?url=https://appp-psy.ru&text=Присоединяйтесь"
                                   target="_blank"
                                   class="telegram-button" style="margin-left: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"
                                         class="bi bi-telegram" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>
                                    </svg>
                                </a>
                                <a href="https://vk.com/share.php?url=https://appp-psy.ru" target="_blank"
                                   style="margin-left: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 48 48">
                                        <path fill="#1976d2" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                                        <path fill="#fff"
                                              d="M35.937,18.041c0.046-0.151,0.068-0.291,0.062-0.416C35.984,17.263,35.735,17,35.149,17h-2.618 c-0.661,0-0.966,0.4-1.144,0.801c0,0-1.632,3.359-3.513,5.574c-0.61,0.641-0.92,0.625-1.25,0.625C26.447,24,26,23.786,26,23.199 v-5.185C26,17.32,25.827,17,25.268,17h-4.649C20.212,17,20,17.32,20,17.641c0,0.667,0.898,0.827,1,2.696v3.623 C21,24.84,20.847,25,20.517,25c-0.89,0-2.642-3-3.815-6.932C16.448,17.294,16.194,17,15.533,17h-2.643 C12.127,17,12,17.374,12,17.774c0,0.721,0.6,4.619,3.875,9.101C18.25,30.125,21.379,32,24.149,32c1.678,0,1.85-0.427,1.85-1.094 v-2.972C26,27.133,26.183,27,26.717,27c0.381,0,1.158,0.25,2.658,2c1.73,2.018,2.044,3,3.036,3h2.618 c0.608,0,0.957-0.255,0.971-0.75c0.003-0.126-0.015-0.267-0.056-0.424c-0.194-0.576-1.084-1.984-2.194-3.326 c-0.615-0.743-1.222-1.479-1.501-1.879C32.062,25.36,31.991,25.176,32,25c0.009-0.185,0.105-0.361,0.249-0.607 C32.223,24.393,35.607,19.642,35.937,18.041z"></path>
                                    </svg>
                                </a>

                            </p>
                        </div>

                        <!--<button class="btn btn-default" style="padding-left: 15px; padding-bottom: 15px"-->
                        <!--        id="logoutModalOpen">Выйти из аккаунта-->
                        <!--</button>-->
                    <style>
                        .tabs {
                            display: flex;
                            flex-direction: column;
                        }

                        .tab-list {
                            display: flex;
                            flex-wrap: wrap;
                            list-style-type: none;
                            padding: 0;
                            margin: 0;
                        }

                        .tab-list li {
                            flex: 1 1 48%; /* Два элемента в ряд, с небольшими отступами */
                            margin: 3px; /* Отступы вокруг элементов */
                            box-sizing: border-box;
                            background: #562E74; /* Пример фона */
                            border-radius: 4px; /* Закругление углов */
                            padding: 8px; /* Внутренние отступы */
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            text-align: center;
                            font-size: 14px; /* Размер шрифта */
                            transition: background 0.3s, transform 0.3s; /* Плавный переход при наведении */
                        }

                        .tab-list li svg {
                            margin-right: 8px; /* Отступ между иконкой и текстом */
                        }

                        .tab-list li.active {
                            background: #39144e; /* Пример активного фона */
                            font-weight: bold; /* Выделение активного элемента */
                        }

                        .tab-list li:hover {
                            background: #39144e; /* Цвет фона при наведении */
                            transform: scale(1.05); /* Немного увеличиваем при наведении */
                        }

                        .tab {
                            padding: 5px 10px;
                            background: #562E74;
                            color: white;
                            cursor: pointer;
                            border-radius: 5px;
                            margin-right: 5px;
                        }

                        .tab.active {
                            background: rgba(62, 31, 77, 0.78);
                            border: 1px solid white;
                        }

                        .tab-content {
                            display: none;
                        }

                        .tab-content.active {
                            display: block;
                        }

                        .tabs-container {
                            display: flex;
                            flex-direction: column;
                            margin: 0 auto;
                            max-width: 1000px;
                        }

                        .tabs-header {
                            display: flex;
                            overflow-x: auto; /* Добавляем горизонтальную прокрутку */
                            border-bottom: 1px solid #ddd;
                            white-space: nowrap; /* Предотвращает перенос кнопок на новую строку */
                        }

                        .tab-link {
                            background: #562E74;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            cursor: pointer;
                            text-align: center;
                            border-radius: 5px;
                            font-size: 16px;
                            display: inline-block; /* Изменяем способ отображения кнопок */
                            margin-right: 5px; /* Отступ между кнопками */
                        }

                        .tab-link.active {
                            background: #3e1f4d;
                        }

                        .tabs-content {
                            padding: 20px;
                        }

                        .tab-content {
                            display: none;
                        }

                        .tab-content.active {
                            display: block;
                        }
.marginResize{
    margin-left:200px;
}
                        @media (max-width: 768px) {
                            .tab-link {
                                font-size: 14px;
                                padding: 8px 12px;
                                margin-right: 3px; /* Уменьшаем отступы между кнопками */
                            }
                            .marginResize{
                                margin-left:0px;
                            }
                        }

                    </style>

                    <script>
                        document.querySelectorAll('.tab').forEach(function (tab) {
                            tab.addEventListener('click', function () {
                                var activeTab = document.querySelector('.tab.active');
                                var activeContent = document.querySelector('.tab-content.active');

                                activeTab.classList.remove('active');
                                activeContent.classList.remove('active');

                                this.classList.add('active');
                                var tabId = this.getAttribute('data-tab');
                                document.getElementById(tabId).classList.add('active');
                            });
                        });
                    </script>


                    <style>
                        .modern-card {
                            background-color: #f9f9f9;
                            border-radius: 12px;
                            padding: 20px;
                            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                            transition: transform 0.3s ease;
                        }

                        .modern-title {
                            font-family: 'Montserrat', sans-serif;
                            font-size: 24px;
                            font-weight: 600;
                            color: #333;
                            margin-bottom: 20px;
                        }

                        .modern-btn {
                            background-color: #562E74;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            margin-bottom: 10px;
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                            border-radius: 10px;
                            font-family: 'Montserrat', sans-serif;
                            font-weight: 500;
                            font-size: 16px;
                            transition: background-color 0.3s ease, box-shadow 0.3s ease;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            width: 100%;
                            text-align: left;
                        }

                        .modern-btn:hover {
                            background-color: #3e1f4d;
                            color: white;
                        }

                        #video-buttons-container {
                            display: flex;
                            flex-direction: column;
                            gap: 10px;
                        }

                        .hidden-btn {
                            display: none;
                        }

                        #show-more-btn {
                            background-color: #562E74;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            cursor: pointer;
                            border-radius: 10px;
                            font-family: 'Montserrat', sans-serif;
                            font-weight: 500;
                            transition: background-color 0.3s ease, box-shadow 0.3s ease;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            display: block;
                        }

                        #show-more-btn:hover {
                            background-color: #3e1f4d;
                        }
                    </style>

                    <script>
                        //   document.getElementById('show-more-btn').addEventListener('click', function () {
                        $('show-more-btn').on('click', function () {
                            var hiddenButtons = document.querySelectorAll('.hidden-btn');
                            var isHidden = hiddenButtons[0].style.display === 'none' || hiddenButtons[0].style.display === '';

                            hiddenButtons.forEach(function (btn) {
                                btn.style.display = isHidden ? 'flex' : 'none'; // Переключаем видимость
                            });

                            // Меняем текст кнопки в зависимости от состояния
                            this.innerHTML = isHidden ?
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
  <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
  <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
  <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
</svg> Свернуть` :
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg> Смотреть все`;
                        });

                    </script>


                    @if(auth()->user() && auth()->user()->group == "admin")
                        <div style="margin-top: 200px" class="modal fade" id="editRole" tabindex="-1" role="dialog"
                             aria-labelledby="editRoleLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editRoleLabel">Изменение роли </h5>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="/user/edit/role" id="donationForm">
                                            @csrf
                                            <div id="hidEl">

                                            </div>
                                            <div class="form-group">
                                                <label for="roles" class="form-label">Выбрать роль</label>
                                                <select name="selected_role" class="form-select" id="roles">
                                                    @if(!empty($roles))
                                                        @foreach($roles as $role)
                                                            <option value="{{$role->slug}}">{{$role->name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <br>
                                            <button type="submit" class="btn btn-primary" id="registerButton">
                                                Сохранить
                                            </button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary closeModal"
                                                style="background-color: #613482" data-dismiss="modal">Закрыть
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 200px" class="modal fade" id="editPermissions" tabindex="-1"
                             role="dialog" aria-labelledby="editPermissionsLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPermissionsLabel">Изменение доступов </h5>
                                    </div>
                                    <div class="modal-body">
                                        <form id="permsForm" method="post" action="/user/edit/perms">
                                            @csrf
                                            <div id="hidEl">
                                            </div>
                                            <div class="form-group">
                                                <div class="mb-6 perm-checks"></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="background-color: #613482"
                                                class="btn btn-secondary closeModal">Закрыть
                                        </button>
                                        <button type="button" class="btn btn-primary" id="getCheckedValues">Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 200px" class="modal fade" id="addAdminModal" tabindex="-1" role="dialog"
                             aria-labelledby="addAdminLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addAdminLabel">Добавить админинстратора </h5>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addAdminForm" method="post" action="/user/admin">
                                            @csrf
                                            <div id="hidEl">
                                            </div>
                                            <div class="form-group">
                                                <div class="mb-6">
                                                    <label for="tel_num">Номер телефона пользователя</label><br>
                                                    <input type="tel" name="tel_num" id="tel_num" class="form-control"
                                                           placeholder="+79158625892" value="">
                                                </div>
                                            </div>
                                            <br>
                                            <button type="submit" class="btn btn-primary">Сохранить</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="background-color: #613482"
                                                class="btn btn-secondary closeModal">Закрыть
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 200px" class="modal fade" id="editProduct" tabindex="-1" role="dialog"
                             aria-labelledby="editProductLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editProductLabel">Редактировать подписку</h5>
                                    </div>
                                    @csrf
                                    <div class="modal-body" id="editProductForm">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="background-color: #613482"
                                                class="btn btn-secondary closeModal">Закрыть
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="margin-top: 200px" class="modal fade" id="createProduct" tabindex="-1" role="dialog"
                             aria-labelledby="createProductLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createProductLabel">Добавить подписку</h5>
                                    </div>
                                    @csrf
                                    <div class="modal-body" id="createProductForm">
                                        <form method='post' action='/product/create'>
                                            @csrf
                                            <div id='hidEl'>
                                            </div>
                                            <div class='form-group'>
                                                <div class='mb-6'>
                                                    <label for='name'>Имя подписки</label><br>
                                                    <input type='text' name='name' id='name' class='form-control'
                                                           placeholder='Имя подписки' value=''>
                                                    <label for='price'>Цена</label><br>
                                                    <input type='number' name='price' id='price' class='form-control'
                                                           placeholder='Цена' value=''>
                                                    <label for='first_week_price'>Стоимость за первую неделю</label><br>
                                                    <input type='number' name='first_week_price' id='first_week_price'
                                                           class='form-control' placeholder='Стоимость за первую неделю'
                                                           value=''>
                                                    <label for='level'>Уровень</label><br>
                                                    <input type='text' name='level' id='level' class='form-control'
                                                           placeholder='Уровень' value=''>
                                                    <label for='descr'>Описание</label><br>
                                                    <textarea type='text' name='descr' id='descr' class='form-control'
                                                              placeholder='Описание'></textarea>
                                                </div>
                                            </div>
                                            <br>
                                            <button type='submit' class='btn btn-primary'>Сохранить</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" style="background-color: #613482"
                                                class="btn btn-secondary closeModal">Закрыть
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            /* Уникальные стили для конкретной карточки и таблицы */
                            .user-management-card {
                                border-radius: 15px;
                                overflow: hidden;
                                border: none;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                transition: box-shadow 0.3s ease, transform 0.3s ease;
                                margin-bottom: 1rem;
                            }

                            .user-management-card:hover {
                                transform: translateY(-5px);
                                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                            }

                            .user-management-card .card-title h3 {
                                font-size: 1.5rem;
                                font-weight: 600;
                                color: #613482;
                                margin: 1rem;
                            }

                            .user-management-card .card-body {
                                padding: 1rem;
                            }

                            .user-management-card .btn-dark {
                                background-color: #613482;
                                border: none;
                                transition: background-color 0.3s ease;
                            }

                            .user-management-card .btn-dark:hover {
                                background-color: #4b286d;
                            }

                            .user-management-card .table {
                                border-collapse: separate;
                                border-spacing: 0;
                            }

                            .user-management-card .table thead th {
                                background-color: #f8f9fa;
                                color: #613482;
                                font-weight: 600;
                                border-bottom: 2px solid #e9ecef;
                            }

                            .user-management-card .table tbody tr:nth-child(odd) {
                                background-color: #f9f9f9;
                            }

                            .user-management-card .table tbody tr:nth-child(even) {
                                background-color: #ffffff;
                            }

                            .user-management-card .table tbody tr:hover {
                                background-color: #f1f1f1;
                            }

                            .user-management-card .table td, .user-management-card .table th {
                                vertical-align: middle;
                                padding: 1rem;
                                border: 1px solid #dee2e6;
                            }

                            .user-management-card .alert-warning {
                                border-radius: 0;
                                margin: 0;
                                padding: 0.5rem 1rem;
                            }

                            .user-management-card .btn-primary, .user-management-card .btn-success {
                                transition: background-color 0.3s ease;
                            }

                            .user-management-card .btn-primary:hover {
                                background-color: #0056b3;
                            }

                            .user-management-card .btn-success:hover {
                                background-color: #007c1b;
                            }
                        </style>

                        <style>
                            /* Уникальные стили для конкретной карточки и таблицы */
                            .subscriptions-card {
                                border-radius: 10px;
                                overflow: hidden;
                                border: none;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                transition: box-shadow 0.3s ease, transform 0.3s ease;
                                margin-bottom: 1rem;
                            }

                            .subscriptions-card:hover {
                                transform: translateY(-5px);
                                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
                            }

                            .subscriptions-card .card-title h3 {
                                font-size: 1.5rem;
                                font-weight: 600;
                                color: #613482;
                                margin: 1rem;
                            }

                            .subscriptions-card .card-body {
                                padding: 1rem;
                            }

                            .subscriptions-card .btn-success {
                                background-color: #28a745;
                                border: none;
                                transition: background-color 0.3s ease;
                            }

                            .subscriptions-card .btn-success:hover {
                                background-color: #218838;
                            }

                            .subscriptions-card .table {
                                border-collapse: separate;
                                border-spacing: 0;
                            }

                            .subscriptions-card .table thead th {
                                background-color: #f8f9fa;
                                color: #613482;
                                font-weight: 600;
                                border-bottom: 2px solid #e9ecef;
                            }

                            .subscriptions-card .table tbody tr:nth-child(odd) {
                                background-color: #f9f9f9;
                            }

                            .subscriptions-card .table tbody tr:nth-child(even) {
                                background-color: #ffffff;
                            }

                            .subscriptions-card .table tbody tr:hover {
                                background-color: #f1f1f1;
                            }

                            .subscriptions-card .table td, .subscriptions-card .table th {
                                vertical-align: middle;
                                padding: 1rem;
                                border: 1px solid #dee2e6;
                            }

                            .subscriptions-card .alert-warning {
                                border-radius: 0;
                                margin: 0;
                                padding: 0.5rem 1rem;
                            }

                            .subscriptions-card .btn-success svg {
                                vertical-align: middle;
                                margin-left: 0.5rem;
                            }
                        </style>
                        
                        <!--    <div style="margin:20px">-->
                        <!--    <strong>Поделитесь нашим сайтом!</strong>-->
                        <!--    <p>-->
                        <!--        <a href="https://api.whatsapp.com/send?text=https://appp-psy.ru"-->
                        <!--           target="_blank"-->
                        <!--           class="whatsapp-button">-->
                        <!--            <svg xmlns="http://www.w3.org/2000/svg" style="color: #25D366;" width="48"-->
                        <!--                 height="48" fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">-->
                        <!--                <path-->
                        <!--                    d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>-->
                        <!--            </svg>-->
                        <!--        </a>-->
                        <!--        <a href="https://t.me/share/url?url=https://appp-psy.ru&text=Присоединяйтесь"-->
                        <!--           target="_blank"-->
                        <!--           class="telegram-button" style="margin-left: 10px;">-->
                        <!--            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor"-->
                        <!--                 class="bi bi-telegram" viewBox="0 0 16 16">-->
                        <!--                <path-->
                        <!--                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09"/>-->
                        <!--            </svg>-->
                        <!--        </a>-->
                        <!--        <a href="https://vk.com/share.php?url=https://appp-psy.ru" target="_blank"-->
                        <!--           style="margin-left: 10px;">-->
                        <!--            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 48 48">-->
                        <!--                <path fill="#1976d2" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>-->
                        <!--                <path fill="#fff"-->
                        <!--                      d="M35.937,18.041c0.046-0.151,0.068-0.291,0.062-0.416C35.984,17.263,35.735,17,35.149,17h-2.618 c-0.661,0-0.966,0.4-1.144,0.801c0,0-1.632,3.359-3.513,5.574c-0.61,0.641-0.92,0.625-1.25,0.625C26.447,24,26,23.786,26,23.199 v-5.185C26,17.32,25.827,17,25.268,17h-4.649C20.212,17,20,17.32,20,17.641c0,0.667,0.898,0.827,1,2.696v3.623 C21,24.84,20.847,25,20.517,25c-0.89,0-2.642-3-3.815-6.932C16.448,17.294,16.194,17,15.533,17h-2.643 C12.127,17,12,17.374,12,17.774c0,0.721,0.6,4.619,3.875,9.101C18.25,30.125,21.379,32,24.149,32c1.678,0,1.85-0.427,1.85-1.094 v-2.972C26,27.133,26.183,27,26.717,27c0.381,0,1.158,0.25,2.658,2c1.73,2.018,2.044,3,3.036,3h2.618 c0.608,0,0.957-0.255,0.971-0.75c0.003-0.126-0.015-0.267-0.056-0.424c-0.194-0.576-1.084-1.984-2.194-3.326 c-0.615-0.743-1.222-1.479-1.501-1.879C32.062,25.36,31.991,25.176,32,25c0.009-0.185,0.105-0.361,0.249-0.607 C32.223,24.393,35.607,19.642,35.937,18.041z"></path>-->
                        <!--            </svg>-->
                        <!--        </a>-->

                        <!--    </p>-->
                        <!--</div>-->

                        <!--<button class="btn btn-default" style="padding-left: 15px; padding-bottom: 15px"-->
                        <!--        id="logoutModalOpen">Выйти из аккаунта-->
                        <!--</button>-->

                        <div class="modal fade logoutModal" id="logoutModal" tabindex="-1"
                             aria-labelledby="logoutModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="logoutModalLabel">Подтверждение выхода</h5>
                                    </div>
                                    <div class="modal-body">
                                        Вы уверены, что хотите выйти из профиля?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary cancel-logout"
                                                onclick="window.location.reload()">Отмена
                                        </button>
                                        <button type="button" class="btn btn-primary"
                                                onclick="window.location.href='/logout'">Выйти
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function () {

                                $("#logoutModalOpen").on("click", function () {
                                    $("#logoutModal").modal("show")
                                })

                                $("#cancel-logout").on("click", function () {
                                    $("#logoutModal").modal("hide")
                                })

                            })
                        </script>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        
        $(document).on('click', '#authConfirmPayment', function () {

    $(this).prop('disabled', true).text('Обработка...');
});
        
           document.addEventListener("DOMContentLoaded", function () {
               
               
        const upgradeForm = document.querySelector('form[action="/changeToHigher"]');
        if (upgradeForm) {
            upgradeForm.addEventListener("submit", function (e) {
                const button = upgradeForm.querySelector("button[type=submit]");
                button.disabled = true;
                button.innerText = "Переход...";
            });
        }
    });
        
        window.onload = function () {
            let myLink = document.querySelector('.formFL a');
            if (myLink) {
                myLink.textContent = 'Новый текст ссылки';
            }
        };


    </script>
    <script>
        $('#getCheckedValues').on("click", function () {
            var checkedValues = {}
            var userId = $("#user_id").val();

            $(".perm-checks").find('input[type=checkbox]').each(function () {
                var isChecked = $(this).is(':checked');
                var keyI = $(this).data("key");
                checkedValues[keyI] = isChecked;
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            debugger;
            checkedValues["userId"] = userId;
            let stringify = JSON.stringify({checkedValues});
            window.location.href = '/user/edit/perms/' + stringify;
            /*$.ajax({
                url: '/user/edit/perms', // URL вашего маршрута
                type: 'POST',
                //contentType: 'application/json',
                data: {
                    settings: checkedValues
                },
                success: function(response) {
                    debugger;
                    window.location.reload(); // Обработка успешного ответа
                },
                error: function(xhr) {
                    alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                }
            });*/

        });

        // Обработчик события показа модального окна
        const exampleModal = document.getElementById('editRole');
        $(".editRoleBtn").on('click', function (event) {
            debugger;
            $("#editRole").modal("show");
            // Извлечение информации из атрибута data-bs-whatever
            const recipient = $(this).data('user-id');
            // Обновление содержимого модального окна
            $("#editRole").find("#hidEl").html("<input type='hidden' name='user_id' value=" + recipient + ">");
        });

        $("#closeModal").on("click", function () {
            $("#editRole").modal("hide");
        })

        $(".editPerms").on('click', function (event) {
            debugger;
            $("#editPermissions").modal("show");
            // Извлечение информации из атрибута data-bs-whatever
            const recipient = $(this).data('user-id');
            // Обновление содержимого модального окна
            $("#editPermissions").find("#hidEl").html("<input type='hidden' id='user_id' name='user_id' value=" + recipient + ">");
            var userId = $("#user_id").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/user/get/perms', // URL вашего маршрута
                type: 'POST',
                data: {
                    userId: userId
                },
                success: function (response) {
                    $(".perm-checks").html(response); // Обработка успешного ответа
                },
                error: function (xhr) {
                    alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                }
            });
        });

        $(".closeModal").on("click", function () {
            debugger;
            $("#editRole").modal("hide");
            $("#editPermissions").modal("hide");
            $("#addAdminModal").modal("hide");
            $("#editProduct").modal("hide");
            $("#createProduct").modal("hide");

        })

        $(".editProductBtn").on("click", function () {
            $("#editProduct").modal("show");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            debugger;
            $.ajax({
                url: '/product/get', // URL вашего маршрута
                type: 'POST',
                data: {
                    productId: $(this).data("product-id")
                },
                success: function (response) {
                    $("#editProductForm").html(response); // Обработка успешного ответа
                },
                error: function (xhr) {
                    alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                }
            });
        })
        $("#addProduct").on("click", function () {
            $("#createProduct").modal("show");

        })
        $("#addAdmin").on("click", function () {
            $("#addAdminModal").modal("show");
        })

        function removeAvatar(event) {
            $.ajax({
                url: '{{ route('removeAvatar') }}',  // Laravel route to delete avatar
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    window.location.reload()
                },
                error: function (xhr) {
                    window.location.reload()
                }
            })
        }
    </script>
@endsection
