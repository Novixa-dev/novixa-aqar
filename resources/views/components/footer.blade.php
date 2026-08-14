<footer class="bg-gray-900 text-gray-300 pt-16 pb-8 border-t border-gray-800">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            <!-- Col 1: Brand & Bio -->
            <div class="space-y-4">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" class="h-10 w-auto rounded-xl shadow-xs" alt="Al-Ofuq Real Estate" />
                    <span class="text-xl font-black text-white">
                        {{ app()->getLocale() === 'ar' ? 'شركة الأفق العقارية' : 'Al-Ofuq Real Estate' }}
                    </span>
                </a>
                <p class="text-sm text-gray-400 leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'بوابتكم الأولى للتملك والتطوير والاستثمار العقاري وإدارة الأملاك في اليمن، تطوير وإشراف Novixa و AK01REDWAN.' : 'Your premier gateway for real estate ownership, investment, and property management in Yemen. Developed & supervised by Novixa & AK01REDWAN.' }}
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
                        <span class="text-blue-400">📍</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'اليمن - حضرموت / المكلا - شارع الستين، برج الأفق' : 'Yemen - Hadhramout / Mukalla - 60th St, Al-Ofuq Tower' }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-blue-400">📞</span>
                        <span dir="ltr">+967 5 300 000 / +967 777 000 000</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-blue-400">✉️</span>
                        <span>info@alofuq-aqar.ye</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="pt-8 mt-8 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
            <div>
                &copy; {{ date('Y') }} <strong>Novixa Aqar</strong>. {{ __('All rights reserved.') }}
            </div>
            <div class="flex items-center gap-4">
                <span>{{ app()->getLocale() === 'ar' ? 'تطوير وإشراف:' : 'Developed by:' }} <strong>Novixa & AK01REDWAN</strong></span>
            </div>
        </div>
    </div>
</footer>
