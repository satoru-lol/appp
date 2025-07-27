@extends('app', [
    'title' => 'Бонусная программа',
    'keywords' => '', # Ключевые слова
    'description' => '' # Описание страницы
])

@section('content')
<div class="bread_crumb">
    <div class="container">
        <ul>
            <li><a href="/">Главная <span>—</span></a></li>
            <li>Бонусная программа</li>
        </ul>
    </div>
</div>
<div class="blog_bonus">
    <div class="container">
        <div class="title_bonus">
            <h3>У вас <span>{{ $bonus->value ?? 0 }}</span> <img src="img/bonus_b.svg" alt=""> бонусов</h3>
            <p>Чтобы поощрить вас за вклад в развитие психологической общины, мы предлагаем систему баллов
                и вознаграждений, которая позволяет накапливать баллы за участие в мероприятиях, обучающих программах и других активностях ассоциации, а затем обменивать их на ценные бонусы.</p>
        </div>
    </div>
</div>
<div class="history_block">
    <div class="container">
        <div class="title_g">
            <h4>История бонусов</h4>
        </div>
        <div class="block_history">
            @forelse ($bonus_histories as $item)
            <div class="item">
                <div class="left_title">
                    <h4>{{ $item->text }}</h4>
                    <p>{{ $item->created_at }}</p>
                </div>
                <div class="right_b">
                    <h5>{{ $item->bonus < 0 ? '-' : '+' }} {{ str_replace('-', '', $item->bonus) }} <img src="img/bonus_b.svg" alt=""></h5>
                </div>
            </div>
            @empty
            <div class="alert alert-warning">
                Отсутствуют
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection