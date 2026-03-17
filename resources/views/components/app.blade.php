<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen" data-theme="light">
    {{-- <h1>{{ config('app.name') }}</h1> --}}
    {{-- <div>
        @guest
            <a href="{{ route('login') }}">[Login]</a>
        @endguest
        @auth
            <a href="{{ route('auth.logout') }}">[Logout]</a>
        @endauth
    </div> --}}
    {{-- <hr /> --}}
    {{ $slot }}
</body>

</html>
