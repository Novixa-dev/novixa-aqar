<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', app()->getLocale() === 'ar' ? 'نوفيكسا عقار | Novixa Aqar - المنصة العقارية الذكية' : 'Novixa Aqar | Smart Real Estate Platform')</title>
    <meta name="description" content="{{ app()->getLocale() === 'ar' ? 'نوفيكسا عقار (Novixa Aqar) - المنصة العقارية الذكية لإيجار وشراء العقارات والأراضي والحلول العقارية المتقدمة، تطوير Novixa و AK01REDWAN.' : 'Novixa Aqar - Smart real estate management, listing, and AI valuation platform. Owned by Novixa & developed by AK01REDWAN.' }}">
    <meta property="og:site_name" content="Novixa Aqar">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    @stack('meta')

    @if(config('googletagmanager.id'))
        @include('googletagmanager::head')
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 selection:bg-blue-600 selection:text-white">
    @if(config('googletagmanager.id'))
        @include('googletagmanager::body')
    @endif

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col">
        @include('components.home-navbar')

        <main class="grow">
            @yield('content'){{ $slot ?? '' }}
        </main>

        @include('components.footer')
    </div>

    <!-- Chatbot Widget -->
    @include('components.chatbot-widget')

    <!-- Scripts -->
    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (toggle && menu) {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        menu.classList.toggle('hidden');
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
