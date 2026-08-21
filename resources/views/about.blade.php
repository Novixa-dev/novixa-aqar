@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'من نحن | دار حضرموت العقارية' : 'About Us | Dar Hadhramaut Real Estate')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('About Us') }}
        </span>
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
                        <span class="text-blue-600 text-lg">🛡️</span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'الأمان والتوثيق القانوني:' : 'Legal Security & Deeds:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'فحص شامل لتسلسل البصائر الشرعية وخلو العقار من أي نزاعات.' : 'Thorough verification of deeds, title chains, and legal clearance.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg">💡</span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'التقييم العادل والشفافية:' : 'Fair Valuation & Clarity:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'تثمين دقيق ومطابق لأسعار السوق الواقعية دون مبالغات.' : 'Accurate market valuation reflecting genuine property value.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg">🤝</span>
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
                <div class="text-3xl mb-3">🌊</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'الفرع الرئيسي - المكلا (حضرموت)' : 'Headquarters - Mukalla (Hadhramout)' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'شارع الستين - برج حضرموت التجاري، الدور الرابع. يغطي أحياء فوه، الديس، الشرج، والمخططات الساحلية.' : '60th Street - Hadhramaut Commercial Tower, 4th Floor. Covering Fuwah, Al-Dees, and coastal developments.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold">📍 +967 776 248 526</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
                <div class="text-3xl mb-3">🏛️</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'فرع العاصمة - صنعاء' : 'Capital Branch - Sana\'a' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري. يغطي حدة، بيت بوس، الستين، ومشاريع التطوير السكني الفاخر.' : 'Hadda Street - Hadhramaut Investment Complex. Covering Hadda, Bayt Baws, and premium residential projects.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold">📍 +967 1 400 000</div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xs">
                <div class="text-3xl mb-3">⚓</div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'فرع العاصمة المؤقتة - عدن' : 'Aden Branch' }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed mb-3">
                    {{ app()->getLocale() === 'ar' ? 'المعلا - الشارع الرئيسي. يغطي خور مكسر، كريتر، إنماء، والمنطقة الحرة للمشاريع التجارية.' : 'Mualla - Main Street. Covering Khormaksar, Crater, Inma, and Free Zone investments.' }}
                </p>
                <div class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold">📍 +967 2 200 000</div>
            </div>
        </div>
    </section>
</div>
@endsection
