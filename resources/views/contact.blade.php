@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-14">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('Contact Us') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'تواصل مع دار حضرموت العقارية' : 'Contact Dar Hadhramaut Real Estate' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'مستشارونا العقاريون في خدمتكم على مدار الساعة للإجابة عن استفسارات البيع، الشراء، التثمين، وتوثيق العقود.' : 'Our dedicated real estate advisors are available 24/7 to assist with property acquisitions, sales, valuations, and contract documentation.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-16">
        <!-- Left: Contact Details & Yemeni Branches (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Fast Contact Card -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl">
                <h3 class="text-xl font-black mb-2">{{ app()->getLocale() === 'ar' ? 'خدمة العملاء والاستشارات' : 'Direct Advisory & Inquiries' }}</h3>
                <p class="text-blue-100 text-xs leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تواصل فورياً مع أحد مستشارينا الميدانيين عبر الواتساب أو الاتصال الهاتفي المباشر.' : 'Reach our advisory desk instantly via WhatsApp or direct phone.' }}
                </p>

                <div class="space-y-4">
                    <a href="https://wa.me/967776248526?text={{ urlencode(app()->getLocale() === 'ar' ? 'مرحباً، أود الاستفسار عن عروض وخدمات دار حضرموت العقارية' : 'Hello, I would like to inquire about Dar Hadhramaut Real Estate listings.') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center gap-3 p-3.5 bg-green-500 hover:bg-green-600 text-white rounded-2xl font-bold text-xs transition-all shadow-md">
                        <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                        </svg>
                        <span>{{ app()->getLocale() === 'ar' ? 'محادثة فورية عبر الواتساب (776 248 526 967+)' : 'Live WhatsApp Support (+967 776 248 526)' }}</span>
                    </a>

                    <a href="tel:+967776248526"
                       class="flex items-center gap-3 p-3.5 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-semibold text-xs transition-all border border-white/20">
                        <span class="text-base">📞</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'الهاتف الموحد: 776 248 526 967+' : 'Phone: +967 776 248 526' }}</span>
                    </a>

                    <div class="flex items-center gap-3 p-3.5 bg-white/10 rounded-2xl text-white text-xs border border-white/20">
                        <span class="text-base">✉️</span>
                        <span>info@darhadhramaut.ye</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 bg-white/10 rounded-2xl text-white text-xs border border-white/20">
                        <span class="text-base">🕒</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'السبت - الخميس: 8:00 صباحاً - 8:00 مساءً' : 'Sat - Thu: 8:00 AM - 8:00 PM' }}</span>
                    </div>
                </div>
            </div>

            <!-- Branches List Card -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm space-y-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-700">
                    {{ app()->getLocale() === 'ar' ? 'عناوين المكاتب والفروع الرئيسية' : 'Office Branches' }}
                </h4>
                
                <div class="flex items-start gap-3 text-xs">
                    <span class="text-blue-600 font-bold">📍</span>
                    <div>
                        <strong class="text-gray-900 dark:text-white block mb-0.5">{{ app()->getLocale() === 'ar' ? 'المكلا (المقر الرئيسي):' : 'Mukalla (HQ):' }}</strong>
                        <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'شارع الستين - برج حضرموت التجاري، الدور الرابع.' : '60th Street - Hadhramaut Commercial Tower, 4th Floor.' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-xs">
                    <span class="text-blue-600 font-bold">📍</span>
                    <div>
                        <strong class="text-gray-900 dark:text-white block mb-0.5">{{ app()->getLocale() === 'ar' ? 'صنعاء:' : 'Sana\'a:' }}</strong>
                        <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري.' : 'Hadda Street - Hadhramaut Investment Complex.' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-xs">
                    <span class="text-blue-600 font-bold">📍</span>
                    <div>
                        <strong class="text-gray-900 dark:text-white block mb-0.5">{{ app()->getLocale() === 'ar' ? 'عدن:' : 'Aden:' }}</strong>
                        <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'المعلا - الشارع الرئيسي مقابل البنك الأهلي.' : 'Mualla - Main Street opposite NBY Bank.' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Contact Message Form (7 cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 sm:p-10">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                {{ app()->getLocale() === 'ar' ? 'أرسل لنا رسالة أو استفسار' : 'Send us a Message' }}
            </h3>
            <p class="text-gray-500 dark:text-gray-400 text-xs mb-6">
                {{ app()->getLocale() === 'ar' ? 'املأ النموذج أدناه وسيقوم أحد مسؤولي خدمة العملاء بالتواصل معك خلال ساعات قليلة.' : 'Fill out the form below and our team will get back to you shortly.' }}
            </p>
            @include('components.contact-form')
        </div>
    </div>
</div>
@endsection
