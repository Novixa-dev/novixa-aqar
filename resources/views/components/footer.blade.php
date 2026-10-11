<footer class="bg-[#1C1715] text-[#D4C8BE] pt-16 pb-10 border-t border-[#332A26]">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Col 1: Brand & Bio -->
            <div class="space-y-4">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-white text-[#1C1715] flex items-center justify-center font-black text-xl shadow-md">
                        <svg class="w-6 h-6 text-[#1C1715]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold tracking-tight text-white flex items-center gap-1.5 font-sans">
                            {{ app()->getLocale() === 'ar' ? 'رســــو' : 'RUSU' }}
                        </span>
                        <span class="text-[9px] font-bold uppercase tracking-widest text-[#B5A599]">
                            REAL ESTATE
                        </span>
                    </div>
                </a>
                <p class="text-xs text-[#B5A599] leading-relaxed font-light">
                    {{ app()->getLocale() === 'ar' ? 'رسو الاستثمارية تضع معايير جديدة للسكن الفاخر والاستثمار الموثوق في المملكة العربية السعودية.' : 'RUSU Real Estate sets new standards for luxury residential living and trusted investments.' }}
                </p>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-4">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2.5 text-xs text-[#B5A599]">
                    <li><a href="/" class="hover:text-white transition-colors">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('property.list') }}" class="hover:text-white transition-colors">{{ __('Properties') }}</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-white transition-colors">{{ __('Services') }}</a></li>
                    <li><a href="{{ route('calculators') }}" class="hover:text-white transition-colors">{{ __('Calculators') }}</a></li>
                    <li><a href="{{ route('news') }}" class="hover:text-white transition-colors">{{ __('News') }}</a></li>
                </ul>
            </div>

            <!-- Col 3: Company & Legal -->
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-4">{{ __('Company') }}</h3>
                <ul class="space-y-2.5 text-xs text-[#B5A599]">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">{{ __('Contact Us') }}</a></li>
                    <li><a href="{{ route('terms-and-conditions') }}" class="hover:text-white transition-colors">{{ __('Terms & Conditions') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition-colors">{{ __('Privacy Policy') }}</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact Info -->
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-widest mb-4">{{ app()->getLocale() === 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h3>
                <ul class="space-y-3 text-xs text-[#B5A599]">
                    <li class="flex items-start gap-2.5">
                        <span class="text-amber-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                        <span>{{ app()->getLocale() === 'ar' ? 'المملكة العربية السعودية - الرياض، حي النرجس / حي الياسمين' : 'Saudi Arabia - Riyadh, Al Nargis / Al Yasmin' }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-amber-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></span>
                        <span dir="ltr" class="font-mono text-xs">+966 55 000 0000</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-amber-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></span>
                        <span>info@rusurealestate.com</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-[#332A26] flex flex-col sm:flex-row items-center justify-between text-xs text-[#8C7A6B] gap-4">
            <div>
                &copy; {{ date('Y') }} <strong>{{ $brand->name() }}</strong>. {{ __('All rights reserved.') }}
            </div>
            <div class="flex items-center gap-4">
                <span>{{ app()->getLocale() === 'ar' ? 'تصميم وتطوير فاخر' : 'Luxury Real Estate Platform' }}</span>
            </div>
        </div>
    </div>
</footer>
