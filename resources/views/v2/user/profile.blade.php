@extends('app', [
    'title' => 'Профиль пользователя',
    'keywords' => '',
    'description' => ''
])

{{-- Force re-cache --}}

@section('content')
    <link href="{{ asset('v2/css/profile.css') }}" rel="stylesheet">
    
    <style>
        /* Новые стили для профиля в соответствии с дизайном login-v2 */
        .profile-container {
            padding: 1rem 0;
        }
        
        .profile-card-v2 {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(97, 52, 130, 0.1);
            border: 1px solid #e0e0e0;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .profile-card-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .profile-card-header h5 {
            margin: 0;
            font-size: 1.25rem;
            color: #333;
            font-weight: 600;
        }
        
        .profile-card-body {
            padding: 1.5rem;
        }
        
        /* Обновленные стили для табов */
        .profile-tabs-container {
            margin-bottom: 2rem;
            border-radius: 1rem;
            background-color: #fff;
            padding: 0.5rem;
            box-shadow: 0 4px 16px rgba(97, 52, 130, 0.08);
        }
        
        .profile-tabs-v2 {
            border: none;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
        }
        
        .profile-tabs-v2 .nav-item {
            margin: 0;
        }
        
        .profile-tabs-v2 .nav-link {
            color: #6c757d;
            border: none;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            position: relative;
            transition: all 0.2s ease;
            margin: 0;
            border-radius: 0.75rem;
        }
        
        .profile-tabs-v2 .nav-link:hover {
            color: #613482;
            background-color: rgba(97, 52, 130, 0.05);
        }
        
        .profile-tabs-v2 .nav-link.active {
            color: #fff;
            background-color: #613482;
            border: none;
        }
        
        .profile-tabs-v2 .nav-link.active::after {
            display: none;
        }
        
        @media (max-width: 768px) {
            .profile-tabs-v2 {
                gap: 0.25rem;
                padding: 0.25rem;
            }
            
            .profile-tabs-v2 .nav-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.9rem;
            }
        }
        
        /* Стили для кнопок */
        .btn-v2-primary {
            background-color: #613482;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-v2-primary:hover {
            background-color: #4a276b;
            color: #fff;
        }
        
        .btn-v2-secondary {
            background-color: #f0f2f5;
            color: #333;
            border: 1px solid #ced4da;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-v2-secondary:hover {
            background-color: #e2e6ea;
        }
        
        .btn-v2-danger {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-v2-danger:hover {
            background-color: #c82333;
            color: #fff;
        }
        
        /* Стили для таблиц */
        .table-v2 {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-v2 th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        
        .table-v2 td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .table-v2 tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table-v2 .badge {
            padding: 0.4rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.25rem;
        }
        
        /* Стили для алертов */
        .alert-v2 {
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }
        
        .alert-v2-info {
            background-color: #e8f4fd;
            border-color: #b8daff;
            color: #004085;
        }
        
        .alert-v2-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }
        
        /* Стили для хлебных крошек */
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
    </style>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                <li class="breadcrumb-item active" aria-current="page">Профиль пользователя</li>
            </ol>
        </nav>
    </div>
    
    <div class="container-xl px-4 mt-4 profile-container">
        {{-- Блок для вывода уведомлений об оплате --}}
        @if (session('pay_success'))
            <div class="alert alert-success" role="alert">
                {{ session('pay_success') }}
            </div>
        @endif
        @if (session('pay_error'))
            <div class="alert alert-danger" role="alert">
                {{ session('pay_error') }}
            </div>
        @endif
        @if (session('pay_info'))
            <div class="alert alert-info" role="alert">
                {{ session('pay_info') }}
            </div>
        @endif
        
        <div class="row gap-3 gap-lg-0">
            <div class="col-lg-12">
                
                {{-- TABS --}}
                <div class="profile-tabs-container">
                    <ul class="nav nav-tabs profile-tabs-v2" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'profile' ? 'active' : '' }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="{{ $activeTab == 'profile' ? 'true' : 'false' }}" data-tab="profile">Профиль</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'balance' ? 'active' : '' }}" id="balance-tab" data-bs-toggle="tab" data-bs-target="#balance" type="button" role="tab" aria-controls="balance" aria-selected="{{ $activeTab == 'balance' ? 'true' : 'false' }}" data-tab="balance">Баланс и Оплата</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'activity' ? 'active' : '' }}" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="{{ $activeTab == 'activity' ? 'true' : 'false' }}" data-tab="activity">Активность</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab == 'password' ? 'active' : '' }}" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="{{ $activeTab == 'password' ? 'true' : 'false' }}" data-tab="password">Смена пароля</button>
                        </li>
                        @if (auth()->user()?->isAdmin())
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $activeTab == 'admin' ? 'active' : '' }}" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab" aria-controls="admin" aria-selected="{{ $activeTab == 'admin' ? 'true' : 'false' }}" data-tab="admin">Админка</button>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- TAB CONTENT --}}
                <div class="tab-content" id="profileTabContent">
                    {{-- Profile Tab --}}
                    <div class="tab-pane fade {{ $activeTab == 'profile' ? 'show active' : '' }}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @include('v2.user.partials._details')
                        @include('v2.user.partials._subscription')
                    </div>

                    {{-- Balance Tab --}}
                    <div class="tab-pane fade {{ $activeTab == 'balance' ? 'show active' : '' }}" id="balance" role="tabpanel" aria-labelledby="balance-tab">
                        <div class="profile-card-v2 mb-4">
                            <div class="profile-card-header">
                                <h5 class="mb-0">Пополнить баланс</h5>
                            </div>
                            <div class="profile-card-body">
                                <div class="mb-3">
                                    <p class="mb-0"><strong>Текущий баланс:</strong> {{ auth()->user()->balance ?? 0 }} руб.</p>
                                </div>
                                @include('v2.user.partials._balance_form')
                                <button type="button" class="btn btn-v2-secondary mt-3" id="show-qr-btn" disabled>
                                    <i class="bi bi-qr-code me-2"></i>Показать QR-код для оплаты
                                </button>
                            </div>
                        </div>
                        <div class="profile-card-v2 mt-4">
                            <div class="profile-card-header">
                                <h5 class="mb-0">История транзакций</h5>
                            </div>
                            <div class="profile-card-body">
                                <div id="transactions-tab-content">
                                    @include('v2.user.partials.tabs._transactions')
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Activity Tab --}}
                    <div class="tab-pane fade {{ $activeTab == 'activity' ? 'show active' : '' }}" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                        <div class="profile-card-v2">
                            <div class="profile-card-header">
                                <h5 class="mb-0">Мои встречи</h5>
                            </div>
                            <div class="profile-card-body">
                                @include('v2.user.partials.tabs._meetings')
                            </div>
                        </div>
                        <div class="profile-card-v2 mt-4">
                             <div class="profile-card-header">
                                <h5 class="mb-0">Мои курсы</h5>
                            </div>
                            <div class="profile-card-body">
                                @include('v2.user.partials.tabs._courses')
                            </div>
                        </div>
                         <div class="profile-card-v2 mt-4">
                             <div class="profile-card-header">
                                <h5 class="mb-0">Мои клубы</h5>
                            </div>
                            <div class="profile-card-body">
                                @include('v2.user.partials.tabs._clubs')
                            </div>
                        </div>
                    </div>

                    {{-- Password Tab --}}
                    <div class="tab-pane fade {{ $activeTab == 'password' ? 'show active' : '' }}" id="password" role="tabpanel" aria-labelledby="password-tab">
                        @include('v2.user.partials.tabs._password')
                    </div>

                    {{-- Admin Tab --}}
                    @if (auth()->user()?->isAdmin())
                        <div class="tab-pane fade {{ $activeTab == 'admin' ? 'show active' : '' }}" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                           @include('v2.user.partials.admin._user_management')
                           @include('v2.user.partials.admin._subscription_management')
                           @include('v2.user.partials.admin._participant_payments')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR-код для оплаты</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrcode-container" class="mb-3 d-flex justify-content-center align-items-center" style="min-height: 220px;"></div>
                    <p class="text-muted">Отсканируйте код в вашем банковском приложении</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" id="share-qr-link-btn">
                        <i class="bi bi-share-fill me-2"></i>Поделиться ссылкой
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const showQrBtn = document.getElementById('show-qr-btn');
            const qrCodeModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            const qrcodeContainer = document.getElementById('qrcode-container');
            const shareQrLinkBtn = document.getElementById('share-qr-link-btn');
            let paymentUrlToShare = '';
            
            if(amountInput) {
                amountInput.addEventListener('input', function() {
                    showQrBtn.disabled = !this.value || parseFloat(this.value) <= 0;
                });
            }

            if(showQrBtn) {
                showQrBtn.addEventListener('click', function() {
                    const amount = amountInput.value;
                    if (!amount || parseFloat(amount) <= 0) return;

                    fetch('{{ route("v2.profile.generate-qr-link") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ amount: amount })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.url) {
                            paymentUrlToShare = data.url;
                            qrcodeContainer.innerHTML = ''; // Clear previous QR code
                            new QRCode(qrcodeContainer, {
                                text: data.url,
                                width: 200,
                                height: 200,
                                correctLevel: QRCode.CorrectLevel.L
                            });
                            qrCodeModal.show();
                        } else {
                            alert('Не удалось сгенерировать ссылку для QR-кода.');
                        }
                    })
                    .catch(error => console.error('Error generating QR link:', error));
                });
            }

            if(shareQrLinkBtn) {
                shareQrLinkBtn.addEventListener('click', function() {
                    if (navigator.share && paymentUrlToShare) {
                        navigator.share({
                            title: 'Ссылка на оплату',
                            text: 'Пожалуйста, оплатите по этой ссылке.',
                            url: paymentUrlToShare
                        });
                    } else {
                        alert('Ваш браузер не поддерживает эту функцию, или ссылка не была сгенерирована.');
                    }
                });
            }

            const transactionsContainer = document.getElementById('transactions-tab-content');

            if (transactionsContainer) {
                transactionsContainer.addEventListener('click', function(event) {
                    if (event.target.tagName === 'A' && event.target.closest('.pagination')) {
                        event.preventDefault();
                        const url = event.target.href;

                        fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.text())
                        .then(html => {
                            const listContainer = document.getElementById('transactions-list-container');
                            if(listContainer) {
                                listContainer.innerHTML = html;
                                // Прокрутка к началу блока транзакций, если нужно
                                listContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        })
                        .catch(error => console.error('Error fetching transactions:', error));
                    }
                });
            }

            // Обновление URL при переключении вкладок
            const navLinks = document.querySelectorAll('.profile-tabs-v2 .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab');
                    const url = new URL(window.location.href);
                    url.searchParams.set('tab', tab);
                    window.history.pushState({}, '', url);
                });
            });

            // Проверка URL при загрузке страницы и активация соответствующей вкладки
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                const tabToActivate = document.querySelector(`.profile-tabs-v2 .nav-link[data-tab="${tabParam}"]`);
                if (tabToActivate) {
                    const tabInstance = new bootstrap.Tab(tabToActivate);
                    tabInstance.show();
                }
            }
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Открытие модалки подтверждения покупки
    document.querySelectorAll('.auth-pay-button').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const productId = btn.getAttribute('data-id');
            const cardBody = btn.closest('.subscription-card-body');
            if (cardBody) {
                const productName = cardBody.querySelector('.subscription-title').textContent.trim();
                document.getElementById('productId').value = productId;
                document.getElementById('productName').textContent = productName;
                const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                modal.show();
            }
        });
    });

    // Подтверждение отмены подписки
    const confirmCancellationBtn = document.getElementById('confirmCancellationBtn');
    if (confirmCancellationBtn) {
        confirmCancellationBtn.addEventListener('click', function () {
            fetch('/cancelSubscribe', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(() => window.location.reload());
        });
    }
});
</script>
@endsection

@push('styles')
<style>
    .profile-avatar-container {
        max-width: 180px;
        width: 100%;
        margin: 0 auto;
    }
    .profile-details-grid {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
    }
    @media (max-width: 600px) {
        .profile-details-grid {
            flex-direction: column;
            align-items: center;
        }
    }
    .avatar-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
        transition: transform 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 150px;
        height: 150px;
        background-color: rgba(0, 0, 0, 0);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: background-color 0.3s, opacity 0.3s;
        border-radius: 50%;
        pointer-events: none;
    }
    .avatar-wrapper:hover .avatar-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 1;
        pointer-events: auto;
    }
    .avatar-upload-label {
        color: white;
        text-align: center;
        font-size: 1.2rem;
        font-weight: 500;
        cursor: pointer;
        user-select: none;
        letter-spacing: 0.5px;
    }
    .card.profile-details-card {
        border-top: 0;
        border-radius: 0px 0px 8px 8px;
    }
</style>
@endpush


@push('scripts')
    
@endpush 