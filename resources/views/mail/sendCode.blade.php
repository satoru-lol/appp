@component('mail::message')

<p>Код подтверждения входа в акканут:</p>
<h3><b>Ваш код: </b>{{$code}}</h3>

С уважением,<br>
{{ config('app.name') }}
@endcomponent
