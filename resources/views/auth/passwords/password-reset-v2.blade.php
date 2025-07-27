@extends('app')

@section('content')
<style>
    .password-reset-v2-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 150px);
        background-color: #f4f7f6;
        padding: 2rem 1rem;
    }
    .password-reset-v2-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        border: 1px solid #e0e0e0;
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }
    .password-reset-v2-header {
        text-align: center;
        padding: 2rem 2rem 1.5rem;
    }
    .password-reset-v2-header img {
        height: 50px;
        margin-bottom: 1rem;
    }
    .password-reset-v2-header h2 {
        margin: 0;
        font-size: 1.75rem;
        color: #333;
    }
    .password-reset-v2-header p {
        color: #6c757d;
        margin-top: 0.5rem;
    }
    .password-reset-v2-body {
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
</style>

<div class="password-reset-v2-wrapper">
    <div class="password-reset-v2-card">
        <div class="password-reset-v2-header">
            <img src="/img/logo.svg" alt="Логотип АЧПП">
            <h2>Создайте новый пароль</h2>
            <p>Ваш новый пароль должен отличаться от предыдущего</p>
        </div>
        <div class="password-reset-v2-body">

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label visually-hidden">Email</label>
                    <div class="input-group-v2">
                        <i class="bi bi-envelope input-icon"></i>
                        <input id="email" type="email" class="form-control form-control-v2" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" placeholder="Ваш Email" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label visually-hidden">Новый пароль</label>
                    <div class="input-group-v2">
                        <i class="bi bi-lock input-icon"></i>
                        <input id="password" type="password" class="form-control form-control-v2" name="password" required autocomplete="new-password" placeholder="Новый пароль">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label visually-hidden">Подтвердите пароль</label>
                    <div class="input-group-v2">
                        <i class="bi bi-lock input-icon"></i>
                        <input id="password-confirm" type="password" class="form-control form-control-v2" name="password_confirmation" required autocomplete="new-password" placeholder="Подтвердите новый пароль">
                    </div>
                </div>

                <button type="submit" class="btn btn-v2-primary">
                    Сохранить новый пароль
                </button>
            </form>
        </div>
    </div>
</div>
@endsection 