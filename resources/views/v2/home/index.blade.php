@extends('app')

@section('content')
    <section class="home" style='position: relative;overflow: hidden;'>
        <div class="container">
            <div class="home_block" >
                <div class="row">
                    <div class="col-lg-7">
                        <div class="text_block">
                            <h1><span>Сообщество экспертов</span> психологической поддержки</h1>
                            <p class="citate">Мы верим в силу совместных усилий.<br> Присоединяйтесь
                                к ассоциации,<br> где каждый играет роль в<br> росте и развитии.</p>
                            <a href="{{ route('introduction') }}" class="join-btn">Присоединиться</a>
                        </div>
                    </div>

                    <style>
                        @media (max-width: 900px) {
                            .home .home_block .block_img img {
                                margin-left: 0;
                                width: 45%;
                                margin-top: 37px;
                            }
                        }
                    </style>
                    <div class="col-lg-5">
                        <div class="">
                            <img class="klimov" src="/img/klimov.webp" alt="" style="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container mt-4">
        <section class="home-v2-about">
            <div class="home-v2-card">
                <div class="home-v2-card-header">
                    <h2>Кто мы<span></span>?</h2>
                </div>
                <div class="home-v2-card-body">
                    <p>Ассоциация частнопрактикующих психологов и психотерапевтов (АЧПП) – пространство, объединяющее психологов и психотерапевтов, реализующих свои услуги в формате частной практики.</p>
                    
                    <p>Цель Ассоциации: всесторонняя помощь психологам и психотерапевтам в открытии и развитии частной практики.</p>
                    
                    <p>Ключевые задачи Ассоциации:</p>
                    <ul class="home-v2-list-no-emoji">
                        <li>✓ Содействие в профессиональном развитии специалистов.</li>
                        <li>✓ Содействие в профессиональном самоопределении специалистов, включая понимание:
                            <ul>
                                <li>собственных профессиональных оснований для работы;</li>
                                <li>проблем, с которыми специалист готов работать;</li>
                                <li>«своих» и «не своих» клиентов;</li>
                            </ul>
                        </li>
                        <li>✓ Содействие в профессиональном самоописании и самопредъявлении, включая создание текста о специалисте, создание контент-плана, выбора площадки для размещения своих статей, аудио и видео материалов.</li>
                        <li>✓ Содействие в продвижении и привлечении клиентов.</li>
                        <li>✓ Содействие в расширении системы научно-непротиворечивого психологического просвещения населения о современных психологических и психотерапевтических представлениях о различных сторонах жизни.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="home-v2-benefits">
            <div class="home-v2-card">
                <div class="home-v2-card-header">
                    <h2>Участие в Ассоциации позволит вам<span></span></h2>
                </div>
                <div class="home-v2-card-body">
                    <div class="home-v2-benefit-item">
                        <h3>1. Регулярно получать уникальный, интересный и актуальный контент</h3>
                    </div>
                    
                    <div class="home-v2-benefit-item">
                        <h3>2. Общаться с коллегами на профессиональные и личные темы, находить партнеров, друзей, единомышленников</h3>
                    </div>
                    
                    <div class="home-v2-benefit-item">
                        <h3>3. Развиваться как специалист:</h3>
                        <ul class="home-v2-list-no-emoji">
                            <li>✓ участвовать в супервизионных и интервизионных группах от Ассоциации;</li>
                            <li>✓ учиться на специальных тематических вебинарах Ассоциации;</li>
                            <li>✓ общаться в группах единомышленников для совместной рефлексии профессионального опыта и тренировки технических приемов отдельных направлений и методов психотерапии;</li>
                            <li>✓ попробовать себя в роли преподавателя с дальнейшими перспективами проведения собственных программ от Ассоциации и Института;</li>
                            <li>✓ найти для себя учителя либо самому выступить в роли учителя для начинающих коллег;</li>
                            <li>✓ публиковать свои научные труды и практические кейсы на ресурсах Ассоциации;</li>
                            <li>✓ выступать в качестве приглашенных гостей на круглых столах, в дискуссиях по конкретным направлениям и публиковаться на ресурсах Ассоциации;</li>
                            <li>✓ принимать участие в составе коллектива авторов в издании ежемесячного/квартального журнала Ассоциации;</li>
                            <li>✓ принимать участие в составе коллектива авторов в создании учебной и научной литературы по направлениям.</li>
                        </ul>
                    </div>
                    
                    <div class="home-v2-benefit-item">
                        <h3>4. Получать трафик и запросы на проведение консультации, создавая уникальный контент, популяризирующий те или иные аспекты психологического и психотерапевтического знания</h3>
                    </div>
                    
                    <div class="home-v2-benefit-item">
                        <h3>5. Приятно проводить время на очных встречах (чаепитие, бар, фотосессии, книжные и киноклубы, туристические походы)</h3>
                    </div>
                    
                    <div class="home-v2-benefit-item">
                        <h3>6. Участвовать в волонтерских и социальных проектах Ассоциации</h3>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="block_connect">
            <div class="container">
                <div class="connecting">
                    <h2><span>Присоединяйтесь</span> к нам</h2>
                    <p>Регистрируйтесь на сайте, чтобы получить доступ к услугам <br> наших психологов, участвовать в дискуссиях и мероприятиях <br>
                        и быть в курсе последних новостей и событий ассоциации.</p>
                    <a href="{{ route('introduction') }}" class="register">Зарегистрироваться</a>
                    <div class="custom-card">
                        <img src="/img/reg-photo.webp" alt="Beautiful Image">
                    </div>
                    <h2>Напишите нам — мы <span>на связи</span>!</h2>
                    <p>Если у вас есть вопросы или вам нужна дополнительная <br> информация, не стесняйтесь связаться с нами.</p>
                    <div class="block_img_conn">
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('styles')
<style>
    /* Стили для hero-секции */
    .home .text_block a.join-btn {
        display: inline-block;
        background-color: #613482;
        color: #fff;
        border: none;
        padding: 15px 35px;
        border-radius: 30px;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 18px;
        margin-top: 25px;
    }
    
    .home .text_block a.join-btn:hover {
        background-color: #4a276b;
        color: #fff;
        text-decoration: none;
    }
    
    /* Стили для блока "Присоединяйтесь к нам" */
    .block_connect {
        padding: 50px 0;
        background-color: transparent;
        margin-top: 40px;
    }
    
    .connecting {
        text-align: center;
    }
    
    .connecting h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .connecting h2 span {
        color: #613482;
    }
    
    .connecting p {
        font-size: 1.1rem;
        margin-bottom: 25px;
        line-height: 1.6;
    }
    
    .connecting .register {
        display: inline-block;
        background-color: #613482;
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 16px;
        margin: 10px 0 30px;
    }
    
    .connecting .register:hover {
        background-color: #4a276b;
        color: #fff;
        text-decoration: none;
    }
    
    .block_img_conn {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }

    /* Общие стили для v2 */
    section {
        margin-bottom: 2.5rem;
    }
    
    .home-v2-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .home-v2-card-header {
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .home-v2-card-header h2 {
        margin: 0;
        font-size: 1.6rem;
        color: #333;
        font-weight: 600;
    }
    
    .home-v2-card-header h2 span {
        color: #613482;
    }
    
    .home-v2-card-body {
        padding: 1.5rem;
    }
    
    /* Стили для секции преимуществ */
    .home-v2-benefit-item {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .home-v2-benefit-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .home-v2-benefit-item h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #613482;
        margin-bottom: 1rem;
    }
    
    /* Списки без эмодзи */
    .home-v2-list-no-emoji {
        padding-left: 1.2rem;
        list-style-type: disc;
    }
    
    .home-v2-list-no-emoji li {
        margin-bottom: 0.5rem;
    }
    
    .home-v2-list-no-emoji ul {
        padding-left: 1.5rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        list-style-type: circle;
    }
    
    /* Стили для кнопок */
    .btn-v2-primary {
        display: inline-block;
        background-color: #613482;
        color: #fff;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s;
        font-size: 1rem;
    }
    
    .btn-v2-primary:hover {
        background-color: #4a276b;
        color: #fff;
        text-decoration: none;
    }
    
    /* Основной стиль карточки */
    .custom-card {
        max-width: 100%;
        margin: 20px auto;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Изображение карточки */
    .custom-card img {
        width: 100%;
        height: auto;
        border-radius: 15px;
        display: block;
    }

    /* Дополнительные стили для небольших экранов */
    @media (min-width: 768px) {
        .custom-card {
            max-width: 70%;
        }
    }

    @media (min-width: 1024px) {
        .custom-card {
            max-width: 50%;
        }
    }
    
    /* Медиа запросы */
    @media (max-width: 992px) {
        .home-v2-join-image {
            margin-top: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .home-v2-card-header h2 {
            font-size: 1.4rem;
        }
        
        .home-v2-benefit-item h3 {
            font-size: 1.1rem;
        }
    }
</style>
@endsection 