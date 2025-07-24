@extends('app')

@section('content')
<style>
    .login-v2-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 150px); /* Adjusted for header */
        background-color: #f4f7f6;
        padding: 2rem 1rem;
    }
    .login-v2-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        border: 1px solid #e0e0e0;
        width: 100%;
        max-width: 450px;
        overflow: hidden;
    }
    .login-v2-header {
        text-align: center;
        padding: 2rem 2rem 1.5rem;
    }
    .login-v2-header img {
        height: 50px;
        margin-bottom: 1rem;
    }
    .login-v2-header h2 {
        margin: 0;
        font-size: 1.75rem;
        color: #333;
    }
     .login-v2-header p {
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .login-v2-body {
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
    .login-v2-footer {
        text-align: center;
        margin-top: 1.5rem;
    }
    .login-v2-footer a {
        color: #613482;
        text-decoration: none;
        font-weight: 500;
    }
    .login-v2-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-v2-wrapper">
    <div class="login-v2-card">
        <div class="login-v2-header">
            <img src="/img/logo.svg" alt="Логотип АЧПП">
            <h2>Вход в аккаунт</h2>
            <p>Для доступа к вашему личному кабинету</p>
        </div>
        <div class="login-v2-body">
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

            <form action="{{ route('Auth') }}" method="POST" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label visually-hidden">Email</label>
                    <div class="input-group-v2">
                         <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="email" class="form-control form-control-v2" placeholder="Ваш Email" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label visually-hidden">Пароль</label>
                     <div class="input-group-v2">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control form-control-v2" placeholder="Ваш пароль" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-v2-primary">Войти</button>

                <div class="login-v2-footer">
                    <p class="mb-1"><a href="/password/reset">Забыли пароль?</a></p>
                    <p class="mb-0">Нет аккаунта? <a href="{{ route('introduction') }}">Зарегистрироваться</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 