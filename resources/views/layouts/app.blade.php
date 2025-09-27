<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="{{ Vite::asset('resources/images/favicon_dark.svg') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300;1,400;1,500&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=DM+Serif+Display:ital@0;1&family=DM+Serif+Text:ital@0;1&display=swap"
        rel="stylesheet">

    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="font-sans antialiased bg-background dark:bg-background text-text dark:text-text {{ session()->has('theme') === 'dark' ? 'dark' : '' }}">
    <div class="relative">
        <x-guest-navbar />

        <!-- Page Content -->
        <main class="min-h-screen">
            {{ $slot }}
        </main>

        <x-footer />
    </div>

    @livewireScripts

    <!-- Script aggiuntivo per gestire il cambio tema -->
    <script>
        // Funzione per applicare il tema
        function applyTheme(isDark) {
            const body = document.body;

            body.classList.toggle('dark');
        }

        // Ascolta gli eventi Livewire per il cambio tema
        document.addEventListener('livewire:init', () => {
            Livewire.on('theme-changed', (event) => {
                applyTheme(event.isDark);

                // Opzionale: salva anche in localStorage per persistenza
                if (!event.isDark) {
                    localStorage.setItem('theme-preference', 'dark');
                } else {
                    localStorage.removeItem('theme-preference');
                }
            });
        });

        // Al caricamento della pagina, controlla se c'è una preferenza salvata
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme-preference');
            const sessionDark = {{ session()->has('theme') && session('theme') === 'dark' ? 'true' : 'false' }};

            applyTheme(savedTheme === 'dark' || sessionDark);
        });
    </script>
</body>

</html>
