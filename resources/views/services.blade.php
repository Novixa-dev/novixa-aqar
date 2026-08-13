@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('Services') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ __('Our Real Estate Services') }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ __('We provide comprehensive real estate management and marketing solutions') }}
        </p>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
        <!-- Service 1: Property Sales -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl mb-6">
                    🏢
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Sales') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تسويق احترافي للعقارات عبر شبكة قنوات متقدمة وتقييم تسعيري عادل يضمن سرعة البيع بأعلى عائد.' : 'Professional property marketing across advanced channels with accurate valuations ensuring fast closing at top market value.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Service 2: Property Rental -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mb-6">
                    🔑
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Rental & Leasing') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إدارة عقود الإيجار الرقمية، فحص ومطابقة المستأجرين، وتحصيل الإيجارات تلقائياً بدون عناء.' : 'Digital lease management, tenant vetting and verification, and automated rent collection with zero hassle.' }}
                </p>
            </div>
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1 text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                <span>{{ __('Properties') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Service 3: Valuation -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-3xl mb-6">
                    📊
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Valuation & Analytics') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تقييم عقاري فوري ومتقدم مدعوم بنماذج الذكاء الاصطناعي وتحليل مقارن لبيانات السوق التاريخية.' : 'Instant AI-powered property valuations backed by comparative market analysis and historical transaction data.' }}
                </p>
            </div>
            <a href="{{ route('calculators') }}" class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                <span>{{ __('Calculators') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Service 4: Smart Contracts -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl mb-6">
                    📜
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Legal & Smart Contracts') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'عقود موثقة وإلكترونية ذكية تضمن حقوق جميع الأطراف من ملاك ومستأجرين ومشترين وفق القوانين.' : 'Legally-binding smart digital contracts protecting all stakeholders (landlords, tenants, buyers) with complete compliance.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-amber-600 dark:text-amber-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Service 5: Property Management -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mb-6">
                    🛠️
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Management') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إشراف دوري وصيانة وقائية مع شبكة مقاولين معتمدين وتقارير دورية لأصحاب العقارات.' : 'Periodic maintenance oversight with vetted certified contractors and comprehensive operational owner reporting.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-rose-600 dark:text-rose-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Service 6: 3D & Hologram -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-3xl mb-6">
                    🕶️
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Virtual Staging & 3D Tours') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'استعراض العقارات بتقنيات الواقع الافتراضي والهولوجرام وجولات تفاعلية تتيح المعاينة من أي مكان.' : 'Virtual tours, 3D holographic models, and virtual staging enabling buyers to tour properties from anywhere in the world.' }}
                </p>
            </div>
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1 text-sm font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                <span>{{ __('Properties') }}</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection
