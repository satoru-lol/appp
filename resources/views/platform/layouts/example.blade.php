@extends('platform::layouts.app')

@section('content')
    <div class="container">
        {!! $screen->layout !!}
    </div>
@endsection

@section('scripts')
    <script>
        {!! $script !!}
    </script>
@endsection
