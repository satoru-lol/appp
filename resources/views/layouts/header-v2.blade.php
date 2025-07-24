<style>
    .header-v2 {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        padding: 0.75rem 0;
        transition: all 0.3s ease-in-out;
    }
    .header-v2 .navbar-brand img {
        height: 40px;
    }
    .header-v2 .nav-link {
        color: #495057;
        font-weight: 500;
        transition: color 0.2s ease-in-out;
        padding: 0.5rem 1rem;
        position: relative;
    }
    .header-v2 .nav-link:hover,
    .header-v2 .nav-link.active {
        color: #613482;
    }
    .header-v2 .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 50%;
        height: 2px;
        background-color: #613482;
    }
    .header-v2 .dropdown-menu {
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        padding: 0.5rem 0;
    }
    .header-v2 .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1.5rem;
    }
    .header-v2 .dropdown-item i {
        color: #6c757d;
    }
    .header-v2 .profile-avatar {
        height: 40px;
        width: 40px;
        object-fit: cover;
    }
    .navbar-toggler {
        border: none;
        padding: 0.25rem 0.5rem;
    }
    .navbar-toggler:focus {
        box-shadow: none;
    }
    .mobile-actions {
        display: flex;
        align-items: center;
        gap: 0rem;
    }
</style>

<header class="header-v2">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('v2.refactored.home.index') }}">
                <img src="/img/logo.svg" alt="Logo АЧПП">
            </a>

            <!-- Mobile-only actions (Avatar dropdown & Hamburger) -->
            <div class="d-lg-none mobile-actions">
                @auth
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="mobileUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             <img src="/avatar/{{ auth()->user()->id }}" alt="Аватар" class="rounded-circle profile-avatar">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="mobileUserDropdown">
                            <li><h6 class="dropdown-header">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index') }}"><i class="bi bi-person-circle"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'clubs']) }}"><i class="bi bi-people"></i>Мои клубы</a></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'balance']) }}">
                                    <i class="bi bi-wallet2"></i>
                                    <span>Баланс</span>
                                    <span class="badge bg-light text-dark rounded-pill ms-auto">{{ auth()->user()->balance ?? 0 }} ₽</span>
                                </a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'subscription']) }}"><i class="bi bi-credit-card"></i>Подписка</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                                    <i class="bi bi-box-arrow-right"></i>Выйти
                                </a>
                                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavV2" aria-controls="navbarNavV2" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="navbarNavV2">
                <!-- Main Nav Links (centered for desktop) -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('v2.refactored.home.about') ? 'active' : '' }}" href="{{ route('v2.refactored.home.about') }}">Об Ассоциации</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('v2/refactored/courses*') ? 'active' : '' }}" href="{{ route('v2.refactored.courses.index') }}">Курсы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('v2/refactored/meetings*') ? 'active' : '' }}" href="{{ route('v2.refactored.meetings.index') }}">Наши встречи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('v2/refactored/clubs*') ? 'active' : '' }}" href="{{ route('v2.refactored.clubs.index') }}">Онлайн-клубы</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('v2/refactored/video*') ? 'active' : '' }}" href="{{ route('v2.refactored.video.index') }}">Видеотека</a>
                    </li>
                    @endauth
                </ul>

                <!-- Right side Auth Links (Desktop-only) -->
                <ul class="navbar-nav d-none d-lg-flex align-items-center">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="desktopUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="/avatar/{{ auth()->user()->id }}" alt="Аватар" class="rounded-circle me-2 profile-avatar">
                                {{ auth()->user()->firstname }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="desktopUserDropdown">
                               <li><h6 class="dropdown-header">{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</h6></li>
                                <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index') }}"><i class="bi bi-person-circle"></i>Профиль</a></li>
                                <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'clubs']) }}"><i class="bi bi-people"></i>Мои клубы</a></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'balance']) }}">
                                        <i class="bi bi-wallet2"></i>
                                        <span>Баланс</span>
                                        <span class="badge bg-light text-dark rounded-pill ms-auto">{{ auth()->user()->balance ?? 0 }} ₽</span>
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('v2.refactored.profile.index', ['tab' => 'subscription']) }}"><i class="bi bi-credit-card"></i>Подписка</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form-desktop').submit();">
                                        <i class="bi bi-box-arrow-right"></i>Выйти
                                    </a>
                                    <form id="logout-form-desktop" action="{{ route('logout') }}" method="GET" style="display: none;">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a href="{{ route('v2.refactored.auth.login') }}" class="btn" style="background-color: #613482; color: #fff;">Войти</a>
                        </li>
                    @endauth
                </ul>
                
                <!-- Login button for mobile menu -->
                <div class="d-lg-none mt-3">
                     @guest
                        <a href="{{ route('v2.refactored.auth.login') }}" class="btn w-100" style="background-color: #613482; color: #fff;">Войти</a>
                    @endguest
                </div>

            </div>
        </div>
    </nav>
</header> 