<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app(\App\Settings\GeneralSettings::class)->site_name }}</title>
    <meta name="description" content="عقاري - المنصة العقارية الرائدة لشراء، بيع، وإيجار العقارات والأراضي في المكلا وحضرموت واليمن.">
    <meta property="og:site_name" content="{{ app(\App\Settings\GeneralSettings::class)->site_name }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    @stack('meta')

    @if(config('googletagmanager.id'))
        @include('googletagmanager::head')
    @endif

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    @if(config('googletagmanager.id'))
        @include('googletagmanager::body')
    @endif

    <div class="min-h-screen bg-gray-100 flex flex-col">
        @include('components.home-navbar')

        <main class="grow">
            @yield('content'){{ $slot ?? '' }}
        </main>

        @include('components.footer')
    </div>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')

    <!-- Scripts -->
    @vite('resources/js/app.js')
    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    menu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>
