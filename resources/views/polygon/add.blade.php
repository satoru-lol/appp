@extends('app', [
    'title' => 'Добавление полигона',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('courses') }}">Полигон <span>—</span></a></li>
            <li>{{\Illuminate\Support\Facades\Route::is('editPolygon') ? 'Редактирование полигона' : 'Добавление полигона'}}</li>
        </ul>
    </div>
</div>
<div class="theme_block second_ht">
    <div class="container">
        <div class="title">
            <h2>{{\Illuminate\Support\Facades\Route::is('editPolygon') ? 'Редактирование полигона' : 'Добавление полигона'}}</h2>
        </div>
<form method='post' action="{{\Illuminate\Support\Facades\Route::is('editPolygon') ? route('updatePolygon') : route('polygon.add')}}" enctype="multipart/form-data">
@csrf
<input type="hidden" name="course_id" value="{{!empty($course->id) ? $course->id : ""}}">
 @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
            <div class="name_post">
                <input type="text" name="title" value="{{!empty($course->title) ? $course->title : ""}}" placeholder="Название статьи">
                <input type="text" name="times[read]" value="{{!empty($course->times) ? json_decode($course->times, true)["read"] : ""}}" placeholder="Время чтения">
            </div>
            <div class="name_post">
                <input type="text"  name="times[training]" value="{{!empty($course->times) ? json_decode($course->times, true)["training"] : ""}}" placeholder="Срок обучения">
                <input type="text"  name="times[start]" value="{{!empty($course->times) ? json_decode($course->times, true)["start"] : ""}}" placeholder="Когда старт?">
            </div>
			 {{--<div class="name_post">
                <select name="course_category_id">
                    @foreach(\App\Models\CourseCategory::all() as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>--}}
            <div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                    <p>Загрузите изображение</p>
                    <input type="file" name="image" id="fileInput" style="display:none" accept=".jpg,.jpeg,.png">
                </div>
            </div>
    <div id="fileList">
        @if(!empty($course->image))
            <h4>Ранее загруженные картинки</h4><br>
            <div>
                <img src="/images/{{$course->image}}" name="uploaded_image" style="max-width: 150px;">
                <input type="hidden" name="uploaded_image" value="{{$course->image}}">
            </div>
        @endif
    </div>

    <br>
            <div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('videoInput').click()">
                    <p>Загрузите видео</p>
                    <input type="file" name="video" id="videoInput" style="display:none" {{--accept=".video/mp4"--}}>
                </div>
            </div>
    <div id="videoList"></div><br>

            <textarea name="desc" placeholder="Описание полигона">{{!empty($course->text) ? $course->text : ""}}</textarea>

            <!--<input type="text" name="content[characteristics1]" placeholder="Характеристика 1">
            <input type="text" name="content[characteristics2]" placeholder="Характеристика 2">
            <input type="text" name="content[characteristics3]" placeholder="Характеристика 3">
            <input type="text" name="content[characteristics4]" placeholder="Характеристика 4">
            <input type="text" name="content[characteristics5]" placeholder="Характеристика 5 ">

            <input type="text" name="content[teach1][title]" placeholder="Чему вас научит курс (заголовок 1)">
            <input type="text" name="content[teach1][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">
            <input type="text" name="content[teach2][title]" placeholder="Чему вас научит курс (заголовок 2)">
            <input type="text" name="content[teach2][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">
            <input type="text" name="content[teach3][title]" placeholder="Чему вас научит курс (заголовок 3)">
            <input type="text" name="content[teach3][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">
            <input type="text" name="content[teach4][title]" placeholder="Чему вас научит курс (заголовок 4)">
            <input type="text" name="content[teach4][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">
            <input type="text" name="content[teach5][title]" placeholder="Чему вас научит курс (заголовок 5)">
            <input type="text" name="content[teach5][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">
            <input type="text" name="content[teach6][title]" placeholder="Чему вас научит курс (заголовок 6)">
            <input type="text" name="content[teach3][subtitle]" placeholder="Чему вас научит курс (подзаголовок)">-->
			<br><input type="text" name="speakers" value="{{!empty($course->speakers) ? $course->speakers : ""}}" placeholder="Спикеры полигона">

            <input type="text" name='theory' value="{{!empty($course->theory) ? $course->theory : ""}}" placeholder="Формат">


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


            <div id="pay_type_form">
                <div id="subscriptions" hidden="hidden">
                    <input type="hidden" id="checked_level" value="{{!empty($course->product_level) ? $course->product_level : ""}}">
                    @foreach ($products as $product)
                        <input type="radio" class="btn-check product-check" @if(!empty($course->product_level) && $course->product_level == $product->level) checked @else "" @endif value="{{ $product->level }}" name="options-outlined" data-row-id="{{ $product->id }}" id="{{ $product->name }}" autocomplete="off">
                        <label title="{{ $product->description }}" class="btn btn-outline-success" for="{{ $product->name }}">{{ $product->name }}: Цена: {{ $product->price }} Руб.</label>
                    @endforeach
                </div>

                <div id="pay" hidden="hidden">
                    <input type="text" name="practice" id="practice" value="{{!empty($course->practice) ? $course->practice : ""}}" placeholder="Стоимость">
                </div>
            </div><br>

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

            <input type="hidden" name="product_level" id="product_level" value="">
{{--
            <input type="text" name='feedback' value="{{!empty($course->feedback) ? $course->feedback : ""}}" placeholder="Ссылка для кнопки 'Записаться на курс'">
--}}
            <!--<div class="file-upload-container">
                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                    <p>Добавить диплом/сертификат</p>
                    <input type="file" id="fileInput" multiple style="display:none" accept=".jpg,.jpeg,.png,.pdf,.gif" onchange="handleFiles(this.files)">
                </div>
            </div>-->
           <!-- <div id="fileList"></div>
            <input type="text" placeholder="Вопрос для FAQ">
            <input type="text" placeholder="Ответ">
            <a href="#">Добавить вопрос</a>-->
            <button>{{\Illuminate\Support\Facades\Route::is('editPolygon') ? 'Редактировать полигон' : 'Добавить полигон'}}</button>
        </form>

    </div>
</div>
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
