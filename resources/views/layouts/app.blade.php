<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = trim($__env->yieldContent('title')) ?: ($title ?? $brand->title());
        $pageDescription = trim($__env->yieldContent('meta_description')) ?: ($metaDescription ?? $brand->description());
        $pageImage = trim($__env->yieldContent('og_image')) ?: (($ogImage ?? null) ?: $brand->ogImage());
        $isAr = app()->getLocale() === 'ar';
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($pageDescription), 300) }}">
    {{-- Pages like /properties and /news build filter-aware, genuinely
         distinct titles/descriptions per query string (status, page, search),
         so the canonical must reflect that same URL, not strip it down to
         the bare path -- a canonical pointing at the unfiltered page while
         the title/description claim unique content is self-contradicting. --}}
    <link rel="canonical" href="{{ url()->full() }}">
    @unless ($brand->indexable())
        {{-- Demo / staging: keep sample data out of search results. --}}
        <meta name="robots" content="noindex, nofollow">
    @endunless
    <link rel="icon" href="{{ asset('images/logo.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="{{ $brand->themeColor() }}">
    <meta name="author" content="{{ $brand->company() }}">
    <meta property="og:site_name" content="{{ $brand->name() }}">
    <meta property="og:type" content="{{ trim($__env->yieldContent('og_type')) ?: ($ogType ?? 'website') }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($pageDescription), 300) }}">
    <meta property="og:url" content="{{ url()->full() }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:locale" content="{{ $isAr ? 'ar_YE' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ $isAr ? 'en_US' : 'ar_YE' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($pageDescription), 200) }}">
    <meta name="twitter:image" content="{{ $pageImage }}">
    <meta name="google" content="notranslate">
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
        /* Top Loading Bar — anchored to the reading-start edge (insets, not
           left/right) so it grows in the correct visual direction for both
           LTR (left→right) and RTL (right→left) instead of always sliding
           out from the physical left edge regardless of page direction. */
        #top-progress-bar {
            position: fixed;
            top: 0;
            inset-inline-start: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(to right, #7A6759, #8C7869, #A38F7F, #D5C7BC);
            z-index: 99999;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease;
            box-shadow: 0 0 10px rgba(140, 120, 105, 0.7);
        }
        html[dir="rtl"] #top-progress-bar {
            background: linear-gradient(to left, #7A6759, #8C7869, #A38F7F, #D5C7BC);
        }
    </style>
    <!-- Theme Initialization Script (Prevent Flash) -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        window.toggleTheme = function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: document.documentElement.classList.contains('dark') } }));
        };
    </script>
</head>
<body class="font-sans antialiased bg-[#FAF8F5] dark:bg-[#141211] text-[#26201D] dark:text-stone-100 selection:bg-[#26201D] selection:text-white transition-colors duration-200">
    <!-- Top Progress Bar Element -->
    <div id="top-progress-bar"></div>

    @if(config('googletagmanager.id'))
        @include('googletagmanager::body')
    @endif

    <div class="min-h-screen bg-[#FAF8F5] dark:bg-[#141211] flex flex-col">
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
