@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'اتصل بنا | دار حضرموت العقارية' : 'Contact Us | Dar Hadhramaut Real Estate')

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
                        <span class="text-base"><svg class="w-5 h-5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></span>
                        <span>{{ app()->getLocale() === 'ar' ? 'الهاتف الموحد: 776 248 526 967+' : 'Phone: +967 776 248 526' }}</span>
                    </a>

                    <div class="flex items-center gap-3 p-3.5 bg-white/10 rounded-2xl text-white text-xs border border-white/20">
                        <span class="text-base"><svg class="w-5 h-5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></span>
                        <span>info@novixa.dev</span>
                    </div>

                    <div class="flex items-center gap-3 p-3.5 bg-white/10 rounded-2xl text-white text-xs border border-white/20">
                        <span class="text-base"><svg class="w-5 h-5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></span>
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
                    <span class="text-blue-600 font-bold"><svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                    <div>
                        <strong class="text-gray-900 dark:text-white block mb-0.5">{{ app()->getLocale() === 'ar' ? 'المكلا (المقر الرئيسي):' : 'Mukalla (HQ):' }}</strong>
                        <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'شارع الستين - برج حضرموت التجاري، الدور الرابع.' : '60th Street - Hadhramaut Commercial Tower, 4th Floor.' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-xs">
                    <span class="text-blue-600 font-bold"><svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                    <div>
                        <strong class="text-gray-900 dark:text-white block mb-0.5">{{ app()->getLocale() === 'ar' ? 'صنعاء:' : 'Sana\'a:' }}</strong>
                        <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري.' : 'Hadda Street - Hadhramaut Investment Complex.' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-xs">
                    <span class="text-blue-600 font-bold"><svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
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
