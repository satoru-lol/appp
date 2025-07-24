@extends('app', [
    'title' => 'Добавление блога',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('blog') }}">Блоги <span>—</span></a></li>
            <li>{{\Illuminate\Support\Facades\Route::is('editBlog') ? 'Редактирование блога' : 'Добавление блога'}}</li>
        </ul>
    </div>
</div>
<div class="theme_block">
    <div class="container">
        <div class="title">
            <h2>{{\Illuminate\Support\Facades\Route::is('editBlog') ? 'Редактирование блога' : 'Добавление блога'}}</h2>
        </div>
        <form action="{{\Illuminate\Support\Facades\Route::is('editBlog') ? route('editBlog', [$blog->id]) : route('blog.add')}}" method="{{\Illuminate\Support\Facades\Route::is('editBlog') ? 'get' : 'post'}}" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-300">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="name_post">
                <input type="text" name="name" value="{{!empty($blog->name) ? $blog->name : ""}}" placeholder="Название блога">
                <input type="text" name="time_read" value="{{!empty($blog->time_read) ? $blog->time_read : ""}}" placeholder="Время чтения">
            </div>
            <div class="name_post">
                <select id="author" name="author" {{ $user->group !== 'admin' ? 'disabled' : '' }}>
                    <option value="im" {{!empty($blog->author) ? "im" : "selected"}}>Автор: Вы</option>
                    @if ($user->group === 'admin')
                    <option value="site" {{!empty($blog->author) ? "site" : "selected"}}>Автор: Редакция ассоциации</option>
                    @endif
                </select>

                <select name="category_id">
                    <option>Выберите категорию</option>
                    @foreach ($categories as $item)
                    <option value="{{ $item->id }}" {{!empty($blog->blog_category_id) && $blog->blog_category_id == $item->id ? "selected" : ""}}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                    <p>Загрузите изображение</p>
                    <input type="file" name="image" id="fileInput" style="display:none" accept=".jpg,.jpeg,.png">
                </div>
            </div><br>
            <div id="fileList">
                @if(!empty($blog->image))
                    <h4>Ранее загруженные картинки</h4><br>
                    <div>
                        <img src="/img/blog/{{$blog->image}}" name="uploaded_image" style="max-width: 150px;">
                        <input type="hidden" name="uploaded_image" value="{{$blog->image}}">
                    </div>
                @endif
            </div>

            <div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('videoInput').click()">
                    <p>Загрузите видео</p>
                    <input type="file" name="video" id="videoInput" style="display:none" {{--accept=".video/mp4"--}}>
                </div>
            </div>
            <div id="videoList"></div><br>


            <textarea name="text" placeholder="Текст...">{{!empty($blogContent->text) ? $blogContent->text : ""}}</textarea>

            <button type="submit">{{\Illuminate\Support\Facades\Route::is('editBlog') ? 'Редактировать блог' : 'Добавить блог'}}</button>
        </form>
    </div>
</div>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/langs/ru.js" referrerpolicy="origin"></script>

<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/68cz9g6lyp34hqqoud1pb0russyz0zrzp1o8okddqdp8kkye/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        language: 'ru',
        content_style: `
      table {
        border-collapse: collapse;
        width: 100%;
      }
      table, th, td {
        border: 1px solid black;
      }
      th, td {
        padding: 8px;
        text-align: left;
      }
    `
    });

    document.getElementById('fileInput').addEventListener('change', (e) => {
        const fileListContainer = document.getElementById('fileList');

        const file = e.target.files[0];
        const fileSize = (file.size / 1024).toFixed(2); // Размер в килобайтах
        const fileName = file.name;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-list-item';
        fileItem.innerHTML = `
            <span>${fileName} (${fileSize} Кбайт)</span>
        `;

        fileListContainer.innerHTML = fileItem.outerHTML;
    });

    document.getElementById('videoInput').addEventListener('change', (e) => {
        const fileListContainer = document.getElementById('videoList');

        const file = e.target.files[0];
        const fileSize = (file.size / 10024).toFixed(2); // Размер в килобайтах
        const fileName = file.name;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-list-item';
        fileItem.innerHTML = `
            <span>${fileName} (${fileSize} Кбайт)</span>
        `;

        fileListContainer.innerHTML = fileItem.outerHTML;
    })
</script>
@endsection
