@extends('app')

@section('content')
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li><a href="{{ route ('home') }}">Главная <span>—</span></a></li>
                <li>Об Ассоциации</li>
            </ul>
        </div>
    </div><br>
<style>
    .faq-section {
        background: #fdfdfd;
        min-height: 100vh;
    }

    .faq-title h2 {
        position: relative;
        margin-bottom: 45px;
        display: inline-block;
        font-weight: 600;
        line-height: 1;
    }

    .faq-title h2::before {
        content: "";
        position: absolute;
        left: 50%;
        width: 60px;
        height: 2px;
        background: #613482;
        bottom: -25px;
        margin-left: -30px;
    }

    .faq-title p {
        padding: 0 190px;
        margin-bottom: 10px;
    }

    .faq {
        background: #FFFFFF;
        box-shadow: 0 2px 48px 0 rgba(0, 0, 0, 0.06);
        border-radius: 4px;
    }

    .faq .card {
        border: none;
        background: none;
        border-bottom: 1px dashed #CEE1F8;
    }

    .faq .card .card-header {
        padding: 0px;
        border: none;
        background: none;
        -webkit-transition: all 0.3s ease 0s;
        -moz-transition: all 0.3s ease 0s;
        -o-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .faq .card .card-header:hover {
        background: rgba(233, 30, 99, 0.1);
        padding-left: 10px;
    }

    .faq .card .card-header .faq-title {
        width: 100%;
        text-align: left;
        padding: 0px;
        padding-left: 30px;
        padding-right: 30px;
        font-weight: 400;
        font-size: 15px;
        letter-spacing: 1px;
        color: #3B566E;
        text-decoration: none !important;
        -webkit-transition: all 0.3s ease 0s;
        -moz-transition: all 0.3s ease 0s;
        -o-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
        cursor: pointer;
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .faq .card .card-header .faq-title .badge {
        display: inline-block;
        width: 20px;
        height: 20px;
        line-height: 14px;
        float: left;
        -webkit-border-radius: 100px;
        -moz-border-radius: 100px;
        border-radius: 100px;
        text-align: center;
        background: #613482;
        color: #fff;
        font-size: 12px;
        margin-right: 20px;
    }

    .faq .card .card-body {
        padding: 30px;
        padding-left: 35px;
        padding-bottom: 16px;
        font-weight: 400;
        font-size: 16px;
        line-height: 28px;
        letter-spacing: 1px;
        border-top: 1px solid #F3F8FF;
    }

    .faq .card .card-body p {
        margin-bottom: 14px;
    }

    @media (max-width: 991px) {
        .faq {
            margin-bottom: 30px;
        }

        .faq .card .card-header .faq-title {
            line-height: 26px;
            margin-top: 10px;
        }
    }

    .contact-info {
        width: 100%;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .contact {
        position: relative;
        flex: 1;
        max-width: 300px;
        height: 140px;
        background-color: #f3f3f3;
        margin: 20px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon {
        width: 32px;
        height: 32px;
        color: #613482;
        transition: .3s linear;
    }

    .contact:hover .icon {
        transform: scale(4);
        opacity: 0;
    }

    .contact-content h3,
    .contact-content span {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        font-size: 14px;
        opacity: 0;
    }

    .contact-content h3 {
        top: 20px;
        text-transform: uppercase;
        color: #613482;
    }

    .contact-content span {
        bottom: 20px;
        font-weight: 500;
    }

    .contact:hover h3 {
        opacity: 1;
        top: 15px;
        transition: .3s linear .3s;
    }

    .contact:hover span {
        opacity: 1;
        bottom: 46px;
        transition: .3s linear .3s;
    }


    @media screen and (max-width:900px) {
        .contact {
            flex: 100%;
            max-width: 500px;
        }
    }
</style>

<section class="faq-section">
    <div class="container">
        <div class="faq-title text-center pb-3">
            <h2>Об Ассоциации</h2>
        </div>

        <div class="faq" id="accordion">
            <div class="card">
                <div class="card-header" id="participationHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#participation" onclick="pageLoad('participation')">
                            <span class="badge">1</span><b style="color: black">Участие в Ассоциации</b>
                        </h5>
                    </div>
                </div>
                <div id="participation" class="collapse" aria-labelledby="participationHeading" data-bs-parent="#accordion">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="participation-body">
                        <div class="spinner spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="tasksHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#tasks" >
                            <span class="badge">2</span><b style="color: black">Задачи Ассоциации</b>
                        </h5>
                    </div>
                </div>
                <div id="tasks" class="collapse" aria-labelledby="tasksHeading" data-bs-parent="#tasks">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="tasks-body">
                        <p style="color: #613482"><b>Ключевые задачи Ассоциации:</b></p>
                        <ol>
                            <p>1. Содействие в профессиональном развитии специалистов. </p>
                            <p>2. Содействие в профессиональном самоопределении специалистов, включая понимание: собственных профессиональных оснований для работы;
                                проблем, с которыми специалист готов работать; «своих» и «не своих» клиентов. </p>
                            <p>3. Содействие в профессиональном самоописании и самопредъявлении, включая создание текста о специалисте, формирование контент-плана, выбора площадки для размещения своих статей, аудио- и видеоматериалов.</p>
                            <p>4. Содействие в продвижении и привлечении клиентов. </p>
                            <p>5. Содействие в расширении системы научно непротиворечивого психологического просвещения населения о современных психологических и психотерапевтических представлениях о различных сторонах жизни.</p>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="statusHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#status" >
                            <span class="badge">3</span><b style="color: black">Статусы Ассоциации</b>
                        </h5>
                    </div>
                </div>
                <div id="status" class="collapse" aria-labelledby="tasksHeading" data-bs-parent="#tasks">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="status-body">
                        <p style="color: #613482"><b>Статусы Ассоциации :</b></p>
                        <p>1. Кандидат в участники Ассоциации – не выполнены требования к статусу «участник Ассоциации», но есть либо завершенное обучение по специальности «Психология», «Клиническая психология» или «Психотерапия», либо справка о прохождении обучения по специальности «Психология» или «Психотерапия» в настоящее время.
                        </p>
                        <p>2. Участник Ассоциации – специалитет, бакалавриат, магистратура либо диплом о профессиональной переподготовке по специальности «Психология», «Клиническая психология», либо профессиональная переподготовка по специальности «Психотерапия», а также наличие в открытом доступе информации как о частнопрактикующем специалисте (личный сайт, сайт организации, страница, группа или канал в социальных сетях, агрегаторах и пр.)
                        </p>
                        <p>3. Действительный участник Ассоциации – специалист, соответствующий статусу «участник Ассоциации», который либо представляет Портал для психологов и психотерапевтов в качестве основного преподавателя какого-либо направления, либо показал свою работу в формате супервизии одному из супервизоров Ассоциации и не получил существенных замечаний.
                        </p>
                        <p>4. Особый статус «Доверенный врач» могут получить психиатры и наркологи, сотрудничающие с психологами, клиническими психологами и психотерапевтами по вопросам совместного ведения клиентского случая. Для получения статуса «Доверенный врач» требуется соответствующее образование и рекомендация кого-то из участников Ассоциации либо собеседование с кем-то из супервизоров Ассоциации.
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="mHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#m" >
                            <span class="badge">4</span><b style="color: black ">Мероприятия Ассоциации</b>
                        </h5>
                    </div>
                </div>
                <div id="m" class="collapse" aria-labelledby="tasksHeading" data-bs-parent="#m">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="m-body">
                        <p style="color: #613482"><b>Мероприятия Ассоциации:</b></p>
                        <p>1. Общий базовый курс «Система развития частной практики "под ключ": самоописание, позиционирование, продвижение» для новых участников Ассоциации, позволяющий начать либо оптимизировать уже имеющуюся частную практику. </p>
                        <p>2. Уникальные образовательные, развивающие, досуговые материалы (лекции, интервью, мастер-классы). </p>
                        <p>3. Система супервизорской и интервизорской подготовки участников. </p>
                        <p>4. Тематические регулярные клубные встречи очно и онлайн, направленные на поддержку участников, профессиональное общение и досуг. </p>
                        <p>5. Система подготовки преподавателей «Полигон», направленная на поиск и развитие начинающих преподавателей по психологическим и психотерапевтическим дисциплинам. </p>
                        <p>6. Система психологического просвещения населения с одновременной помощью специалистам в привлечении клиентов. </p>
                        <p>7. Система личного учительства через создание и сопровождение отношений «учитель-ученик».</p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="contactsHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#contacts" onclick="pageLoad('contacts')">
                            <span class="badge">5</span><b style="color: black">Контакты</b>
                        </h5>
                    </div>
                </div>
                <div id="contacts" class="collapse" aria-labelledby="contactsHeading" data-bs-parent="#accordion">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="contacts-body">
                        <div class="spinner spinner-border" role="status"></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="paymentsHeading">
                    <div class="mb-0">
                        <h5 class="faq-title" data-bs-toggle="collapse" data-bs-target="#payments" onclick="pageLoad('payments')">
                            <span class="badge">6</span><b style="color: black">Реквизиты</b>
                        </h5>
                    </div>
                </div>
                <div id="payments" class="collapse" aria-labelledby="paymentsHeading" data-bs-parent="#accordion">
                    <div class="card-body" style="max-height: 500px; overflow: scroll" id="payments-body">
                        <div class="spinner spinner-border" role="status"></div>
                    </div>
                </div>
            </div>



        </div>
		<p></p>
		<p><b>Ассоциация частнопрактикующих психологов и психотерапевтов (АЧПП)*</b><span style="font-weight: 400;"> &ndash; пространство, объединяющее психологов и психотерапевтов, реализующих свои услуги в формате частной практики.</span></p>
<p><b>Цель Ассоциации:</b><span style="font-weight: 400;"> всесторонняя помощь психологам и психотерапевтам в открытии и развитии частной практики.</span></p>
<p><b>Ключевые задачи Ассоциации:</b></p>
<ol>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Содействие в профессиональном развитии специалистов.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Содействие в профессиональном самоопределении специалистов, включая понимание:</span></li>
</ol>
<ul style="margin-left: 20px">
    <li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;"><b>-</b> собственных профессиональных оснований для работы;</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;"><b>-</b> проблем, с которыми специалист готов работать;</span></li>
    <li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;"><b>-</b> &laquo;своих&raquo; и &laquo;не своих&raquo; клиентов;</span></li>
</ul>
<ol>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Содействие в профессиональном самоописании и самопредъявлении, включая создание текста о специалисте, создание контент-плана, выбора площадки для размещения своих статей, аудио и видео материалов.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Содействие в продвижении и привлечении клиентов.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Содействие в расширении системы научно-непротиворечивого психологического просвещения населения о современных психологических и психотерапевтических представлениях о различных сторонах жизни.</span></li>
</ol>
<p><b>Для реализации задач Ассоциация предлагает следующие мероприятия</b><span style="font-weight: 400;"> (мероприятия будут внедряться постепенно, список может дополняться):</span></p>
<ol>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Общий базовый курс &laquo;Система развития частной практики &laquo;под ключ&raquo;: самоописание, позиционирование, продвижение&raquo; для новых участников Ассоциации, позволяющий начать либо оптимизировать уже имеющуюся частную практику.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Уникальные образовательные, развивающие, досуговые материалы (лекции, интервью, мастер-классы).</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Система супервизорской и интервизорской подготовки участников.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Тематические регулярные клубные встречи очно и онлайн, направленные на поддержку участников, профессиональное общение и досуг.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Система подготовки преподавателей &laquo;Полигон&raquo;, направленную на поиск и развитие начинающих преподавателей по психологическим и психотерапевтическим дисциплинам.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Система психологического просвещения населения с одновременной помощью специалистам в привлечении клиентов.</span></li>
<li style="font-weight: 400;" aria-level="1"><span style="font-weight: 400;">✔ Систему личного учительства через создание и сопровождение отношений &laquo;учитель-ученик&raquo;.</span></li>
</ol>
<p><span style="font-weight: 400;"><b>Членство в Ассоциации дает приоритетные условия при поступлении на образовательные программы СПб ИДПО и агрегаторе Портала для психологов и психотерапевтов (в разработке, ожидаемый срок запуска осень 2024 г.).</b></span></p>
<p><span style="font-weight: 400;">Условия вступления в ассоциацию находятся в разработке, официальное открытие Ассоциации состоится 6 июля 2024 г. на очном летнем Марафоне Портала для психологов и психотерапевтов, отель Введенский, г. Санкт-Петербург.</span></p>
<p><b>Оставить предварительную заявку на участие в Ассоциации</b></p>
<p><span style="font-weight: 400;">* ОГРН 1237800080825 от 13 июля 2023 г., ИНН/КПП 7816742659/781601001</span></p>
    </div>

</section>

@csrf

<script>
    let activePage

    function pageLoad(page) {
        if (activePage === page) {
            activePage = ''
            return true
        } else {
            activePage = page
        }

        $('#' + page + '-body').html('<div class="spinner spinner-border" role="status"></div>')

        $.post('/pages/' + page, {
            _token: $('input[name="_token"]').val()
        }, (content) => {
            $('#' + page + '-body').html(content)
        })
    }
</script>
@endsection
