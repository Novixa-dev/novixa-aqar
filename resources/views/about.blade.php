@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'من نحن | دار حضرموت العقارية' : 'About Us | Dar Hadhramaut Real Estate')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">

        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'دار حضرموت العقارية' : 'Dar Hadhramaut Real Estate' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'بوابتكم الأولى للتملك والاستثمار العقاري في الجمهورية اليمنية. نقدم حلولاً متكاملة للشراء، البيع، الإيجار، وتوثيق البصائر بأعلى معايير الأمان والشفافية.' : 'Your premier gateway for property ownership and investment in Yemen. We deliver end-to-end solutions for buying, selling, leasing, and verified deed documentation with absolute trust.' }}
        </p>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 lg:p-12 mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-6">
                    {{ app()->getLocale() === 'ar' ? 'رؤيتنا ورسالتنا العقارية' : 'Our Vision & Real Estate Mission' }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تأسست دار حضرموت العقارية لتكون الشريك الموثوق للأفراد، المستثمرين، والمطورين العقاريين في مختلف المحافظات اليمنية (المكلا، صنعاء، عدن، مأرب، تعز). ندمج بين الخبرة الميدانية العريقة وأحدث التقنيات الرقمية كالجولات الافتراضية والنماذج ثلاثية الأبعاد.' : 'Dar Hadhramaut Real Estate was founded to be the trusted partner for homeowners, investors, and property developers across Yemen (Mukalla, Sana\'a, Aden, Marib, Taiz). We combine profound local market expertise with modern real estate technology, virtual tours, and 3D property models.' }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl text-center">
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mb-1">100%</div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'بصائر موثقة ومفحوصة' : 'Verified Deeds' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl text-center">
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mb-1">+500</div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'عقار ومخطط سكني' : 'Listed Properties' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl text-center col-span-2 sm:col-span-1">
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mb-1">24/7</div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'دعم واستشارات' : 'Client Advisory' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 p-8 rounded-3xl border border-blue-100 dark:border-gray-600">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ app()->getLocale() === 'ar' ? 'قيمنا الجوهرية والالتزام' : 'Our Core Commitments' }}
                </h3>
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg"><svg class="w-6 h-6 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg></span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'الأمان والتوثيق القانوني:' : 'Legal Security & Deeds:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'فحص شامل لتسلسل البصائر الشرعية وخلو العقار من أي نزاعات.' : 'Thorough verification of deeds, title chains, and legal clearance.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg"><svg class="w-6 h-6 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" /></svg></span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'التقييم العادل والشفافية:' : 'Fair Valuation & Clarity:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'تثمين دقيق ومطابق لأسعار السوق الواقعية دون مبالغات.' : 'Accurate market valuation reflecting genuine property value.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg"><svg class="w-6 h-6 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg></span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'خدمة متميزة للعملاء والمغتربين:' : 'Diaspora & Local Client Care:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'حلول خاصة لإدارة واستثمار عقارات المغتربين اليمنيين بالخارج بأمان تام.' : 'Tailored real estate investment and management services for overseas Yemenis.' }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Geographic Coverage & Branches -->
    <section class="mb-12">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-3">
                {{ app()->getLocale() === 'ar' ? 'تغطيتنا وفروعنا في اليمن' : 'Our Branches & Nationwide Coverage' }}
            </h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">
                {{ app()->getLocale() === 'ar' ? 'مكاتبنا الميدانية وكوادرنا الاستشارية في خدمتكم في أهم المراكز الحضرية' : 'Our physical branches and local advisory teams are at your service' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
                <div class="text-blue-500 mb-3"><svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg></div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'الفرع الرئيسي - المكلا (حضرموت)' : 'Headquarters - Mukalla (Hadhramout)' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'شارع الستين - برج حضرموت التجاري، الدور الرابع. يغطي أحياء فوه، الديس، الشرج، والمخططات الساحلية.' : '60th Street - Hadhramaut Commercial Tower, 4th Floor. Covering Fuwah, Al-Dees, and coastal developments.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold"><svg class="w-3.5 h-3.5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> +967 776 248 526</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
                <div class="text-blue-500 mb-3"><svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg></div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'فرع العاصمة - صنعاء' : 'Capital Branch - Sana\'a' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري. يغطي حدة، بيت بوس، الستين، ومشاريع التطوير السكني الفاخر.' : 'Hadda Street - Hadhramaut Investment Complex. Covering Hadda, Bayt Baws, and premium residential projects.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold"><svg class="w-3.5 h-3.5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> +967 1 400 000</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
                <div class="text-blue-500 mb-3"><svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" /></svg></div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'فرع العاصمة المؤقتة - عدن' : 'Aden Branch' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'المعلا - الشارع الرئيسي. يغطي خور مكسر، كريتر، إنماء، والمنطقة الحرة للمشاريع التجارية.' : 'Mualla - Main Street. Covering Khormaksar, Crater, Inma, and Free Zone investments.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold"><svg class="w-3.5 h-3.5 inline-block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> +967 2 200 000</div>
            </div>
        </div>
    </section>
</div>
@endsection
