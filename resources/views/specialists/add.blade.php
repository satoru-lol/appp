@extends('app', [
    'title' => 'Присоединение к специалистам ассоциации',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="{{ route('home') }}">Главная <span>—</span></a></li>
            <li><a href="{{ route('specialists') }}">Список специалистов <span>—</span></a></li>
            <li>Присоединение к специалистам ассоциации</li>
        </ul>
    </div>
</div>
<div class="theme_block second_ht">
    <div class="container">
        <div class="title">
            <h2>Присоединение</h2>
        </div>

        @isset($error)
        <div class="alert alert-danger">{{ $error }}</div>
        @endisset

        @isset($success)
        <div class="alert alert-danger">{{ $error }}</div>
        @endisset

        @if (!isset($specialist->id))
        <form action="{{ route('specialists.add') }}" method="POST">
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
                <select name="category" class="form-select @error('category') border-danger @enderror" required>
                    <option>Выберите специальность</option>
                    @forelse ($categories as $item)
                    <option value="{{ $item->id }}" {{ old('category') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                    @empty
                    @endforelse
                </select>
                <select name="gender" class="form-select @error('gender') border-danger @enderror">
                    <option>Ваш пол</option>
                    <option value="0" {{ old('gender') == 0 ? 'selected' : '' }}>Мужской</option>
                    <option value="1" {{ old('gender') == 1 ? 'selected' : '' }}>Женский</option>
                </select>
            </div>
            
            <input type="text" name="birthday" id="birthday" value="{{ old('birthday') }}" class="@error('birthday') border-danger @enderror" placeholder="Дата рождения">

            <textarea name="about" placeholder="О себе" class="mb-0 @error('about') border-danger @enderror" oninput="aboutCount(this.value.length)">{{ old('about') }}</textarea>
            <div class="px-2 mb-3 mt-2 rounded-3" id="aboutCount"><span>0</span> / 250</div>

            <div class="name_post">
                <input type="text" name="degree" value="{{ old('degree') }}" class="@error('degree') border-danger @enderror" placeholder="Ваша степень">
                <input type="number" name="experience" value="{{ old('experience') }}" class="@error('experience') border-danger @enderror" placeholder="Стаж работы в годах, например: 12" min="1" max="40">
            </div>

            <div class="name_post">
                <input type="number" name="price[online]" value="{{ old('price.online') }}" class="@error('price.online') border-danger @enderror" placeholder="Стоимость онлайн">
                <input type="number" name="price[reception]" value="{{ old('price.reception') }}" class="@error('price.reception') border-danger @enderror" placeholder="Стоимость личного приема">
            </div>
            
            <div class="name_post">
                <input type="number" name="time[online]" value="{{ old('time.online') }}" class="@error('time.online') border-danger @enderror" placeholder="Время работы онлайн">
                <input type="number" name="time[reception]" value="{{ old('time.reception') }}" class="@error('time.reception') border-danger @enderror" placeholder="Время работы личного приема">
            </div>

            <div class="name_post">
                <input type="text" name="free_time" value="{{ old('free_time') }}" class="@error('location') border-danger @enderror" placeholder="Бесплатная консультация в минутах, например: 20" title="Бесплатная консультация в минутах, например: 20">
                <input type="text" name="location" value="{{ old('location') }}" class="@error('location') border-danger @enderror" placeholder="Локация, например: Россия, Москва">
            </div>

            <button type="submit">Присоединиться</button>
        </form>
        @else
        <div class="alert alert-success d-flex align-items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
            </svg>

            @if ($specialist->status === 0)
                <div>Ваша заявка отправлена, пожалуйста, ожидайте одобрений</div>
            @else
                <div>Вы уже находитесь в списке специалистов!</div>
            @endif
        </div>
        @endif
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.3/air-datepicker.min.css">
<script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.3/air-datepicker.min.js"></script>

<script>
    const airdate = new AirDatepicker('#birthday', {
        selectedDates: []
    })
    const birthday = document.getElementById('birthday')

    if(birthday.value != '') {
        const oldate = birthday.value.split('.')
        airdate.selectDate(new Date(oldate[2], parseInt(oldate[1]) - 1, oldate[0]))
    }

    birthday.addEventListener('change', (e) => {
        const birthday = e.target
        if(birthday.value != '') {
            const oldate = birthday.value.split('.')
            console.log(oldate)
            airdate.selectDate(new Date(oldate[2], parseInt(oldate[1]) - 1, oldate[0]))
        }
    })

    function aboutCount(count)
    {
        const about = document.querySelector('textarea[name="about"]')
        const aboutCount = document.getElementById('aboutCount')

        if(count >= 250) {
            aboutCount.querySelector('span').innerHTML = 250
            about.value = String(about.value).substr(0, 250)
            aboutCount.classList.add('text-danger', 'border', 'border-danger')
        } else {
            aboutCount.classList.remove('text-danger', 'border', 'border-danger')
            aboutCount.querySelector('span').innerText = count
        }
    }

    let getLocation
    
    function displayLocation(latitude, longitude) {
        var request = new XMLHttpRequest();

        var method = 'GET';
        var url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + latitude + ',' + longitude + '&sensor=true';
        var async = true;

        request.open(method, url, async);
        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                var data = JSON.parse(request.responseText);
                var address = data.results[0];
                getLocation = data;
            }
        };
        request.send();
    };

    var successCallback = function(position) {
        var x = position.coords.latitude;
        var y = position.coords.longitude;
        displayLocation(x, y);
    };

    var errorCallback = function(error) {
        var errorMessage = 'Unknown error';
        switch (error.code) {
            case 1:
                errorMessage = 'Permission denied';
                break;
            case 2:
                errorMessage = 'Position unavailable';
                break;
            case 3:
                errorMessage = 'Timeout';
                break;
        }
        getLocation = errorMessage
    };

    var options = {
        enableHighAccuracy: true,
        timeout: 1000,
        maximumAge: 0
    };

    navigator.geolocation.getCurrentPosition(successCallback, errorCallback, options);
</script>
@endsection