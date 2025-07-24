<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Панель администратора') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

{{--    <script src="https://cdn.tailwindcss.com"></script>--}}
    <script src="{{asset('js/tailwind.js')}}"></script>
    <script src="{{asset('js/jquery.min.js')}}"></script>

    <link rel="stylesheet" href="/css/admin/app.min.css">
    <link rel="stylesheet" href="/css/icons.min.css">

    @stack("scripts")
</head>

<body>
    <div class="relative isolate flex min-h-svh w-full bg-white max-lg:flex-col lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        @include('admin.layouts.nav')
        @include('admin.layouts.header')
        <main class="flex flex-1 flex-col pb-2 lg:min-w-0 lg:pl-64 lg:pr-2 lg:pt-2">
            @yield('content')
        </main>
    </div>
    @stack("scripts")
</body>
</html>
