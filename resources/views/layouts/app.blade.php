<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @wireUiStyles



    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    

    @livewireStyles
    

</head>

<body class="font-sans antialiased">
    <x-banner />
    <x-wireui-notifications />
    <x-wireui-dialog />
    <livewire:flash-notifications.flash-notifications />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts
    @wireUiScripts
    <script>
        const sliders = {};

        function nextSlide(id) {
            const slider = document.querySelector(`#${id} > .flex`);
            if (!slider) return;

            const slides = slider.children.length;
            sliders[id] = (sliders[id] ?? 0) + 1;
            if (sliders[id] >= slides) sliders[id] = 0;

            slider.style.transform = `translateX(-${sliders[id] * 96}px)`;
        }

        function prevSlide(id) {
            const slider = document.querySelector(`#${id} > .flex`);
            if (!slider) return;

            const slides = slider.children.length;
            sliders[id] = (sliders[id] ?? 0) - 1;
            if (sliders[id] < 0) sliders[id] = slides - 1;

            slider.style.transform = `translateX(-${sliders[id] * 96}px)`;
        }
    </script>
    <script>
        function markNotificationRead(id) {
            fetch('/notifications/read/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(() => location.reload());
        }
    </script>

    <x-notifications />

</body>

</html>
