<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Events Base</title>

        <link rel="preload" href="{{ asset('css/app.css') }}" as="style">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <link rel="preload" href="{{ asset('js/app.js') }}" as="script">
        <script src="{{ asset('js/app.js') }}" defer></script>
        
    </head>
    <body class="antialiased">
        <div class="website-content">
            @unless (Auth::check())
                @include('layouts.guest')
            @else
                <div id="app">
                    <app
                        currentuser="{{ Auth::user() }}"
                    ></app>
                </div>
            @endif
        </div>
    </body>
</html>
