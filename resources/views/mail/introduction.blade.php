@component('mail::message')

<p>Чтобы подтвердить вступление в ассоциацию, нажмите на кнопку подтвердить:</p>
<p><a href="{{ $url }}" style="box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
    background-color: #2d3748;
    border-bottom: 8px solid #2d3748;
    border-left: 18px solid #2d3748;
    border-right: 18px solid #2d3748;
    border-top: 8px solid #2d3748;">Подтвердить</a></p>

<p><small>Если у вас не работает кнопка, то перейдите по ссылке: <a href="{{ $url }}">{{ $url }}</a></small></p>

С уважением,<br>
{{ config('app.name') }}
@endcomponent
