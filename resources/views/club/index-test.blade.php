@extends('app')

@section('content')
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Онлайн-клубы</li>
            </ol>
        </nav>
    </div>

    <section class="courses_block courses_block_club">
        <style>
            .breadcrumb {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 2px 12px rgba(97,52,130,0.07);
                padding: 12px 22px;
                margin-bottom: 24px;
                font-size: 1.04rem;
                --bs-breadcrumb-divider-color: #b39ddb;
            }
            .breadcrumb-item + .breadcrumb-item::before {
                color: #b39ddb;
                font-size: 1.1em;
                padding-right: 6px;
                padding-left: 6px;
            }
            .breadcrumb-item a {
                color: #613482;
                text-decoration: none;
                font-weight: 500;
                transition: color .18s;
            }
            .breadcrumb-item a:hover {
                color: #7e57c2;
                text-decoration: underline;
            }
            .breadcrumb-item.active {
                color: #7e57c2;
                font-weight: 600;
            }
            /* Стили для страницы списка клубов */
            .clubs-v2-container { max-width: 1200px; margin: 0 auto; padding: 32px 16px; }
            .clubs-v2-header { margin-bottom: 32px; }
            .clubs-v2-header h1 { color: #613482; margin-bottom: 16px; font-size: 2rem; }
            
            .clubs-v2-list { display: flex; flex-wrap: wrap; gap: 32px; }
            .club-v2-card { background: #fff; border-radius: 18px; box-shadow: 0 2px 16px rgba(97,52,130,0.08); width: 340px; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow .2s; border: 2px solid #eee; }
            .club-v2-card:hover { box-shadow: 0 4px 32px rgba(97,52,130,0.18); border-color: #b39ddb; }
            .club-v2-image { background: linear-gradient(120deg, #ede7f6 60%, #fff 100%); height: 180px; background-size: cover; background-position: center; }
            .club-v2-info { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
            .club-v2-info h2 { font-size: 1.2rem; color: #613482; margin-bottom: 12px; }
            .club-v2-info p { color: #444; margin-bottom: 8px; display: flex; align-items: center; }
            .club-v2-info p svg { margin-right: 8px; }
            .club-v2-actions { margin-top: auto; padding-top: 16px; display: flex; gap: 8px; }
            .club-v2-btn { display: inline-block; background: #613482; color: #fff; border-radius: 8px; padding: 8px 18px; text-decoration: none; transition: background .2s; border: none; cursor: pointer; font-size: 14px; }
            .club-v2-btn:hover { background: #7e57c2; color: #fff; }
            .club-v2-btn-outline { display: inline-block; background: transparent; color: #613482; border: 1px solid #613482; border-radius: 8px; padding: 8px 18px; text-decoration: none; transition: all .2s; font-size: 14px; }
            .club-v2-btn-outline:hover { background: #f5f0ff; color: #613482; }
            .club-v2-btn-light { display: inline-block; background: #f5f0ff; color: #613482; border: none; border-radius: 8px; padding: 8px 18px; text-decoration: none; transition: all .2s; font-size: 14px; }
            .club-v2-btn-light:hover { background: #e8e0f5; color: #613482; }
            
            .club-info-container { background-color: #f9f9f9; padding: 24px; border-radius: 12px; margin-bottom: 32px; }
            .club-info-title { font-size: 1.5rem; color: #613482; margin-bottom: 16px; }
            .club-info-text { line-height: 1.6; margin-bottom: 16px; }
            .club-info-subtitle { font-size: 1.3rem; color: #613482; margin-top: 24px; margin-bottom: 16px; }
            
            @media (max-width: 768px) {
                .clubs-v2-list { justify-content: center; }
                .club-v2-card { width: 100%; max-width: 340px; }
                .club-v2-actions { flex-wrap: wrap; }
            }
            
            /* Стили для модальных окон */
            .custom-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
            .custom-modal.show { display: flex; }
            .custom-modal-content { background: #fff; border-radius: 12px; padding: 24px; width: 90%; max-width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); position: relative; }
            .custom-modal-close { position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; }
            .custom-modal h2 { color: #613482; margin-bottom: 20px; }
            .custom-modal .form-label { color: #444; margin-bottom: 8px; display: block; }
            .custom-modal .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 8px; }
            .custom-modal .form-text { color: #666; font-size: 0.9rem; margin-bottom: 16px; }
            .custom-modal textarea { min-height: 100px; }
        </style>

        <div class="clubs-v2-container">
            <div class="clubs-v2-header">
                <h1>Клубные встречи Ассоциации</h1>

                <div class="club-info-container">
                    <p class="club-info-text" style="font-size: 1.1rem; color: #333; margin-bottom: 18px;">
                        Это регулярные тематические профессиональные встречи, направленные на развитие и сопровождение частнопрактикующих специалистов. Встречи курируются как преподавателями Портала для психологов и психотерапевтов, так и активными участниками Ассоциации. Частота встреч: 1–4 раза в месяц.
                    </p>
                    <h3 class="club-info-subtitle" style="font-size: 1.2rem; color: #613482; margin-top: 18px; margin-bottom: 10px;">Клубы позволяют:</h3>
                    <ul style="margin-bottom: 18px; list-style-position: inside;">
                        <li>Обсудить сложные случаи и получить мнение коллег.</li>
                        <li>Получить экспертное мнение на интересующий вопрос.</li>
                        <li>Обменяться опытом.</li>
                        <li>Получить коллегиальную поддержку.</li>
                        <li>Найти выход из сложной ситуации.</li>
                    </ul>
                    <h3 class="club-info-subtitle" style="font-size: 1.2rem; color: #613482; margin-top: 18px; margin-bottom: 10px;">В клубах не предполагается:</h3>
                    <ul style="list-style-position: inside;">
                        <li>Супервизия конкретного случая.</li>
                        <li>Модельная терапия.</li>
                        <li>Отработка техник.</li>
                        <li>Клинический разбор.</li>
                    </ul>
                     <h3 class="club-info-subtitle" style="margin-top: 24px;">Предстоящие встречи:</h3>
                </div>

                <div class="clubs-v2-list">
                    @forelse ($clubs as $club)
                        <div class="club-v2-card">
                            {{--
                            @if($club->image)
                                <img src="/images/{{ $club->image }}" alt="{{ $club->title }}" class="img-fluid">
                            @endif
                            --}}
                            {{-- <div class="club-v2-image"></div> --}}
                            <div class="club-v2-info">
                                <h2>{{ $club->title }}</h2>
                                @if(!empty($club->clubDates->date))
                                    <p class="club-v2-date">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 2V5M16 2V5M3.5 9.09H20.5M21 8.5V17C21 20 19.5 22 16 22H8C4.5 22 3 20 3 17V8.5C3 5.5 4.5 3.5 8 3.5H16C19.5 3.5 21 5.5 21 8.5Z" stroke="#613482" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M15.6947 13.7H15.7037M15.6947 16.7H15.7037M11.9955 13.7H12.0045M11.9955 16.7H12.0045M8.29431 13.7H8.30329M8.29431 16.7H8.30329" stroke="#613482" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        {{ date('d.m.Y', strtotime($club->clubDates->date)) }}
                                    </p>
                                    <p class="club-v2-time">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22 12C22 17.52 17.52 22 12 22C6.48 22 2 17.52 2 12C2 6.48 6.48 2 12 2C17.52 2 22 6.48 22 12Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M15.71 15.18L12.61 13.33C12.07 13.01 11.63 12.24 11.63 11.61V7.51" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        {{ date('H:i', strtotime($club->clubDates->start_time)) }}-{{ date('H:i', strtotime($club->clubDates->end_time)) }}
                                    </p>
                                @endif
                                
                                @php
                                    $times = json_decode($club->times);
                                @endphp
                                @if ($times && isset($times->read))
                                <p class="club-v2-duration">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.5 12H14.5" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12.5 16V8" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $times->read }}
                                </p>
                                @endif
                                
                                @if(!empty($club->speakers))
                                <p class="club-v2-speakers">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12.12 12.78C12.05 12.77 11.96 12.77 11.88 12.78C10.12 12.72 8.71997 11.28 8.71997 9.50998C8.71997 7.69998 10.18 6.22998 12 6.22998C13.81 6.22998 15.28 7.69998 15.28 9.50998C15.27 11.28 13.88 12.72 12.12 12.78Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18.74 19.38C16.96 21.01 14.6 22 12 22C9.40001 22 7.04001 21.01 5.26001 19.38C5.36001 18.44 5.96001 17.52 7.03001 16.8C9.77001 14.98 14.25 14.98 16.97 16.8C18.04 17.52 18.64 18.44 18.74 19.38Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#613482" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ $club->speakers }}
                                </p>
                                @endif
                                
                                <div class="club-v2-actions">
                                    <a href="{{ $club->feedback }}" target="_blank" class="club-v2-btn-outline">Ссылка</a>
                                    <a href="{{ route('v2.club.show', $club->id) }}" class="club-v2-btn">Подробнее</a>
                                    <button type="button" class="club-v2-btn-light js-donation-btn" data-club-id="{{$club->id}}">Донат</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; width: 100%; padding: 40px 0; background-color: #f8f9fa; border-radius: 12px;">
                            <p style="font-size: 1.1rem; color: #6c757d;">Этот раздел находится на обновлении. Скоро здесь появятся новые клубы!</p>
                        </div>
                    @endforelse
                </div>

                @if($isPermittedAdd)
                    <div class="text-center mt-4">
                        <button id="add-theme-btn" class="club-v2-btn">Добавить встречу</button>
                    </div>
                @endif
             </div>
         </div>
    </section>

    <!-- MODALS -->
    <!-- Add Theme Modal -->
    <div id="add-theme-modal" class="modal">
         <div class="modal-content">
             <span class="close">&times;</span>
             <form action="/club/store" method="post" enctype="multipart/form-data">
                 @csrf
                 <input type="text" name="title" placeholder="Тема встречи">
                 <textarea name="description" placeholder="Описание"></textarea>
                 <input type="file" name="image">
                 <button type="submit">Добавить</button>
             </form>
         </div>
     </div>

    <!-- Donation Modal Structure -->
    <div id="donationModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-modal-close">&times;</span>
            <h2>Поддержка клуба</h2>
            <form id="donationForm" action="#" method="POST">
                @csrf
                <input type="hidden" name="club_id" id="modal_club_id">
                <div class="mb-3">
                    <label for="donationAmount" class="form-label">Сумма</label>
                    <input type="number" class="form-control" name="amount" id="donationAmount" placeholder="Введите сумму" required>
                    <div class="form-text">Деньги будут списаны с вашего счета на сайте</div>
                </div>
                <div class="mb-3">
                    <label for="donationReason" class="form-label">Комментарии</label>
                    <textarea class="form-control" name="reason" id="donationReason" rows="3" placeholder="Напишите комментарии" required></textarea>
                </div>
                <div class="d-flex justify-content-between gap-2">
                     <button type="button" class="btn btn-secondary js-donation-cancel">Закрыть</button>
                     <button type="submit" class="club-v2-btn">Отправить</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addThemeModal = document.getElementById('add-theme-modal');
            const donationModal = document.getElementById('donationModal');

            document.body.addEventListener('click', function(e) {
                // --- Open Modals ---
                if (e.target.matches('#add-theme-btn')) {
                    if (addThemeModal) addThemeModal.style.display = 'flex';
                }
                if (e.target.matches('.js-donation-btn')) {
                    if (donationModal) {
                        const clubId = e.target.dataset.clubId;
                        const clubIdField = donationModal.querySelector('#modal_club_id');
                        if (clubIdField) clubIdField.value = clubId;
                        donationModal.classList.add('show');
                    }
                }

                // --- Close Modals ---
                if (e.target.matches('#add-theme-modal .close')) {
                    if (addThemeModal) addThemeModal.style.display = 'none';
                }
                if (e.target.matches('#donationModal .custom-modal-close') || e.target.matches('#donationModal .js-donation-cancel')) {
                    if (donationModal) donationModal.classList.remove('show');
                }
            });

            // --- Form Submission ---
            if (donationModal) {
                const donationForm = document.getElementById('donationForm');
                if (donationForm) {
                    donationForm.addEventListener('submit', (event) => {
                        event.preventDefault();
                        
                        // Показываем индикатор загрузки
                        const submitBtn = donationForm.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerText;
                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Отправка...';
                        
                        // Получаем CSRF токен
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        
                        // Отправляем запрос на сервер
                        fetch('{{ route("club.donate") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                club_id: donationForm.querySelector('#modal_club_id').value,
                                amount: donationForm.querySelector('#donationAmount').value,
                                reason: donationForm.querySelector('#donationReason').value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Обновляем баланс пользователя, если он отображается на странице
                                const balanceElement = document.querySelector('.custom-balance');
                                if (balanceElement && data.new_balance !== undefined) {
                                    balanceElement.value = data.new_balance + ' руб.';
                                }
                                
                                // Показываем сообщение об успехе
                                alert(data.message || 'Донат успешно отправлен!');
                                
                                // Очищаем форму и закрываем модальное окно
                                donationForm.reset();
                                donationModal.classList.remove('show');
                            } else {
                                alert(data.message || 'Произошла ошибка при отправке доната.');
                            }
                        })
                        .catch(error => {
                            console.error('Ошибка:', error);
                            alert('Произошла ошибка при отправке доната. Пожалуйста, попробуйте позже.');
                        })
                        .finally(() => {
                            // Восстанавливаем кнопку
                            submitBtn.disabled = false;
                            submitBtn.innerText = originalText;
                        });
                    });
                }
            }

            // --- Click outside to close ---
            window.addEventListener('click', (event) => {
                if (event.target == addThemeModal) {
                    addThemeModal.style.display = 'none';
                }
                if (event.target == donationModal) {
                    donationModal.classList.remove('show');
                }
            });
        });
    </script>
@endsection 