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
        <!-- <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">  -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/cdn.min.js" defer></script> -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script> -->
        <audio id="beep-sound" src="{{ asset('sound/beep.mp3') }}"></audio>
        <audio id="beep-error-sound" src="{{ asset('sound/beep-error.flac') }}"></audio>
        <audio id="error-sound" src="{{ asset('sound/error.wav') }}"></audio>

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
            <div id="sidebar" class="flex flex-col flex-grow">
            @include('components.sidebar')
            </div>
            <div id="content" class="flex flex-col w-full">
                <div class="navigation">
                    @include('layouts.navigation')
                </div>
                <main class="flex-1">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        @stack('scripts')
        <script>
            function adjustSidebarHeight() {
                const sidebar = document.getElementById('sidebar');
                const content = document.getElementById('content');
                sidebar.style.height = content.scrollHeight + 'px';
            }

            // Adjust sidebar height on load
            window.addEventListener('load', adjustSidebarHeight);

            // Adjust sidebar height on resize
            window.addEventListener('resize', adjustSidebarHeight);

            // Optionally, adjust sidebar height on content changes if dynamic content is added
            new MutationObserver(adjustSidebarHeight).observe(document.getElementById('content'), {
                childList: true,
                subtree: true
            });
        </script>
        
    </body>
</html>
