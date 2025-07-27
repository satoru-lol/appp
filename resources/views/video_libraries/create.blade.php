@extends('app', [
'title' => 'Профиль пользователя',
'keywords' => '', # Ключевые слова
'description' => '' # Описание страницы
])

@section('content')
    <div class="container">
        <h1>Создание видеотеки</h1>

        <form action="{{ route('video-libraries.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Название видеотеки</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div><br>

            <div class="form-group">
                <label>Видео</label>
                <div id="videos-wrapper">
                    <div class="video-item">
                        <select class="form-control video-type-select" name="videos[0][type]">
                            <option value="youtube">YouTube</option>
                            <option value="google">Google Drive</option>
                            <option value="yandex">Yandex Disk</option>
                        </select>
                        <input type="url" class="form-control mt-2 video-url" name="videos[0][url]" placeholder="Video URL">
                        <input type="text" class="form-control mt-2 video-path d-none" name="videos[0][path]" placeholder="Video Path">
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-3" id="add-video-btn">Добавить еще</button>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Сохранить</button>
        </form>
    </div>
@endsection

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let videoIndex = 1;

            $('#add-video-btn').click(function() {
                let videoTemplate = `
                    <div class="video-item mt-3">
                        <select class="form-control video-type-select" name="videos[${videoIndex}][type]">
                            <option value="youtube">YouTube</option>
                            <option value="google">Google Drive</option>
                            <option value="yandex">Yandex Disk</option>
                        </select>
                        <input type="url" class="form-control mt-2 video-url" name="videos[${videoIndex}][url]" placeholder="Ссылка видео">
                        <button type="button" class="btn btn-danger mt-2 remove-video-btn">Удалить</button>
                    </div>
                `;

                $('#videos-wrapper').append(videoTemplate);
                videoIndex++;
            });

            $(document).on('click', '.remove-video-btn', function() {
                $(this).closest('.video-item').remove();
            });

            $(document).on('change', '.video-type-select', function() {
                const selectedType = $(this).val();
                const videoItem = $(this).closest('.video-item');

                if (selectedType === 'youtube') {
                    videoItem.find('.video-url').attr('placeholder', 'YouTube URL');
                    videoItem.find('.video-path').addClass('d-none');
                } else if (selectedType === 'google') {
                    videoItem.find('.video-url').attr('placeholder', 'Google Drive URL');
                    videoItem.find('.video-path').addClass('d-none');
                } else if (selectedType === 'yandex') {
                    videoItem.find('.video-url').attr('placeholder', 'Yandex Disk URL');
                    videoItem.find('.video-path').addClass('d-none');
                }

                // Show path input if necessary
                if (selectedType === 'youtube') {
                    videoItem.find('.video-path').removeClass('d-none');
                }
            });
        });
    </script>
