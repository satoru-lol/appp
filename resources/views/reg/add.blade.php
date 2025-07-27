@extends('app', [
    'title' => 'Добавление мероприятия',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('blog') }}">Блоги <span>—</span></a></li>
            <li>{{\Illuminate\Support\Facades\Route::is('editRegmerop') ? 'Редактирование мероприятия' : 'Добавление мероприятия '}}</li>
        </ul>
    </div>
</div>
<div class="theme_block">
    <div class="container">
        <div class="title">
            <h2>{{\Illuminate\Support\Facades\Route::is('editRegmerop') ? 'Редактирование мероприятия' : 'Добавление мероприятия '}}</h2>
        </div>
        <form action="{{\Illuminate\Support\Facades\Route::is('editRegmerop') ? route('updateReg') : route('reg.add')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="reg_id" value="{{!empty($blog->id) ? $blog->id : ""}}">
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
                <input type="text" name="name" value="{{!empty($blog->name) ? $blog->name : ""}}" placeholder="Название мероприятия">
                <input type="datetime-local"  name="date" value="{{!empty($blog->date) ? $blog->date : ""}}" placeholder="Когда старт?">
            </div>
            <div class="name_post">
                <select id="author" name="author" {{ $user->group !== 'admin' ? 'disabled' : '' }}>
                    <option value="im" {{ old('author') == 'im' ? 'selected' : '' }}>Автор: Вы</option>
                    @if ($user->group === 'admin')
                    <option value="site" {{ old('author') == 'site' ? 'selected' : '' }}>Автор: Редакция ассоциации</option>
                    @endif
                </select>

                <select name="category_id">
                    <option>Выберите категорию</option>
                    @foreach ($categories as $item)
                    <option value="{{ $item->id }}" {{ old('category_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                    <p>Загрузите изображение</p>
                    <input type="file" name="image" id="fileInput" style="display:none" accept=".jpg,.jpeg,.png">
                </div>
            </div>

            <div id="fileList">
                @if(!empty($blog->image))
                    <h4>Ранее загруженные картинки</h4><br>
                    <div>
                        <img src="/img/blog/{{$blog->image}}" name="uploaded_image" style="max-width: 150px;">
                        <input type="hidden" name="uploaded_image" value="{{$blog->image}}">
                    </div>
                @endif

            </div>

            <textarea name="text" placeholder="Текст...">{{!empty($blogContent->text) ? $blogContent->text : ""}}</textarea><br>
            <div class="name_post">
                <input id="feedback" type="text" name="feedback" value="{{!empty($blog->feedback) ? $blog->feedback : ""}}" placeholder="Ссылка для кнопки 'Принять участие'">
            </div><br>
            <div style="display: flex">
                <div>
                    <input type="radio" class="btn-check" name="options-outlined" id="pay_for_other" autocomplete="off">
                    <label class="btn btn-outline-success" for="pay_for_other">Оплата отдельно <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                            <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                        </svg></label>
                </div>

                <div style="margin-left: 15px">
                    <input type="radio" class="btn-check" name="options-outlined" id="pay_for_subscription" autocomplete="off">
                    <label class="btn btn-outline-success" for="pay_for_subscription">За подписку <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19 " fill="currentColor" class="bi bi-journal-text" viewBox="0 0 16 16">
                            <path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                            <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                            <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                        </svg></label>
                </div>

                <div style="margin-left: 15px">
                    <input type="radio" class="btn-check" name="options-outlined" id="for_free" autocomplete="off">
                    <label class="btn btn-outline-success" for="for_free">Бесплатно <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                            <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                        </svg></label>
                </div>
            </div><hr>

            <input type="hidden" name="product_level" id="product_level" value="">

            <div class="name_post" id="pay_type_form">
                <div id="subscriptions" hidden="hidden">
                    <input type="hidden" id="checked_level" value="{{!empty($blog->product_level) ? $blog->product_level : ""}}">
                    @foreach ($products as $product)
                        <input type="radio" class="btn-check product-check" @if(!empty($blog->product_level) && $blog->product_level == $product->level) checked @else "" @endif value="{{ $product->level }}" name="options-outlined" data-row-id="{{ $product->id }}" id="{{ $product->name }}" autocomplete="off">
                        <label title="{{ $product->description }}" class="btn btn-outline-success" for="{{ $product->name }}">{{ $product->name }}: Цена: {{ $product->price }} Руб.</label>
                    @endforeach
                </div>

                <div id="pay" hidden="hidden">
                    <input type="text" name="practice" id="practice" value="{{!empty($blog->amount) ? $blog->amount : ""}}" placeholder="Стоимость">
                </div>
            </div><br>
            <button type="submit">{{\Illuminate\Support\Facades\Route::is('editRegmerop') ? 'Редактировать мероприятия' : 'Добавить мероприятие'}}</button>
        </form>
    </div>
</div>
<script>
    let practice = $("#practice").val();
    let product_level = $("#checked_level").val();

    if (product_level.length != "") {
        $("#subscriptions").removeAttr("hidden");
        $("#pay").attr("hidden", "hidden");
        //$("#pay_for_subscription").attr("checked", "checked");
    } else if (practice.value != "0" || practice.length != "") {
        $("#pay").removeAttr('hidden');
        $("#subscriptions").attr("hidden", "hidden");
        $("#pay_for_other").attr("checked", "checked");
        $("#product_level").val(practice.value);
    }

    let checkedProduct = 0;

    $("#pay_for_other").on("click", function () {
        $("#pay").removeAttr('hidden');
        $("#practice").val("")
        $("#subscriptions").attr("hidden", "hidden");
    });

    $("#pay_for_subscription").on("click", function () {
        $("#subscriptions").removeAttr("hidden");
        $("#pay").attr("hidden", "hidden");
    });

    $("#for_free").on("click", function () {
        $("#pay").attr("hidden", "hidden");
        $("#practice").val("free");
    });

    $(".product-check").on("click", function () {
        checkedProduct = $(this).val();
        $("#product_level").val(checkedProduct);
    });
</script>
<script>
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
    })
</script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/langs/ru.js" referrerpolicy="origin"></script>

<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/68cz9g6lyp34hqqoud1pb0russyz0zrzp1o8okddqdp8kkye/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
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
</script>
@endsection
