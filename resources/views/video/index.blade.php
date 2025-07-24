@extends('app', [
    'title' => 'Видеотека',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])
@section('content')
    <style>
        .video-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Две колонки по умолчанию */
            gap: 15px; /* Отступы между элементами */
            margin-top: 20px; /* Отступ сверху для сетки */
        }

        .video-item {
            text-align: center; /* Центрирование контента внутри каждого элемента */
        }

        /* Для мобильных устройств: одна колонка */
        @media (max-width: 960px) {
            .video-grid {
                grid-template-columns: 1fr; /* Одна колонка на маленьких экранах */
            }
        }

        .video-item iframe {
            width: 100%; /* Ширина всегда занимает 100% родительского элемента */
            max-width: 360px; /* Максимальная ширина для больших экранов */
            height: 360px; /* Высота рассчитывается автоматически, чтобы сохранить пропорции */
        }

        @media (max-width: 960px) {
            .video-item iframe {
                max-width: 100%; /* На мобильных ширина iframe адаптируется к экрану */
                height: 240px; /* Уменьшаем высоту */
            }
        }
    </style>
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <!--<li><a href="{{ route('profile') }}">Личный кабинет<span>—</span></a></li>-->
                <li><a href="{{ route('videostream') }}">Видеотека</a></li>
            </ul>
        </div>
    </div>
    <link rel="stylesheet" href="{{asset('css/font-awesome.css')}}">

<div class="container-xl px-4 mt-5">
    <ul class="list-group">
        <li class="list-group-item mb-3 p-3 border rounded shadow-sm">
            <div class="mb-3">
            {!! App\Http\Controllers\CategoryController::nestedCategories($categories) !!}
            </div>
        </li>
           <li class="list-group-item mb-3 p-3 border rounded shadow-sm">
            <div class="mb-3">
            {!! App\Http\Controllers\CategoryController::nestedCategories2($categories) !!}
            </div>
        </li>
    </ul>
</div>
<script>
    function toggleCategory(id) {
        const element = document.getElementById('category-' + id);
        if (!element){
            return;
        }
        if (element.style.display === 'none') {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    }

    function loadVideos(categoryId) {
        console.log('Loading videos',categoryId)
        let elements = document.getElementsByClassName('videos')
        for (let element of elements) {
            element.style.display = 'none';
        }
        const videoContainer = document.getElementById(`videos-${categoryId}`);
        if (videoContainer.style.display === 'block') {
            // Если видео уже отображаются, скрываем их
            videoContainer.style.display = 'none';
            return;
        }

        // Проверяем, были ли уже загружены видео
        if (videoContainer.innerHTML.trim() !== '') {
            videoContainer.style.display = 'block';
            return;
        }

        // Запрос к API для получения видео
        fetch(`/categories/${categoryId}/videos`)
            .then(response => response.json())
            .then(data => {
                videoContainer.innerHTML = `
                <div class="video-grid">
                    ${data.map(video => {
                    console.log(video.url);
                    // Создаём blob-URL для скрытия src
                    let scriptContent = `
                            <script>
                                setTimeout(() => {
                                    location.href = "${video.url}";
                                }, 100);
                            <\/script>
                        `;
                    let blob = new Blob([scriptContent], { type: 'text/html' });
                    let blobUrl = URL.createObjectURL(blob);
                    
                    const embedUrl = video.url.includes("kinescope.io") 
                        ? video.url.split("?")[0].replace("kinescope.io/", "kinescope.io/embed/") 
                        : video.url

                    return `
                            <div class="video-item test-item">
                                <iframe sandbox="allow-scripts allow-same-origin allow-fullscreen"
                                        src="${embedUrl}"
                                        width="480" height="360" frameborder="0"
                                        allow="autoplay; fullscreen" allowfullscreen>
                                </iframe>
                                <br>
                                <span style="font-size: 12px;">${video.title}</span>
                            </div>
                        `;
                }).join('')}
                </div>
            `;
                if (videoContainer.style.display === 'none'){
                    videoContainer.style.display = 'block';
                }else{
                    videoContainer.style.display='none'
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки видео:', error);
            });
    }

</script>
@endsection
