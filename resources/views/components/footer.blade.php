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
                @php($socials = app(\App\Settings\GeneralSettings::class))
                @if ($socials->facebook_url || $socials->twitter_url || $socials->youtube_url || $socials->github_url)
                    <div class="flex items-center gap-3 pt-1">
                        @if ($socials->facebook_url)
                            <a href="{{ $socials->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-blue-600 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12.07C22 6.5 17.52 2 12 2S2 6.5 2 12.07c0 5.02 3.66 9.18 8.44 9.93v-7.03H7.9v-2.9h2.54V9.84c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.9h-2.34V22c4.78-.75 8.44-4.91 8.44-9.93Z"/></svg>
                            </a>
                        @endif
                        @if ($socials->twitter_url)
                            <a href="{{ $socials->twitter_url }}" target="_blank" rel="noopener" aria-label="Twitter / X" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-blue-500 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 2H22l-7.1 8.1L22.9 22H16l-5.3-6.9L4.6 22H1.5l7.6-8.7L1 2h7l4.8 6.3L18.9 2Zm-2.4 18h1.7L8.6 4h-1.8l9.7 16Z"/></svg>
                            </a>
                        @endif
                        @if ($socials->youtube_url)
                            <a href="{{ $socials->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-red-600 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.3 3.6-6.3 3.6Z"/></svg>
                            </a>
                        @endif
                        @if ($socials->github_url)
                            <a href="{{ $socials->github_url }}" target="_blank" rel="noopener" aria-label="GitHub" class="w-9 h-9 rounded-full bg-gray-800 hover:bg-gray-700 flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.56-1.11-4.56-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.64 0 0 .84-.27 2.75 1.02a9.6 9.6 0 0 1 5 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.37.2 2.39.1 2.64.64.7 1.03 1.59 1.03 2.68 0 3.84-2.35 4.68-4.58 4.93.36.31.68.92.68 1.85v2.74c0 .26.18.57.69.48A10 10 0 0 0 12 2Z"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
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
                {{ $socials->footer_copyright ?: (date('Y').' '.$brand->name().'. '.__('All rights reserved.')) }}
            </div>
            <div class="flex items-center gap-4">
                <span>{{ app()->getLocale() === 'ar' ? 'تصميم وتطوير فاخر' : 'Luxury Real Estate Platform' }}</span>
            </div>
        </div>
    </div>
</footer>
