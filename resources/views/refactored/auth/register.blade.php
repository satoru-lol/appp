@extends('app')

@section('title', $title ?? 'Регистрация - АЧПП')
@section('description', $description ?? 'Зарегистрируйтесь в Ассоциации частнопрактикующих психологов и психотерапевтов')

@section('content')
<style>
    .register-v2-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 150px);
        background-color: #f4f7f6;
        padding: 2rem 1rem;
    }
    .register-v2-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        border: 1px solid #e0e0e0;
        width: 100%;
        max-width: 500px;
        overflow: hidden;
    }
    .register-v2-header {
        text-align: center;
        padding: 2rem 2rem 1.5rem;
    }
    .register-v2-header img {
        height: 50px;
        margin-bottom: 1rem;
    }
    .register-v2-header h2 {
        margin: 0;
        font-size: 1.75rem;
        color: #333;
    }
    .register-v2-header p {
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .register-v2-body {
        padding: 0 2rem 2rem;
    }
    .input-group-v2 {
        position: relative;
    }
    .input-group-v2 .form-control-v2 {
        padding-left: 2.75rem;
    }
    .input-group-v2 .input-icon {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .form-control-v2 {
        height: 50px;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        padding: 0 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control-v2:focus {
        border-color: #613482;
        box-shadow: 0 0 0 0.25rem rgba(97, 52, 130, 0.25);
    }
    .form-control-v2.is-valid {
        border-color: #28a745;
    }
    .form-control-v2.is-invalid {
        border-color: #dc3545;
    }
    .btn-v2-primary {
        background-color: #613482;
        color: #fff;
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
        width: 100%;
        font-size: 1rem;
    }
    .btn-v2-primary:hover {
        background-color: #4a276b;
    }
    .register-v2-footer {
        text-align: center;
        margin-top: 1.5rem;
    }
    .register-v2-footer a {
        color: #613482;
        text-decoration: none;
        font-weight: 500;
    }
    .register-v2-footer a:hover {
        text-decoration: underline;
    }
    .form-check-v2 {
        margin-bottom: 1rem;
    }
    .form-check-v2 input[type="checkbox"] {
        margin-right: 0.5rem;
    }
    .form-check-v2 label {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .form-check-v2 label a {
        color: #613482;
    }
    .validation-feedback {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    .valid-feedback {
        color: #28a745;
    }
    .invalid-feedback {
        color: #dc3545;
    }
    .row {
        margin: 0 -0.5rem;
    }
    .col-6 {
        padding: 0 0.5rem;
        flex: 0 0 50%;
        max-width: 50%;
    }
</style>

<div class="register-v2-wrapper">
    <div class="register-v2-card">
        <div class="register-v2-header">
            <img src="/img/logo.svg" alt="Логотип АЧПП">
            <h2>Регистрация</h2>
            <p>{{ $description }}</p>
        </div>
        <div class="register-v2-body">
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('v2.refactored.auth.register.submit') }}" method="POST" novalidate id="registerForm">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="firstname" class="form-label visually-hidden">Имя</label>
                        <div class="input-group-v2">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="firstname" id="firstname" class="form-control form-control-v2" 
                                   placeholder="Имя" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="validation-feedback" id="firstname-feedback"></div>
                    </div>
                    <div class="col-6">
                        <label for="lastname" class="form-label visually-hidden">Фамилия</label>
                        <div class="input-group-v2">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" name="lastname" id="lastname" class="form-control form-control-v2" 
                                   placeholder="Фамилия" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="validation-feedback" id="lastname-feedback"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label visually-hidden">Email</label>
                    <div class="input-group-v2">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="form-control form-control-v2" 
                               placeholder="Ваш Email" value="{{ old('email') }}" required>
                    </div>
                    <div class="validation-feedback" id="email-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label visually-hidden">Телефон</label>
                    <div class="input-group-v2">
                        <i class="bi bi-telephone input-icon"></i>
                        <input type="tel" name="phone" id="phone" class="form-control form-control-v2" 
                               placeholder="Телефон (необязательно)" value="{{ old('phone') }}">
                    </div>
                    <div class="validation-feedback" id="phone-feedback"></div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label visually-hidden">Пароль</label>
                    <div class="input-group-v2">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control form-control-v2" 
                               placeholder="Пароль (минимум 6 символов)" required>
                    </div>
                    <div class="validation-feedback" id="password-feedback"></div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label visually-hidden">Подтверждение пароля</label>
                    <div class="input-group-v2">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-v2" 
                               placeholder="Подтвердите пароль" required>
                    </div>
                    <div class="validation-feedback" id="password-confirmation-feedback"></div>
                </div>

                <div class="form-check-v2">
                    <input type="checkbox" name="terms" id="terms" value="1" required>
                    <label for="terms" class="form-label">
                        Я согласен с <a href="/terms" target="_blank">условиями использования</a> 
                        и <a href="/privacy" target="_blank">политикой конфиденциальности</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-v2-primary" id="registerBtn">
                    <span class="btn-text">Зарегистрироваться</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>

                <div class="register-v2-footer">
                    <p class="mb-0">
                        Уже есть аккаунт? 
                        <a href="{{ route('v2.refactored.auth.login') }}">Войти</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const btn = document.getElementById('registerBtn');
    const btnText = btn.querySelector('.btn-text');
    const spinner = btn.querySelector('.spinner-border');

    // Валидация в реальном времени
    const inputs = {
        firstname: document.getElementById('firstname'),
        lastname: document.getElementById('lastname'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        password: document.getElementById('password'),
        password_confirmation: document.getElementById('password_confirmation')
    };

    // Валидация имени и фамилии
    [inputs.firstname, inputs.lastname].forEach(input => {
        input.addEventListener('blur', function() {
            const value = this.value.trim();
            const feedback = document.getElementById(this.id + '-feedback');
            
            if (value.length < 2) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                feedback.textContent = 'Минимум 2 символа';
                feedback.className = 'validation-feedback invalid-feedback';
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                feedback.textContent = '';
            }
        });
    });

    // AJAX проверка email
    inputs.email.addEventListener('blur', function() {
        const email = this.value.trim();
        const feedback = document.getElementById('email-feedback');
        
        if (!email.includes('@') || email.length < 5) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            feedback.textContent = 'Введите корректный email';
            feedback.className = 'validation-feedback invalid-feedback';
            return;
        }

        // AJAX проверка доступности email
        fetch('{{ route("v2.refactored.auth.check-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.available) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
                feedback.textContent = 'Email доступен';
                feedback.className = 'validation-feedback valid-feedback';
            } else {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                feedback.textContent = 'Email уже занят';
                feedback.className = 'validation-feedback invalid-feedback';
            }
        })
        .catch(() => {
            // Если AJAX не работает, просто убираем валидацию
            this.classList.remove('is-valid', 'is-invalid');
            feedback.textContent = '';
        });
    });

    // Валидация пароля
    inputs.password.addEventListener('input', function() {
        const password = this.value;
        const feedback = document.getElementById('password-feedback');
        
        if (password.length < 6) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
            feedback.textContent = 'Минимум 6 символов';
            feedback.className = 'validation-feedback invalid-feedback';
        } else {
            this.classList.add('is-valid');
            this.classList.remove('is-invalid');
            feedback.textContent = 'Пароль подходит';
            feedback.className = 'validation-feedback valid-feedback';
        }

        // Проверяем подтверждение пароля
        if (inputs.password_confirmation.value) {
            validatePasswordConfirmation();
        }
    });

    // Валидация подтверждения пароля
    function validatePasswordConfirmation() {
        const password = inputs.password.value;
        const confirmation = inputs.password_confirmation.value;
        const feedback = document.getElementById('password-confirmation-feedback');
        
        if (confirmation !== password) {
            inputs.password_confirmation.classList.add('is-invalid');
            inputs.password_confirmation.classList.remove('is-valid');
            feedback.textContent = 'Пароли не совпадают';
            feedback.className = 'validation-feedback invalid-feedback';
        } else if (confirmation.length >= 6) {
            inputs.password_confirmation.classList.add('is-valid');
            inputs.password_confirmation.classList.remove('is-invalid');
            feedback.textContent = 'Пароли совпадают';
            feedback.className = 'validation-feedback valid-feedback';
        }
    }

    inputs.password_confirmation.addEventListener('input', validatePasswordConfirmation);

    // Обработка отправки формы
    form.addEventListener('submit', function(e) {
        btnText.textContent = 'Регистрация...';
        spinner.classList.remove('d-none');
        btn.disabled = true;
    });
});
</script>
@endsection