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

<header class="sticky top-0 z-50 w-full bg-[#211C19]/90 backdrop-blur-md border-b border-white/10 text-white transition-all duration-200">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Far Right (RTL): Brand Logo & Symbol -->
            <a href="/" class="flex items-center gap-3 group shrink-0">
                <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center font-black text-xl shadow-md group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-xl font-extrabold tracking-tight text-white font-sans">
                        {{ app()->getLocale() === 'ar' ? 'رســــو' : 'RUSU' }}
                    </span>
                    <span class="text-[9px] font-bold uppercase tracking-widest text-[#C2B2A3]">
                        REAL ESTATE
                    </span>
                </div>
            </a>

            <!-- Center Navigation Links -->
            <nav class="hidden md:flex items-center gap-1 lg:gap-1.5 xl:gap-2 text-[13px] lg:text-sm font-bold">
                <a href="{{ url('/') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->is('/') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                </a>
                <a href="{{ route('property.list') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('property*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'العقارات' : 'Properties' }}
                </a>
                <a href="{{ route('services') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('services*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our Services' }}
                </a>
                <a href="{{ route('calculators') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('calculators*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الحاسبات العقارية' : 'Calculators' }}
                </a>
                <a href="{{ route('news') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('news*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}
                </a>
                <a href="{{ route('about') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('about*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'عن الشركة' : 'About Us' }}
                </a>
                <a href="{{ route('contact') }}" class="px-3 py-1.5 lg:px-3.5 xl:px-4 lg:py-2 rounded-full whitespace-nowrap transition-all {{ request()->routeIs('contact*') ? 'bg-[#8C7869] text-white shadow-sm' : 'text-stone-200 hover:text-white hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                </a>
            </nav>

            <!-- Far Left (RTL): Phone + Contact Button + Mobile Menu -->
            <div class="flex items-center gap-3">

                <!-- Contact CTA Pill Button (Taupe style matching mockup) -->
                <a href="{{ route('contact') }}" class="hidden 2xl:inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-[#8C7869] hover:bg-[#7A6759] text-white text-xs font-bold shadow-md transition-all active:scale-95 border border-white/10">
                    {{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}
                </a>

                <!-- Theme Toggle Button -->
                <button type="button" 
                        onclick="window.toggleTheme()" 
                        class="p-2 rounded-full text-stone-300 hover:text-white bg-white/10 hover:bg-white/20 border border-white/15 transition-all focus:outline-none cursor-pointer"
                        title="{{ app()->getLocale() === 'ar' ? 'تبديل المظهر' : 'Toggle Theme' }}">
                    <svg class="w-4 h-4 hidden dark:block text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-4 h-4 block dark:hidden text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Language Switcher Toggle Pill -->
                <div class="flex items-center bg-white/10 p-1 rounded-full border border-white/15">
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" 
                           class="px-2.5 py-0.5 text-xs font-bold text-stone-200 hover:text-white transition-colors">
                            <span>EN</span>
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" 
                           class="px-2.5 py-0.5 text-xs font-bold text-stone-200 hover:text-white transition-colors">
                            <span>عربي</span>
                        </a>
                    @endif
                </div>

                <!-- Auth / User Dropdown -->
                @if (auth()->check())
                    <div class="relative dropdown" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button type="button" 
                                id="userDropdownBtn"
                                @click="open = !open"
                                onclick="const m = document.getElementById('userDropdownMenu'); if (m) { m.classList.toggle('hidden'); }"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-white bg-white/10 hover:bg-white/20 rounded-full border border-white/15 transition-colors cursor-pointer">
                            <div class="w-6 h-6 rounded-full bg-[#8C7869] text-white flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:inline">{{ $user->name }}</span>
                            <svg class="w-3.5 h-3.5 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="userDropdownMenu"
                             x-show="open" 
                             x-cloak
                             class="hidden absolute end-0 mt-2 w-56 bg-[#211C19] text-white rounded-2xl shadow-2xl border border-white/15 py-2 z-50 dropdown-menu">
                            <div class="px-4 py-2.5 border-b border-white/10">
                                <p class="text-xs font-medium text-stone-400">{{ app()->getLocale() === 'ar' ? 'مسجل كـ' : 'Signed in as' }}</p>
                                <p class="text-xs font-bold text-white truncate">{{ $user->email }}</p>
                            </div>
                            <a href="{{ $dashboardUrl }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-stone-200 hover:bg-white/10 transition-colors">
                                <span>{{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard' }}</span>
                            </a>
                            <a href="{{ route('wishlist') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-stone-200 hover:bg-white/10 transition-colors">
                                <span>{{ app()->getLocale() === 'ar' ? 'المفضلة' : 'Wishlist' }}</span>
                            </a>
                            <a href="/user/profile" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-stone-200 hover:bg-white/10 transition-colors">
                                <span>{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}</span>
                            </a>
                            <div class="border-t border-white/10 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-400 hover:bg-white/10 text-start transition-colors">
                                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل الخروج' : 'Log out' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                            class="px-4 py-2 text-xs font-bold text-stone-200 hover:text-white transition-colors">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}"
                            class="px-4 py-2 text-xs font-bold text-white bg-[#8C7869] hover:bg-[#7A6759] rounded-full shadow-sm transition-all">
                            {{ __('Register') }}
                        </a>
                    </div>
                @endif

                <!-- Hamburger Menu Button -->
                <button id="menuToggleButton" type="button"
                    class="inline-flex md:hidden items-center justify-center p-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white focus:outline-none transition-colors"
                    aria-label="Toggle navigation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div class="hidden md:hidden py-4 border-t border-white/10 text-white" id="menuToggle">
            <div class="flex flex-col gap-2 font-bold text-sm">
                <a href="{{ url('/') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->is('/') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}
                </a>
                <a href="{{ route('property.list') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('property*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'العقارات' : 'Properties' }}
                </a>
                <a href="{{ route('services') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('services*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'خدماتنا' : 'Our Services' }}
                </a>
                <a href="{{ route('calculators') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('calculators*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الحاسبات العقارية' : 'Calculators' }}
                </a>
                <a href="{{ route('news') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('news*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'الأخبار' : 'News' }}
                </a>
                <a href="{{ route('about') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('about*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'عن الشركة' : 'About Us' }}
                </a>
                <a href="{{ route('contact') }}" class="py-2.5 px-4 rounded-xl transition-colors {{ request()->routeIs('contact*') ? 'bg-[#8C7869] text-white' : 'text-stone-200 hover:bg-white/10' }}">
                    {{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'Contact Us' }}
                </a>
                @auth
                    <div class="flex flex-col gap-2 pt-4 border-t border-white/10">
                        <a href="{{ $dashboardUrl }}" class="w-full text-center py-2.5 text-xs font-bold text-white bg-[#8C7869] rounded-full">
                            {{ __('Dashboard') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center py-2.5 text-xs font-bold text-red-400 bg-white/10 rounded-full">
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col gap-2 pt-4 border-t border-white/10">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                            class="w-full text-center py-2.5 text-xs font-bold text-stone-200 bg-white/10 rounded-full">
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}"
                            class="w-full text-center py-2.5 text-xs font-bold text-white bg-[#8C7869] rounded-full">
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
