 @extends('app', [
    'title' => 'Добавление клубы',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
 <div class="tab-content" id="tab-4">
                            <div class="container-xl px-4 mt-4">
                                <div class="transaction-card card modern-card">
                                    <div class="card-title">
                                        <h3 class="modern-title">Видеотека
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-camera-reels" viewBox="0 0 16 16">
                                                <path
                                                    d="M6 3a3 3 0 1 1-6 0 3 3 0 0 1 6 0M1 3a2 2 0 1 0 4 0 2 2 0 0 0-4 0"/>
                                                <path
                                                    d="M9 6h.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 7.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 16H2a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm6 8.73V7.27l-3.5 1.555v4.35zM1 8v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1"/>
                                                <path
                                                    d="M9 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6M7 3a2 2 0 1 1 4 0 2 2 0 0 1-4 0"/>
                                            </svg>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        @if(session('error'))
                                            <div class="alert alert-danger">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        <div id="video-buttons-container" class="btn-container">
                                            <a href="{{ $hasAccess ? route('video.detail', ['id' => 1]) : 'javascript:void(0)' }}"
                                            onclick="{{ !$hasAccess ? 'alert(\'Видеотека доступна только по подписке \\\'Пробная\\\', \\\'Премиум\\\' или \\\'Базовый\\\'\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                            
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Всё о развитии и продвижении частной практики
                                            </a>
                                            <a href="{{ $hasAccess ? route('video.detail', ['id' => 2]) : 'javascript:void(0)' }}"
                                              onclick="{{ !$hasAccess ? 'alert(\'Видеотека доступна только по подписке \\\'Пробная\\\', \\\'Премиум\\\' или \\\'Базовый\\\'\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Всё о профессиональной подготовке психолога к частной практике
                                            </a>
                                            <a href="{{ $hasAccess ? route('video.detail', ['id' => 3]) : 'javascript:void(0)' }}"
                                              onclick="{{ !$hasAccess ? 'alert(\'Видеотека доступна только по подписке \\\'Пробная\\\', \\\'Премиум\\\' или \\\'Базовый\\\'\')' : '' }}"
                                               class="btn btn-brand modern-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    <path
                                                        d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                </svg>
                                                Фрагменты с курсов Портала ДПО
                                            </a>
                                            @php
                                                $categoryVideo = \App\Models\Category::whereNull('parent_id')->get();
                                            @endphp

                                            @foreach($categoryVideo as $cat)
                                                <a onclick="{{ !$hasAccess ? 'alert(\'Видеотека доступна только по подписке \\\'Пробная\\\', \\\'Премиум\\\' или \\\'Базовый\\\'\')' : '' }}" href="{{ $hasAccess ? route('show.category', ['id' => $cat->id]) : 'javascript:void(0)' }}"
                                                   class="btn btn-brand modern-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-fast-forward"
                                                         viewBox="0 0 16 16">
                                                        <path
                                                            d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                        <path
                                                            d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    </svg>
                                                    {{$cat->name}}
                                                </a>

                                                {{--<a href="{{ route('video.detail', ['id' => 5]) }}"
                                                   class="btn btn-brand modern-btn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                        <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                        <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                    </svg>
                                                    КУРСЫ
                                                </a>--}}
                                            @endforeach
                                            {{--   <!-- Дополнительные кнопки, которые будут скрыты -->
                                               <a href="{{ route('video.detail', ['id' => 4]) }}" class="btn btn-brand modern-btn hidden-btn">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                       <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                       <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                   </svg>
                                                   Скрытая кнопка
                                               </a>
                                               <a  href="{{ route('video.detail', ['id' => 5]) }}" class="btn btn-brand modern-btn hidden-btn">
                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-fast-forward" viewBox="0 0 16 16">
                                                       <path d="M6.804 8 1 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C.713 12.69 0 12.345 0 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                       <path d="M14.804 8 9 4.633v6.734zm.792-.696a.802.802 0 0 1 0 1.392l-6.363 3.692C8.713 12.69 8 12.345 8 11.692V4.308c0-.653.713-.998 1.233-.696z"/>
                                                   </svg>
                                                   Скрытая кнопка
                                               </a>--}}
                                        </div>
                                  <!--      <button id="show-more-btn" class="btn btn-brand mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path
                                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                <path
                                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                            </svg>
                                            Смотреть все
                                        </button>-->
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                         <style>
                        .modern-card {
                            background-color: #f9f9f9;
                            border-radius: 12px;
                            padding: 20px;
                            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                            transition: transform 0.3s ease;
                        }

                        .modern-title {
                            font-family: 'Montserrat', sans-serif;
                            font-size: 24px;
                            font-weight: 600;
                            color: #333;
                            margin-bottom: 20px;
                        }

                        .modern-btn {
                            background-color: #562E74;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            margin-bottom: 10px;
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                            border-radius: 10px;
                            font-family: 'Montserrat', sans-serif;
                            font-weight: 500;
                            font-size: 16px;
                            transition: background-color 0.3s ease, box-shadow 0.3s ease;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            width: 100%;
                            text-align: left;
                        }

                        .modern-btn:hover {
                            background-color: #3e1f4d;
                            color: white;
                        }

                        #video-buttons-container {
                            display: flex;
                            flex-direction: column;
                            gap: 10px;
                        }

                        .hidden-btn {
                            display: none;
                        }

                        #show-more-btn {
                            background-color: #562E74;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            cursor: pointer;
                            border-radius: 10px;
                            font-family: 'Montserrat', sans-serif;
                            font-weight: 500;
                            transition: background-color 0.3s ease, box-shadow 0.3s ease;
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            display: block;
                        }

                        #show-more-btn:hover {
                            background-color: #3e1f4d;
                        }
                    </style>

                    <script>
                        document.getElementById('show-more-btn').addEventListener('click', function () {
                            var hiddenButtons = document.querySelectorAll('.hidden-btn');
                            var isHidden = hiddenButtons[0].style.display === 'none' || hiddenButtons[0].style.display === '';

                            hiddenButtons.forEach(function (btn) {
                                btn.style.display = isHidden ? 'flex' : 'none'; // Переключаем видимость
                            });

                            // Меняем текст кнопки в зависимости от состояния
                            this.innerHTML = isHidden ?
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
  <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
  <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
  <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
</svg> Свернуть` :
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg> Смотреть все`;
                        });

                    </script>
                    
                         <script>
                        document.getElementById('show-more-btn').addEventListener('click', function () {
                            var hiddenButtons = document.querySelectorAll('.hidden-btn');
                            var isHidden = hiddenButtons[0].style.display === 'none' || hiddenButtons[0].style.display === '';

                            hiddenButtons.forEach(function (btn) {
                                btn.style.display = isHidden ? 'flex' : 'none'; // Переключаем видимость
                            });

                            // Меняем текст кнопки в зависимости от состояния
                            this.innerHTML = isHidden ?
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16">
  <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
  <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
  <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
</svg> Свернуть` :
                                `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg> Смотреть все`;
                        });

                    </script>

                        @endsection