<footer class="bg-gray-900 text-gray-300 pt-16 pb-8 border-t border-gray-800">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Col 1: Brand & Bio -->
            <div class="space-y-4">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" class="h-10 w-auto rounded-xl shadow-xs" alt="Dar Hadhramaut Real Estate" />
                    <span class="text-xl font-black text-white">
                        {{ app()->getLocale() === 'ar' ? 'دار حضرموت العقارية' : 'Dar Hadhramaut Real Estate' }}
                    </span>
                </a>
                <p class="text-sm text-gray-400 leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'بوابتكم الأولى للتملك والتطوير والاستثمار العقاري وإدارة الأملاك في اليمن.' : 'Your premier gateway for real estate ownership, investment, and property management in Yemen.' }}
                </p>
            </div>

            <!-- Col 2: Quick Links -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="/" class="hover:text-blue-400 transition-colors">{{ __('Home') }}</a></li>
                    <li><a href="{{ route('property.list') }}" class="hover:text-blue-400 transition-colors">{{ __('Properties') }}</a></li>
                    <li><a href="{{ route('services') }}" class="hover:text-blue-400 transition-colors">{{ __('Services') }}</a></li>
                    <li><a href="{{ route('calculators') }}" class="hover:text-blue-400 transition-colors">{{ __('Calculators') }}</a></li>
                    <li><a href="{{ route('news') }}" class="hover:text-blue-400 transition-colors">{{ __('News') }}</a></li>
                </ul>
            </div>

            <!-- Col 3: Company & Legal -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ __('Company') }}</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-blue-400 transition-colors">{{ __('About Us') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-blue-400 transition-colors">{{ __('Contact Us') }}</a></li>
                    <li><a href="{{ route('terms-and-conditions') }}" class="hover:text-blue-400 transition-colors">{{ __('Terms & Conditions') }}</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-blue-400 transition-colors">{{ __('Privacy Policy') }}</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact Info -->
            <div>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">{{ app()->getLocale() === 'ar' ? 'معلومات الاتصال' : 'Contact Information' }}</h3>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li class="flex items-start gap-2.5">
                        <span class="text-blue-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                        <span>{{ app()->getLocale() === 'ar' ? 'اليمن - حضرموت / المكلا - شارع الستين، برج حضرموت التجاري' : 'Yemen - Hadhramout / Mukalla - 60th St, Hadhramaut Commercial Tower' }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-blue-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></span>
                        <span dir="ltr">+967 776 248 526</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-blue-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></span>
                        <span>info@darhadhramaut.ye</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 mt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <div>
                &copy; {{ date('Y') }} <strong>{{ app()->getLocale() === 'ar' ? 'دار حضرموت العقارية' : 'Dar Hadhramaut Real Estate' }}</strong>. {{ __('All rights reserved.') }}
            </div>
            <div class="flex items-center gap-4">
                <span>{{ app()->getLocale() === 'ar' ? 'تطوير:' : 'Powered by:' }} <strong>Novixa Aqar</strong></span>
            </div>
        </div>
    </div>
</footer>
