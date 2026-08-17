@props([
    'user' => null,
    'role' => null,
    'dashboardUrl' => null,
])
@if (auth()->check())
    @php
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';
        $dashboardUrl = match($role) {
            'super_admin', 'admin' => '/admin',
            'staff', 'agent' => '/staff',
            'buyer' => '/buyer',
            'tenant' => '/tenant',
            'landlord' => '/landlord',
            'seller' => '/seller',
            'contractor' => '/contractor',
            default => '/admin',
        };
    @endphp
@endif
<header class="sticky top-0 z-40 w-full bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 transition-all duration-200">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo & Identity -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto rounded-xl shadow-xs transition-transform group-hover:scale-105" alt="Al-Ofuq Real Estate" />
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-tight text-gray-900 dark:text-white flex items-center gap-1.5">
                        {{ app()->getLocale() === 'ar' ? 'شركة الأفق العقارية' : 'Al-Ofuq Real Estate' }}
                    </span>
                    <span class="text-[11px] font-medium text-blue-600 dark:text-blue-400">
                        {{ app()->getLocale() === 'ar' ? 'أفق اليمن للعقارات والاستثمار' : 'Yemen Horizon Properties' }}
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="hidden lg:flex items-center gap-8 font-medium">
                {!! app(App\Services\MenuService::class)->buildMenu() !!}
            </nav>

            <!-- Actions: Language Switcher + Auth -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button (Light/Dark) -->
                <button type="button" 
                        onclick="window.toggleTheme()" 
                        class="p-2 rounded-full text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 transition-all focus:outline-none cursor-pointer"
                        title="{{ app()->getLocale() === 'ar' ? 'تبديل المظهر (فاتح / داكن)' : 'Toggle Theme (Light / Dark)' }}"
                        aria-label="Toggle Theme">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg class="w-4 h-4 block dark:hidden text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

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

                <!-- Auth Buttons / User Dropdown -->
                @if (auth()->check())
                    <div class="relative dropdown" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button" 
                                id="userDropdownBtn"
                                @click="open = !open"
                                onclick="const m = document.getElementById('userDropdownMenu'); if (m) { m.classList.toggle('hidden'); }"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                            <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:inline">{{ $user->name }}</span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userDropdownMenu"
                             x-show="open" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="hidden absolute end-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 py-2 z-50 dropdown-menu">
                            <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'تم تسجيل الدخول كـ' : 'Signed in as' }}</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ $user->email }}</p>
                            </div>
                            <a href="{{ $dashboardUrl }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
                            </a>
                            <a href="/user/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}</span>
                            </a>
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 text-start transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log out' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
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
                @auth
                    <div class="flex flex-col gap-2 pt-4 mt-2 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ $dashboardUrl }}" class="w-full text-center py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl">
                            {{ __('Dashboard') }}
                        </a>
                        <a href="/user/profile" class="w-full text-center py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 rounded-xl">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center py-2.5 text-sm font-semibold text-red-600 bg-red-50 dark:bg-red-900/20 rounded-xl">
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                @else
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
                @endauth
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
