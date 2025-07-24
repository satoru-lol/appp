@extends('app', [
    'title' => 'Вступление в ассоциацию',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
    <div class="comment_block1">
        <div class="container">
            <div class="title">
                <h2>Укажите свои контактные данные</h2>
            </div>
            <div class="form_group">
                <form id="contactForm" action="{{ route('introductionSend') }}" method="POST">
                    @csrf

                    <!-- Предупреждение -->
                    <div id="warning" class="alert alert-warning">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="warning-icon">
                                <path fill="#f8bb86" d="M1 21h22L12 2 1 21zM12 16v2h-2v-2h2zm0-6v4h-2v-4h2z"/>
                            </svg>
                        </div>
                        <div class="message">
                            <span style="font-size: 12px">Пожалуйста, внимательно проверьте введенные данные и еще раз нажмите на кнопку <b>отправить</b>.</span>
                        </div>
                    </div><br>

                    <div class="inputs">
                        <div class="input-container">
                            <label for="firstname" class="form-label">Ваше имя</label>
                            <input type="text" name="firstname" id="firstname" value="{{ old('firstname') }}" placeholder="Ваше имя" required class="input-name">
                            <div class="tooltip">Имя должно содержать не больше 15 символов</div>
                        </div>
                        <div class="input-container">
                            <label for="lastname" class="form-label">Ваша фамилия</label>
                            <input type="text" name="lastname" id="lastname" value="{{ old('lastname') }}" placeholder="Ваша фамилия" required class="input-lastname">
                            <div class="tooltip">Это поле обязательно</div>
                        </div>
                    </div>

                    <div class="inputs">
                        <div class="input-container">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}" required class="tooltip-input">
                            <div class="tooltip">Внимательно проверьте правильность почты</div>
                        </div>
                        <div class="input_mask">
                            <div class="input-container">
                                <label for="mobile" class="form-label">Телефон</label>
                                <input type="tel" id="mobile" name="phone" value="{{ old('phone') }}" class="input-phone">
                                <div class="tooltip">Введите корректный номер телефона</div>
                            </div>
                        </div>
                    </div>
                    <div id="error-message" class="error-message" style="display: none;">
                        Некоторые поля обязательны для заполнения.
                    </div>

                    <script>
                        $(document).ready(function() {
                            $('#contactForm').on('submit', function(event) {
                                var isValid = true;
                                $('.input-container input').each(function() {
                                    if ($(this).prop('required') && $(this).val().trim() === '') {
                                        isValid = false;
                                    }
                                });

                                if (!isValid) {
                                    event.preventDefault(); // Предотвратить отправку формы
                                    $('#error-message').show(); // Показать сообщение об ошибке
                                } else {
                                    $('#error-message').hide(); // Скрыть сообщение об ошибке, если все поля заполнены
                                }
                            });
                        });

                    </script>
                    <style>
                        .form-label .required-asterisk {
                            color: red;
                            margin-left: 0.25rem;
                            font-weight: bold;
                        }

                        .error-message {
                            color: red;
                            font-size: 1rem;
                            margin-top: 1rem;
                            font-weight: bold;
                        }

                        .input-container {
                            display: flex;
                            flex-direction: column; /* Убедитесь, что элементы идут вертикально */
                            margin-bottom: 1rem;
                        }

                        .form-label {
                            position: relative;
                            margin-bottom: 0.5rem;
                            font-weight: bold;
                        }

                        .form-label::after {
                            content: "*";
                            color: red;
                            margin-left: 0.25rem;
                            font-weight: bold;
                        }

                        .input-name, .input-lastname, .tooltip-input, .input-phone {
                            padding: 0.5rem;
                            font-size: 1rem;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                        }

                        .tooltip {
                            margin-top: 0.5rem;
                            color: #666;
                            font-size: 0.875rem;
                        }
                    </style>

                    <!-- Ошибки валидации и сообщения -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-300">{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @session('success')
                    <div class="alert alert-success mb-0">
                        {{ $value }}
                    </div>
                    @endsession

                    @session('error')
                    <div class="alert alert-danger mb-0">
                        {{ $value }}
                    </div>
                    @endsession

                    <button type="submit" class="submit-btn">Отправить</button>
                </form>
            </div>
        </div>

        <style>
            #warning {
                padding: 20px;
                margin-bottom: 10px; /* Уменьшили отступ снизу */
                border-radius: 10px;
                display: flex;
                align-items: center;
                background-color: #fff4e5;
                border: 1px solid #ffecb5;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                opacity: 0;
                transform: translateY(-20px);
                transition: opacity 0.5s ease, transform 0.5s ease;
            }

            #warning.show {
                opacity: 1;
                transform: translateY(0);
            }

            #warning .icon {
                margin-right: 15px;
            }

            #warning .warning-icon {
                width: 30px;
                height: 30px;
                fill: #f8bb86;
            }

            #warning .message {
                flex: 1;
                color: #856404;
                font-weight: 600;
                font-size: 16px;
            }

            .submit-btn {
                background-color: #007bff;
                color: #fff;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s, transform 0.2s;
                margin-top: 10px; /* Уменьшили отступ сверху */
            }

            .submit-btn:hover {
                background-color: #0056b3;
                transform: translateY(-2px);
            }

            .inputs {
                margin-bottom: 20px;
            }

            .input-container {
                position: relative;
                margin-bottom: 10px; /* Уменьшили отступ снизу для контейнера инпутов */
            }

            input[type="text"], input[type="email"], input[type="tel"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-sizing: border-box;
                font-size: 16px;
            }

            .tooltip {
                position: absolute;
                bottom: 120%;
                left: 0;
                background-color: #333;
                color: #fff;
                padding: 8px;
                border-radius: 4px;
                font-size: 12px;
                white-space: nowrap;
                opacity: 0;
                transform: translateY(10px);
                transition: opacity 0.3s ease, transform 0.3s ease;
                pointer-events: none;
                z-index: 1000;
                width: 100%;
                text-align: center;
                visibility: hidden;
            }

            .input-container:hover .tooltip,
            .input-container:focus-within .tooltip {
                opacity: 1;
                transform: translateY(0);
                visibility: visible;
            }

            @media (max-width: 768px) {
                .submit-btn {
                    width: 100%;
                    padding: 10px 0;
                    font-size: 14px;
                }

                .inputs, .input-container {
                    margin-bottom: 15px;
                }

                .tooltip {
                    font-size: 10px;
                    bottom: 100%;
                }

                #warning {
                    padding: 15px;
                    margin-bottom: 5px; /* Уменьшили отступ снизу */
                }
            }

            @media (max-width: 480px) {
                .submit-btn {
                    font-size: 12px;
                }

                input[type="text"], input[type="email"], input[type="tel"] {
                    font-size: 14px;
                }

                .tooltip {
                    font-size: 9px;
                    padding: 6px;
                }

                #warning {
                    padding: 10px;
                    margin-bottom: 0; /* Убрали отступ снизу */
                }
            }
        </style>

        <script>
            let formSubmitted = false;

            document.getElementById('contactForm').addEventListener('submit', function(e) {
                if (!formSubmitted) {
                    e.preventDefault();
                    const warning = document.getElementById('warning');
                    warning.classList.add('show'); // Запускаем анимацию
                    formSubmitted = true; // Устанавливаем флаг для следующего отправления
                }
            });
        </script>

    </div>
@endsection
