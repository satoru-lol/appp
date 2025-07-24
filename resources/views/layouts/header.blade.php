
<header id="header">
        <div class="header_block d-flex align-items-center justify-content-between  px-0 px-lg-5">
            <!-- Логотип -->
            <div class="logo">
                <a href="/"><img src="/img/logo.svg" alt="Logo"></a>
            </div>

            <!-- Обычное меню -->
            <nav class="menu_block d-lg-block">
                <ul class="d-flex">
                    <li><a href="{{ route('about') }}" class="cool-link">Об Ассоциации</a></li>
                    <li><a href="{{ route('courses-v2.index') }}" class="cool-link">Курсы</a></li>
                    <!--<li><a href="/polygons" class="cool-link">Полигон</a></li>-->
                    <!--<li><a href="/regmerop" class="cool-link">Регулярные мероприятия</a></li>-->
                    <li><a href="{{ route('v2.meetings.index') }}" class="cool-link">Наши встречи</a></li>
                    <li><a href="{{ route('v2.club.index') }}" class="cool-link">Онлайн-клубы</a></li>
                     @auth
                         <li><a class="cool-link" href="{{ route('v2.video.index') }}">Видеотека</a></li>
                     @endauth
                     
{{--                    <li><a href="/forum" class="cool-link">Форум</a></li>--}}
{{--                    <li><a href="/blog" class="cool-link">Блоги</a></li>--}}
                </ul>
            </nav>

            <!-- Гамбургер-меню для мобильных устройств -->
            <div class="hamburger-menu d-lg-none">
                <input id="menu__toggle" type="checkbox" />
                <label class="menu__btn" for="menu__toggle">
                    <span></span>
                </label>
                <ul class="menu__box">
                    <li><a href="{{ route('about') }}">Об Ассоциации</a></li>
                    <li><a href="{{ route('courses-v2.index') }}">Курсы</a></li>
                    <!--<li><a href="/polygons">Полигон</a></li>-->
                    <!--<li><a href="/regmerop">Регулярные мероприятия</a></li>-->
                    <li><a href="{{ route('v2.meetings.index') }}" class="cool-link">Наши встречи</a></li>
                    <li><a href="{{ route('v2.club.index') }}">Онлайн-клубы</a></li>
                    @auth
                                  <li><a href="{{ route('v2.video.index') }}">Видеотека</a></li>
                        <li class="dropdown">
                            <div class="profile d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                <img src="/avatar/{{ auth()->user()->id }}" alt="Profile Picture" class="profile-picture">
                                <span>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
{{--
                                <div class="dropdown-icon"><img src="/img/select.svg" alt=""></div>
--}}
                            </div>
                            <ul class="dropdown-menu">

                            </ul>
                        </li>
                        <li><a class="dropdown-item {{ request()->routeIs('profile') ? 'active' : '' }}" href="/profile">Профиль</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item btn btn-danger text-danger" href="/logout">{{ __('Выйти') }}</a></li>
{{--
                        <li><a class="dropdown-item btn btn-danger" href="/logout">{{ __('Выйти') }}</a></li>
--}}
                    @else
                        <li><a href="{{ route('login') }}" class="join">Войти</a></li>
                    @endauth
                    <li class="social_nets">
                        <a target="_blank" href="https://vk.com/associacia_chpp"><img src="/img/social1.svg" alt="VK"></a>
                        <a target="_blank" href="https://t.me/+WqnwojGKWjJkNDAy"><img src="/img/social2.svg" alt="Telegram"></a>
                    </li>
                    <li><span style="font-size: 10px">© 2024 Psylancesociaty.ru — ассоциация психологов.</span></li>
                </ul>
            </div>

            <!-- Версия для ПК, отображаемая на больших экранах -->
            <div class="come_in d-lg-block">
                @auth
                    <div class="dropdown">
                        <div class="profile d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <img src="/avatar/{{ auth()->user()->id }}" alt="Profile Picture" class="profile-picture">
                            <span>{{ auth()->user()->firstname }} {{ auth()->user()->lastname }}</span>
                            <div class="dropdown-icon"><img src="/img/select.svg" alt=""></div>
                        </div>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ request()->routeIs('profile') ? 'active' : '' }}" href="/profile">Профиль</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item btn btn-danger" href="/logout">{{ __('Выйти') }}</a></li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="join btn btn-success" style="background-color: #613482; margin-right: 10px">Войти</a>
                @endauth
            </div>
        </div>
</header>
<style>

</style>
