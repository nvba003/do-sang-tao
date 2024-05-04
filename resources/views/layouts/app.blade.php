<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Dosangtao') }}</title>
        <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"> -->

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/inventory.css') }}"> 

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="//cdn.jsdelivr.net/npm/alpinejs" defer></script>

        <!-- CSS -->
        <!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> -->


    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex flex-col md:flex-row">
                @include('components.sidebar')
                <div class="content w-full">
                <div class="navigation">
                    @include('layouts.navigation')
                </div>
                    <main class="flex-1">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!-- Bootstrap JS -->
        <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script> -->

        @stack('scripts')
        
    </body>
</html>
