<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', app()->getLocale() === 'ar' ? 'شركة الأفق العقارية | أفق اليمن للعقارات والاستثمار' : 'Al-Ofuq Real Estate | Yemen Horizon Properties')</title>
    <meta name="description" content="{{ app()->getLocale() === 'ar' ? 'شركة الأفق العقارية - بوابتك الأولى للتملك والاستثمار العقاري في اليمن (حضرموت، صنعاء، عدن). وساطة عقارية، إدارة أملاك، وتثمين ذكي.' : 'Al-Ofuq Real Estate - Your premier gateway for real estate investment and ownership in Yemen. Property brokerage, management, and smart valuation.' }}">
    <meta property="og:site_name" content="Al-Ofuq Real Estate | شركة الأفق العقارية">
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

    <style>
        /* Top Loading Bar */
        #top-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #2563eb, #3b82f6, #60a5fa, #93c5fd);
            z-index: 99999;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.7);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 selection:bg-blue-600 selection:text-white">
    <!-- Top Progress Bar Element -->
    <div id="top-progress-bar"></div>

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
        // Page Navigation Loading Indicator
        window.startLoading = function() {
            const bar = document.getElementById('top-progress-bar');
            if (bar) {
                bar.style.opacity = '1';
                bar.style.width = '30%';
                setTimeout(() => { bar.style.width = '70%'; }, 150);
            }
        };

        window.finishLoading = function() {
            const bar = document.getElementById('top-progress-bar');
            if (bar) {
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.opacity = '0';
                    setTimeout(() => { bar.style.width = '0%'; }, 300);
                }, 200);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            finishLoading();

            // Intercept internal links for top bar effect
            document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"])').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                        startLoading();
                    }
                });
            });

            // Livewire hooks
            if (window.Livewire) {
                window.Livewire.hook('commit', ({ succeed, fail }) => {
                    startLoading();
                    succeed(() => finishLoading());
                    fail(() => finishLoading());
                });
            }

            // Close dropdowns on outside click
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown') && !e.target.closest('#userDropdownMenu') && !e.target.closest('#userDropdownBtn')) {
                    document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
                    const userMenu = document.getElementById('userDropdownMenu');
                    if (userMenu) userMenu.classList.add('hidden');
                }
            });
        });

        window.addEventListener('pageshow', function() {
            finishLoading();
        });
    </script>

    @stack('scripts')
</body>
</html>
