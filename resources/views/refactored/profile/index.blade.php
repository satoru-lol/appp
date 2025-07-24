@extends('app')

@section('title', $title ?? 'Профиль пользователя - АЧПП')
@section('description', $description ?? 'Управление профилем в Ассоциации частнопрактикующих психологов и психотерапевтов')

@section('content')
<link href="{{ asset('v2/css/profile.css') }}" rel="stylesheet">

<style>
    .profile-container {
        padding: 2rem 0;
        background-color: #f4f7f6;
        min-height: calc(100vh - 150px);
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
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
    }
    
    .profile-card-header h5 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    
    .profile-card-body {
        padding: 1.5rem;
    }
    
    .profile-tabs-container {
        margin-bottom: 2rem;
        border-radius: 1rem;
        background-color: #fff;
        padding: 0.5rem;
        box-shadow: 0 4px 16px rgba(97, 52, 130, 0.08);
    }
    
    .nav-pills .nav-link {
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        margin: 0.25rem;
        color: #6c757d;
        border: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #613482 0%, #4a276b 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(97, 52, 130, 0.3);
    }
    
    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f8f9fa;
        color: #613482;
    }
    
    .avatar-section {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 1rem;
    }
    
    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    
    .avatar-upload-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #613482;
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .avatar-upload-btn:hover {
        background: #4a276b;
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
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    
    .btn-v2-primary:hover {
        background-color: #4a276b;
    }
    
    .btn-v2-outline {
        background-color: transparent;
        color: #613482;
        border: 1px solid #613482;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-v2-outline:hover {
        background-color: #613482;
        color: white;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-radius: 0.75rem;
        text-align: center;
        border: 1px solid #e0e0e0;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #613482;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .subscription-status {
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .subscription-active {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .subscription-inactive {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .clubs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .club-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #e0e0e0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .club-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(97, 52, 130, 0.1);
    }
    
    .club-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .club-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .club-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<div class="profile-container">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Навигация по табам -->
        <div class="profile-tabs-container">
            <ul class="nav nav-pills justify-content-center" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->get('tab', 'profile') === 'profile' ? 'active' : '' }}" 
                            id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile-content" 
                            type="button" role="tab">
                        <i class="bi bi-person me-2"></i>Профиль
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->get('tab') === 'subscription' ? 'active' : '' }}" 
                            id="subscription-tab" data-bs-toggle="pill" data-bs-target="#subscription-content" 
                            type="button" role="tab">
                        <i class="bi bi-credit-card me-2"></i>Подписка
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->get('tab') === 'clubs' ? 'active' : '' }}" 
                            id="clubs-tab" data-bs-toggle="pill" data-bs-target="#clubs-content" 
                            type="button" role="tab">
                        <i class="bi bi-people me-2"></i>Мои клубы
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ request()->get('tab') === 'balance' ? 'active' : '' }}" 
                            id="balance-tab" data-bs-toggle="pill" data-bs-target="#balance-content" 
                            type="button" role="tab">
                        <i class="bi bi-wallet me-2"></i>Баланс
                    </button>
                </li>
            </ul>
        </div>

        <!-- Содержимое табов -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Таб профиля -->
            <div class="tab-pane fade {{ request()->get('tab', 'profile') === 'profile' ? 'show active' : '' }}" 
                 id="profile-content" role="tabpanel">
                @include('refactored.profile.partials.profile-tab', ['user' => $user])
            </div>

            <!-- Таб подписки -->
            <div class="tab-pane fade {{ request()->get('tab') === 'subscription' ? 'show active' : '' }}" 
                 id="subscription-content" role="tabpanel">
                @include('refactored.profile.partials.subscription-tab', ['user' => $user, 'subscription' => $subscription ?? null])
            </div>

            <!-- Таб клубов -->
            <div class="tab-pane fade {{ request()->get('tab') === 'clubs' ? 'show active' : '' }}" 
                 id="clubs-content" role="tabpanel">
                @include('refactored.profile.partials.clubs-tab', ['clubs' => $clubs ?? []])
            </div>

            <!-- Таб баланса -->
            <div class="tab-pane fade {{ request()->get('tab') === 'balance' ? 'show active' : '' }}" 
                 id="balance-content" role="tabpanel">
                @include('refactored.profile.partials.balance-tab', ['user' => $user])
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка переключения табов с обновлением URL
    const tabButtons = document.querySelectorAll('#profileTabs .nav-link');
    
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(e) {
            const tabId = e.target.getAttribute('data-bs-target').replace('#', '').replace('-content', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        });
    });

    // Активация таба на основе URL параметра
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'profile';
    
    const activeTabButton = document.getElementById(activeTab + '-tab');
    if (activeTabButton) {
        const tab = new bootstrap.Tab(activeTabButton);
        tab.show();
    }
});
</script>
@endsection