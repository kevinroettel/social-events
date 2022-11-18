<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="preload" href="{{ asset('css/app.css') }}" as="style">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            @unless (Auth::check())
                {{-- @include('auth.login') --}}
                @yield('content')
            @else
                <div>
                    <p>Aye, {{ Auth::user()->username }}</p>

                    <div class="logout-link">
                        <a 
                            class="nav-link"
                            href="{{ route('logout') }}"
                            onclick="
                                event.preventDefault(); 
                                document.getElementById('logout-form').submit();
                            "
                        >
                            Logout
                        </a>
    
                        <form 
                            id="logout-form" 
                            action="{{ route('logout') }}" 
                            method="POST" 
                            style="display: none;"
                        >
                            @csrf
                        </form>
                    </div>
                </div>
            @endif

            {{-- @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/home') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                        @endif
                    @endauth
                </div>
            @endif --}}
        </div>
    </body>
</html>
