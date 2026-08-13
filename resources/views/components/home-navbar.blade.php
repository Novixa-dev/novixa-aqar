@props([
    'user' => null,
    'role' => null,
    'dashboardUrl' => null,
])
@if (auth()->check())
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';
        $dashboardUrl = $role === 'admin' ? '/admin' : '/' . $role;
    @endphp
@endif
<header class="sticky top-0 z-40 w-full bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-all duration-200">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo & Identity -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto rounded-xl shadow-xs transition-transform group-hover:scale-105" alt="Novixa Aqar" />
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-1.5">
                        {{ app()->getLocale() === 'ar' ? 'نوفيكسا عقار' : 'Novixa Aqar' }}
                    </span>
                    <span class="text-[11px] font-medium text-blue-600 dark:text-blue-400">
                        {{ app()->getLocale() === 'ar' ? 'المنصة العقارية الذكية' : 'Smart Real Estate' }}
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden lg:flex items-center gap-8 font-medium">
                {!! app(App\Services\MenuService::class)->buildMenu() !!}
            </nav>

            <!-- Actions: Language Switcher + Auth -->
            <div class="flex items-center gap-3">
                <!-- Language Switcher Toggle Pill -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-800 p-1 rounded-full border border-gray-200 dark:border-gray-700 shadow-inner">
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" 
                           class="flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                           title="Switch to English">
                            <span>🇬🇧</span>
                            <span>English</span>
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-gray-700 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                           title="التحويل إلى العربية">
                            <span>🇸🇦</span>
                            <span>العربية</span>
                        </a>
                    @endif
                </div>

                <!-- Auth Buttons -->
                @if (auth()->check())
                    <a href="{{ $dashboardUrl }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-blue-600 dark:text-gray-200 dark:hover:text-blue-400 rounded-xl transition-colors">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}"
                            class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-sm hover:shadow transition-all">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endif

                <!-- Mobile Menu Button -->
                <button id="menuToggleButton" type="button"
                    class="inline-flex items-center justify-center p-2.5 text-gray-500 rounded-xl lg:hidden hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 focus:outline-none transition-colors"
                    aria-label="Toggle navigation">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div class="hidden lg:hidden py-4 border-t border-gray-100 dark:border-gray-800" id="menuToggle">
            <div class="flex flex-col gap-2">
                {!! app(App\Services\MenuService::class)->buildMenu() !!}
                @guest
                    <div class="flex flex-col gap-2 pt-4 mt-2 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                            class="w-full text-center py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}"
                            class="w-full text-center py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('menuToggleButton');
        const mobileMenu = document.getElementById('menuToggle');
        if (toggleBtn && mobileMenu) {
            toggleBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
