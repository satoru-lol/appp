@extends('app', [
    'title' => 'Добавление встречи',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<style>
    .text-red-300{
        color: red;
        font-size: 14px;
    }
</style>
    <div class="bread_crumb">
        <div class="container">
            <ul>
                <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
                <li><a href="{{ route('ourMeetings') }}">Наши встречи <span>—</span></a></li>
                <li>{{\Illuminate\Support\Facades\Route::is('ourMeetings.edit') ? 'Редактирование встречи' : 'Добавление встречи '}}</li>
            </ul>
        </div>
    </div>
    <div class="theme_block">
        <div class="container">
            <div class="title">
                <h2>{{\Illuminate\Support\Facades\Route::is('ourMeetings.edit') ? 'Редактирование встречи' : 'Добавление встречи '}}</h2>
            </div>
            <form action="{{\Illuminate\Support\Facades\Route::is('ourMeetings.edit') ? route('ourMeetings.update') : route('ourMeetings.add')}}"
                  id="editOrAdd" method="POST" enctype="multipart/form-data">
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
                    <div>
                        <input type="text" name="name" id="name" value="{{!empty($blog->name) ? $blog->name : ""}}"
                               placeholder="Название встречи" required><br>

                        <p class="text-red-300" id="name_error" style="display: none"></p>
                    </div>

                    <div>
                        <input type="datetime-local" id="date" min="2025-01-01T00:00"
                               max="2030-12-31T23:59" name="date" value="{{!empty($blog->date) ? $blog->date : ""}}"
                               placeholder="Когда старт?"><br>

                        <p class="text-red-300" id="date_error" style="display: none"></p>
                    </div>


                </div>

                <div class="name_post">
                    <div>
                        <input type="text" name="fio" id="fio" value="{{!empty($blog->fio) ? $blog->fio : ""}}"
                               placeholder="ФИО организатора">
                        <p class="text-red-300" id="fio_error" style="display: none"></p>
                    </div>
                    <div>
                        <select name="format_id" required id="format_id">
                            <option value="0">Выберите формат встречи</option>
                            @foreach (\App\Models\MeetingFormat::all() as $item)
                                <option value="{{ $item->id }}" {{ !empty($blog->format_id) && $blog->format_id == $item->id ? 'selected' : '' }}>{{ $item->format }}</option>
                            @endforeach
                        </select>
                        <p class="text-red-300" id="format_error" style="display: none"></p>
                    </div>

                </div>
                <div class="name_post">
                    <input id="feedback" type="text" name="feedback"
                           value="{{!empty($blog->feedback) ? $blog->feedback : ""}}"
                           placeholder="Ссылка на мероприятие">

                    <input id="place_meeting" type="text" name="place_meeting"
                          value="{{!empty($blog->feedback) ? $blog->feedback : ""}}" placeholder="Место встречи">
                    <p class="text-red-300" id="feedback_error" style="display: none"></p>
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
                            <input type="hidden" name="uploaded_image" value="{{$blog->image}}"><br>
                            <a href="#" onclick="deleteImage('{{$blog->id}}')">Удалить картинку</a>
                        </div>
                    @endif
                </div>

                <div>
                    <div>Описание</label>
                    <div>
                    <textarea name="text" placeholder="Текст..."
                              id="description">{{!empty($blogContent->text) ? $blogContent->text : ""}}</textarea><br>
                              </div>
                </div>

                <div>
                    <label>Стоимость</label><br>
                    <input type="checkbox" id="free"
                           @if(!empty($blog) && ($blog->amount == 'free' || is_null($blog->amount))) checked @endif> Бесплатно

                    <br><br>
                </div>

                <input type="hidden" name="product_level" id="product_level" value="">

                <div class="name_post" id="pay_type_form">
                    <div id="subscriptions" hidden="hidden">
                        <input type="hidden" id="checked_level"
                               value="{{!empty($blog->product_level) ? $blog->product_level : ""}}">
                        @foreach ($products as $product)
                            <input type="radio" class="btn-check product-check"
                                   @if(!empty($blog->product_level) && $blog->product_level == $product->level) checked @else
                                ""
                            @endif value="{{ $product->level }}" name="options-outlined" data-row-id="{{ $product->id }}
                            " id="{{ $product->name }}" autocomplete="off">
                            <label title="{{ $product->description }}" class="btn btn-outline-success"
                                   for="{{ $product->name }}">{{ $product->name }}: Цена: {{ $product->price }}
                                Руб.</label>
                        @endforeach
                    </div>

                    <div id="pay"
                         @if(!empty($blog) && $blog->amount == 'free') hidden="hidden" @endif  @if(empty($blog)) @endif>
                        <input type="text" name="practice" id="practice"
                               value="{{!empty($blog->amount) ? $blog->amount : ""}}" placeholder="Укажите стоимость">
                        <p class="text-red-300" id="free_error" style="display: none"></p>
                        {{--                    <input type="text" name="explanation" id="explanation" value="{{!empty($blog->explanation) ? $blog->explanation : ""}}" placeholder="Пояснение">--}}

                    </div>
                </div>

                <div>
                    <label style="font-size: 18px;"> Количество участников</label><br>

                    <input type="checkbox" name="unlimited" id="unlimited"
                           @if (!empty($blog) && is_null($blog->quantity)) checked @endif> Без ограничения

                    <br><br>
                </div>
                <div class="name_post">
                    <div>
                        <input type="number" name="quantity" id="count_participant" placeholder="Количество участников"
                               value="{{$blog->quantity ?? '' }}">
                        <p class="text-red-300" id="unlimited_error" style="display: none"></p>
                    </div>

                </div>
                <button type="submit" style="padding: 15px"
                        id="send">{{\Illuminate\Support\Facades\Route::is('ourMeetings.edit') ? 'Сохранить изменения' : 'Добавить встречу'}}</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function (e) {
            e.preventDefault()
            document.getElementById('send').addEventListener('click', function (e) {
                e.preventDefault()
                /*if (document.getElementById('description').value === ''){
                    alert('Описание не может быть пустым!')
                    return false
                }*/
                let count = 0;
                if (document.getElementById('date').value === '') {
                    document.getElementById('date_error').style.display = 'block';
                    document.getElementById('date_error').innerText = 'Дата объязательно к заполнению!'
                    count++;
                }
                if (document.getElementById('name').value === '') {
                    document.getElementById('name_error').style.display = 'block';
                    document.getElementById('name_error').innerText = 'Название встречи объязательно к заполнению!'
                    count++;

                }
                if (document.getElementById('fio').value === '') {
                    document.getElementById('fio_error').style.display = 'block';
                    document.getElementById('fio_error').innerText = 'ФИО организатора объязательно к заполнению!'
                    count++;
                }
                if (document.getElementById('format_id').value === "0") {
                    document.getElementById('format_error').style.display = 'block';
                    document.getElementById('format_error').innerText = 'Формат встречи объязательно к заполнению!'
                    count++;
                }
                if (document.getElementById('free').checked === false && document.getElementById('practice').value === '') {
                    document.getElementById('free_error').style.display = 'block';
                    document.getElementById('free_error').innerText = 'Необходимо указать стоимость или указать что встреча бесплатно!'
                    count++;
                }
                if (document.getElementById('unlimited').checked === false && document.getElementById('count_participant').value === '') {
                    document.getElementById('unlimited_error').style.display = 'block';
                    document.getElementById('unlimited_error').innerText = 'Необходимо указать количество участников или указать что встреча без ограничения!'
                    count++;
                }
                if (count === 0) {
                    document.getElementById('editOrAdd').submit();
                }
            })
            let format_id = document.getElementById('format_id').value
            console.log(format_id)
            console.log(document.getElementById('free').value)
            console.log(document.getElementById('unlimited').value)

            document.getElementById('format_id').addEventListener('change', (event) => {
                let format_id = document.getElementById('format_id').value
                console.log('format', format_id)
                format(format_id)
            })
            format(format_id)
            let unlimit = document.getElementById('unlimited')
            if (unlimit.checked) {
                document.getElementById('count_participant').style.display = 'none'
            }
            let free = document.getElementById('free')
            if (free.checked) {
                document.getElementById('practice').style.display = 'none'
            }

            unlimit.addEventListener('change', (event) => {
                console.log('unlimit', unlimit.value)
                if (event.target.checked) {
                    document.getElementById('count_participant').style.display = 'none'
                } else {
                    document.getElementById('count_participant').style.display = 'block'
                }
            })


        })

        function format(format_id) {
            console.log('format', format_id)
            if (format_id === "1") {
                document.getElementById('place_meeting').style.display = 'none'
                document.getElementById('feedback').style.display = 'block'
                document.getElementById('feedback').setAttribute('required', 'required')
                document.getElementById('place_meeting').removeAttribute('required')
            } else if (format_id === "2") {
                document.getElementById('feedback').style.display = 'none'
                document.getElementById('place_meeting').style.display = 'block'
                document.getElementById('feedback').removeAttribute('required')
                document.getElementById('place_meeting').setAttribute('required', 'required')
            } else {
                document.getElementById('feedback').style.display = 'none'
                document.getElementById('place_meeting').style.display = 'none'
            }
        }

        let practice = document.getElementById('practice').value;
        let product_level = document.getElementById('checked_level').value;

        /*if (product_level.length != "") {
            $("#subscriptions").removeAttr("hidden");
            $("#pay").attr("hidden", "hidden");
            //$("#pay_for_subscription").attr("checked", "checked");
        } else if (practice.value != "0" || practice.length != "") {
            $("#pay").removeAttr('hidden');
            $("#subscriptions").attr("hidden", "hidden");
            $("#pay_for_other").attr("checked", "checked");
            $("#product_level").val(practice.value);
        }*/

        let checkedProduct = 0;
        let payForOther = document.getElementById('pay_for_other')
        let forFree = document.getElementById('for_free');
        /*payForOther.addEventListener('click', function(event) {
            document.getElementById('pay').removeAttribute('hidden')
            document.getElementById('practice').value=''
            // document.getElementById('subscriptions').setAttribute('hidden','hidden')
        })

        forFree.addEventListener('click',function (event){
            document.getElementById('pay').setAttribute('hidden','hidden')
            document.getElementById('practice').value='free'
            // document.getElementById('subscriptions').setAttribute('hidden','hidden')
        })*/

        /*$("#pay_for_other").on("click", function () {
            $("#pay").removeAttr('hidden');
            $("#practice").val("")
            $("#subscriptions").attr("hidden", "hidden");
        });

        $("#pay_for_subscription").on("click", function () {
            $("#subscriptions").removeAttr("hidden");
            $("#pay").attr("hidden", "hidden");
        });

        $("#for_free").on("click", function () {
            debugger;
            $("#pay").attr("hidden", "hidden");
            $("#practice").val("free");
        });

        $(".product-check").on("click", function () {
            checkedProduct = $(this).val();
            $("#product_level").val(checkedProduct);
        });*/
        const free = document.getElementById('free')
        free.addEventListener('change', (event) => {
            console.log(free)
            console.log(event.target.checked)
            if (event.target.checked) {
                document.getElementById('practice').style.display = 'none'
            } else {
                document.getElementById('practice').style.display = 'block'
            }
        })
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

        function deleteImage(fileContainer) {
            console.log('here', fileContainer);
            $.ajax({
                url: '/ourMeetings/deleteImage/' + fileContainer, // URL вашего маршрута
                type: 'GET',
                //contentType: 'application/json',
                data: {},
                success: function (response) {
                    document.getElementById('fileList').style.display = 'none';
                },
                error: function (xhr) {
                    alert('Произошла ошибка: ' + xhr.responseText); // Обработка ошибки
                }
            })
        }
    </script>


    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/langs/ru.js" referrerpolicy="origin"></script>

    <!-- Place the first <script> tag in your HTML's <head> -->
    <script src="https://cdn.tiny.cloud/1/68cz9g6lyp34hqqoud1pb0russyz0zrzp1o8okddqdp8kkye/tinymce/7/tinymce.min.js"
            referrerpolicy="origin"></script>

    <!-- Place the following <script> and <textarea> tags your HTML's <body> -->
    // <script>
    //     tinymce.init({
    //         selector: 'textarea',
    //         plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount linkchecker',
    //         toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    //         language: 'ru',
    //         content_style: `
    //   table {
    //     border-collapse: collapse;
    //     width: 100%;
    //   }
    //   table, th, td {
    //     border: 1px solid black;
    //   }
    //   th, td {
    //     padding: 8px;
    //     text-align: left;
    //   }
    // `
    //     });
    // </script>
@endsection
