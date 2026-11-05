@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'اتصل بنا | '.$brand->name('ar') : 'Contact Us | '.$brand->name('en'))

@section('content')
<div class="relative w-full overflow-hidden">
    <!-- Hero Header Section -->
    <div class="relative bg-[#1C1816] text-white py-20 lg:py-24 overflow-hidden border-b border-white/10">
        <!-- Background Architectural Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80"
                 alt="Luxury Real Estate"
                 class="w-full h-full object-cover opacity-20 filter blur-[1px]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#1C1816]/95 via-[#1C1816]/85 to-[#1C1816]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_center,rgba(140,120,105,0.25),transparent_70%)]"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl font-black text-white leading-tight tracking-tight mb-5">
                {{ app()->getLocale() === 'ar' ? 'تواصل مع '.$brand->name('ar') : 'Contact '.$brand->name('en') }}
            </h1>

            <!-- Subtitle Paragraph -->
            <p class="text-base sm:text-lg text-stone-300 font-light leading-relaxed max-w-3xl mx-auto">
                {{ app()->getLocale() === 'ar' ? 'مستشارونا العقاريون في خدمتكم على مدار الساعة للإجابة عن استفسارات البيع، الشراء، التثمين، وتوثيق العقود.' : 'Our dedicated real estate advisors are available 24/7 to assist with property acquisitions, sales, valuations, and contract documentation.' }}
            </p>
        </div>
    </div>

    <!-- Quick Channels Strip -->
    <div class="relative z-20 container mx-auto px-4 lg:px-8 -mt-8 mb-12">
        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- WhatsApp Channel -->
            <a href="https://wa.me/{{ $brand->phoneDigits() }}?text={{ urlencode(app()->getLocale() === 'ar' ? 'مرحباً، أود الاستفسار عن عروض وخدمات '.$brand->name('ar') : 'Hello, I would like to inquire about '.$brand->name('en').' listings.') }}"
               target="_blank"
               rel="noopener noreferrer"
               class="bg-white dark:bg-[#1C1816] rounded-2xl p-5 shadow-lg border border-stone-200/80 dark:border-stone-800 hover:border-emerald-500 transition-all flex items-center gap-4 group hover:-translate-y-0.5">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <span class="text-xs text-stone-400 block font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'محادثة فورية' : 'Live Chat' }}</span>
                    <strong class="text-sm font-bold text-stone-900 dark:text-white truncate block">WhatsApp (<span dir="ltr">{{ $brand->phone() }}</span>)</strong>
                </div>
            </a>

            <!-- Phone Channel -->
            <a href="tel:+{{ $brand->phoneDigits() }}"
               class="bg-white dark:bg-[#1C1816] rounded-2xl p-5 shadow-lg border border-stone-200/80 dark:border-stone-800 hover:border-[#8C7869] transition-all flex items-center gap-4 group hover:-translate-y-0.5">
                <div class="w-12 h-12 rounded-xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0 group-hover:bg-[#8C7869] group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <span class="text-xs text-stone-400 block font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'الاتصال الموحد' : 'Direct Call' }}</span>
                    <strong class="text-sm font-bold text-stone-900 dark:text-white font-mono truncate block">{{ $brand->phone() }}</strong>
                </div>
            </a>

            <!-- Email Channel -->
            <a href="mailto:{{ $brand->email() }}"
               class="bg-white dark:bg-[#1C1816] rounded-2xl p-5 shadow-lg border border-stone-200/80 dark:border-stone-800 hover:border-[#8C7869] transition-all flex items-center gap-4 group hover:-translate-y-0.5">
                <div class="w-12 h-12 rounded-xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0 group-hover:bg-[#8C7869] group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
                <div class="overflow-hidden">
                    <span class="text-xs text-stone-400 block font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Official Email' }}</span>
                    <strong class="text-sm font-bold text-stone-900 dark:text-white truncate block">{{ $brand->email() }}</strong>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="container mx-auto px-4 lg:px-8 mb-20">
        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left Column: Fast Contact Card & Regional Branches (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Fast Contact VIP Card -->
                <div class="bg-gradient-to-br from-[#1C1816] via-[#26201D] to-[#1C1816] rounded-3xl p-8 text-white shadow-xl border border-white/10 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(140,120,105,0.25),transparent_60%)]"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-white/10 text-stone-200 text-xs font-bold mb-4">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'متاحون لخدمتكم' : 'Active Support' }}</span>
                        </div>

                        <h3 class="text-2xl font-black mb-2 text-white">
                            {{ app()->getLocale() === 'ar' ? 'خدمة العملاء والاستشارات' : 'Direct Advisory & Inquiries' }}
                        </h3>
                        <p class="text-stone-300 text-xs sm:text-sm leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'تواصل فورياً مع أحد مستشارينا الميدانيين عبر الواتساب أو الاتصال الهاتفي المباشر.' : 'Reach our advisory desk instantly via WhatsApp or direct phone.' }}
                        </p>

                        <div class="space-y-3.5">
                            <!-- WhatsApp Button -->
                            <a href="https://wa.me/{{ $brand->phoneDigits() }}?text={{ urlencode(app()->getLocale() === 'ar' ? 'مرحباً، أود الاستفسار عن عروض وخدمات '.$brand->name('ar') : 'Hello, I would like to inquire about '.$brand->name('en').' listings.') }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center justify-between p-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-bold text-xs sm:text-sm transition-all shadow-md group">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 fill-current shrink-0" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                                    </svg>
                                    <span>{{ app()->getLocale() === 'ar' ? 'محادثة فورية عبر الواتساب' : 'Live WhatsApp Support' }}</span>
                                </div>
                                <span class="text-base {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }} group-hover:translate-x-1 transition-transform">&rarr;</span>
                            </a>

                            <!-- Direct Call Button -->
                            <a href="tel:+{{ $brand->phoneDigits() }}"
                               class="flex items-center gap-3 p-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-semibold text-xs sm:text-sm transition-all border border-white/15">
                                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                </span>
                                <span>{{ app()->getLocale() === 'ar' ? 'الهاتف الموحد:' : 'Phone:' }} <span dir="ltr">{{ $brand->phone() }}</span></span>
                            </a>

                            <!-- Email Row -->
                            <a href="mailto:{{ $brand->email() }}"
                               class="flex items-center gap-3 p-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl text-xs sm:text-sm border border-white/15 transition-all">
                                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                </span>
                                <span>{{ $brand->email() }}</span>
                            </a>

                            <!-- Working Hours Row -->
                            <div class="flex items-center gap-3 p-4 bg-white/10 rounded-2xl text-stone-200 text-xs sm:text-sm border border-white/15">
                                <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <span>{{ app()->getLocale() === 'ar' ? 'السبت - الخميس: 8:00 صباحاً - 8:00 مساءً' : 'Sat - Thu: 8:00 AM - 8:00 PM' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Office Branches Card -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-7 border border-stone-200/80 dark:border-stone-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-stone-100 dark:border-stone-800">
                        <h4 class="text-base font-bold text-stone-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'عناوين المكاتب والفروع الرئيسية' : 'Office Branches' }}
                        </h4>
                        <span class="text-xs font-bold text-[#8C7869]">{{ app()->getLocale() === 'ar' ? '3 فروع' : '3 Offices' }}</span>
                    </div>
                    
                    <!-- Branch 1: Mukalla -->
                    <div class="flex items-start gap-3.5 text-xs">
                        <div class="w-8 h-8 rounded-xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <strong class="text-sm font-bold text-stone-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'المكلا (المقر الرئيسي):' : 'Mukalla (HQ):' }}</strong>
                                <span class="px-2 py-0.5 rounded-md bg-[#8C7869]/15 text-[#8C7869] text-[10px] font-black">{{ app()->getLocale() === 'ar' ? 'الرئيسي' : 'HQ' }}</span>
                            </div>
                            <span class="text-stone-500 dark:text-stone-400 leading-relaxed block">{{ $brand->address() }}</span>
                        </div>
                    </div>

                    <!-- Branch 2: Sana'a -->
                    <div class="flex items-start gap-3.5 text-xs">
                        <div class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-stone-900 dark:text-white block mb-1">{{ app()->getLocale() === 'ar' ? 'صنعاء:' : 'Sana\'a:' }}</strong>
                            <span class="text-stone-500 dark:text-stone-400 leading-relaxed block">{{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري.' : 'Hadda Street - Hadhramaut Investment Complex.' }}</span>
                        </div>
                    </div>

                    <!-- Branch 3: Aden -->
                    <div class="flex items-start gap-3.5 text-xs">
                        <div class="w-8 h-8 rounded-xl bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-stone-900 dark:text-white block mb-1">{{ app()->getLocale() === 'ar' ? 'عدن:' : 'Aden:' }}</strong>
                            <span class="text-stone-500 dark:text-stone-400 leading-relaxed block">{{ app()->getLocale() === 'ar' ? 'المعلا - الشارع الرئيسي مقابل البنك الأهلي.' : 'Mualla - Main Street opposite NBY Bank.' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Contact Message Form (7 cols) -->
            <div class="lg:col-span-7 bg-white dark:bg-[#1C1816] rounded-3xl shadow-sm border border-stone-200/80 dark:border-stone-800 p-8 sm:p-12">
                <div class="mb-8">
                    <h3 class="text-2xl sm:text-3xl font-black text-stone-900 dark:text-white mb-3">
                        {{ app()->getLocale() === 'ar' ? 'أرسل لنا رسالة أو استفسار' : 'Send us a Message' }}
                    </h3>
                    <p class="text-stone-500 dark:text-stone-400 text-sm leading-relaxed font-light">
                        {{ app()->getLocale() === 'ar' ? 'املأ النموذج أدناه وسيقوم أحد مسؤولي خدمة العملاء بالتواصل معك خلال ساعات قليلة.' : 'Fill out the form below and our team will get back to you shortly.' }}
                    </p>
                </div>

                <!-- Embedded Contact Form Component -->
                @include('components.contact-form')

                <!-- Privacy & Assurance Footer -->
                <div class="mt-8 pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center gap-3 text-xs text-stone-400">
                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'بياناتك وخصوصيتك محمية بالكامل وفق سياسة الخصوصية المعتمدة.' : 'Your data and privacy are fully protected under our privacy policy.' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
