@extends('app')

@section('title', $title ?? 'Контакты - АЧПП')
@section('description', $description ?? 'Свяжитесь с нами - контактная информация АЧПП')

@section('content')
<style>
    .contacts-hero {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
    }
    .contact-card {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        text-align: center;
        height: 100%;
        transition: transform 0.3s ease;
    }
    .contact-card:hover {
        transform: translateY(-5px);
    }
    .contact-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
    }
    .contact-form {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
    }
</style>

<section class="contacts-hero">
    <div class="container">
        <h1 class="display-4 mb-4">Свяжитесь с нами</h1>
        <p class="lead">
            Мы всегда готовы ответить на ваши вопросы и помочь с любыми вопросами
        </p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <h4>Email</h4>
                    <p class="text-muted">Напишите нам письмо</p>
                    <a href="mailto:info@appp-psy.ru" class="btn btn-outline-primary">
                        info@appp-psy.ru
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="bi bi-telegram"></i>
                    </div>
                    <h4>Telegram</h4>
                    <p class="text-muted">Присоединяйтесь к нашему каналу</p>
                    <a href="https://t.me/+WqnwojGKWjJkNDAy" target="_blank" class="btn btn-outline-primary">
                        Перейти в Telegram
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="bi bi-share"></i>
                    </div>
                    <h4>ВКонтакте</h4>
                    <p class="text-muted">Следите за новостями</p>
                    <a href="https://vk.com/associacia_chpp" target="_blank" class="btn btn-outline-primary">
                        Перейти в VK
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="contact-form">
                    <h3 class="text-center mb-4">Отправить сообщение</h3>
                    <form action="{{ route('v2.refactored.home.contact.send') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Имя *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Тема</label>
                            <input type="text" name="subject" id="subject" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение *</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Отправить сообщение
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h3 class="mb-4">Часто задаваемые вопросы</h3>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Как стать членом Ассоциации?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Для вступления в Ассоциацию необходимо зарегистрироваться на нашем сайте и оформить подписку. После этого вы получите доступ ко всем материалам и мероприятиям.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Какие курсы доступны?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Мы предлагаем широкий спектр курсов по психологии и психотерапии: от базовых программ до специализированных мастер-классов от ведущих специалистов.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Как проходят встречи?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Встречи проходят в онлайн-формате с возможностью активного участия. Записи встреч сохраняются в видеотеке для последующего просмотра.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection