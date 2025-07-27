@component('mail::message')

<p>Пароль для входа в личный кабинет:</p>
<h3><b>Ваш пароль: </b>{{$password}}</h3>

С уважением,<br>
{{ config('app.name') }}
@endcomponent
