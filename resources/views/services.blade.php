@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
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
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M3 21l3.75-9M21 21l-3.75-9M6.75 21h10.5" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Sales') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تسويق احترافي للعقارات عبر شبكة قنوات متقدمة وتقييم تسعيري عادل يضمن سرعة البيع بأعلى عائد.' : 'Professional property marketing across advanced channels with accurate valuations ensuring fast closing at top market value.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Service 2: Property Rental -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mb-6">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Rental & Leasing') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إدارة عقود الإيجار الرقمية، فحص ومطابقة المستأجرين، وتحصيل الإيجارات تلقائياً بدون عناء.' : 'Digital lease management, tenant vetting and verification, and automated rent collection with zero hassle.' }}
                </p>
            </div>
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1 text-sm font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                <span>{{ __('Properties') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Service 3: Valuation -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-3xl mb-6">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Valuation & Analytics') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تقييم عقاري دقيق مدعوم بتحليل مقارن لبيانات السوق والمعاملات التاريخية في المنطقة.' : 'Accurate property valuations backed by comparative market analysis and historical transaction data for the area.' }}
                </p>
            </div>
            <a href="{{ route('calculators') }}" class="inline-flex items-center gap-1 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                <span>{{ __('Calculators') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Service 4: Legal & Documentation -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl mb-6">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Legal & Documentation') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'عقود موثقة وتوقيع إلكتروني آمن يضمن حقوق جميع الأطراف من ملاك ومستأجرين ومشترين وفق القوانين.' : 'Documented contracts and secure digital signatures protecting all stakeholders (landlords, tenants, buyers) with complete compliance.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-amber-600 dark:text-amber-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Service 5: Property Management -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mb-6">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Property Management') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'إشراف دوري وصيانة وقائية مع شبكة مقاولين معتمدين وتقارير دورية لأصحاب العقارات.' : 'Periodic maintenance oversight with vetted certified contractors and comprehensive operational owner reporting.' }}
                </p>
            </div>
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-1 text-sm font-bold text-rose-600 dark:text-rose-400 hover:underline">
                <span>{{ __('Contact Us') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Service 6: Virtual Tours & 3D Models -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-3xl mb-6">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Virtual Tours & 3D Models') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'جولات افتراضية تفاعلية ونماذج ثلاثية الأبعاد تتيح لك معاينة العقار من أي مكان قبل الزيارة.' : 'Interactive virtual tours and 3D property models letting you preview a listing from anywhere before visiting.' }}
                </p>
            </div>
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1 text-sm font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                <span>{{ __('Properties') }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>
    </div>
</div>
@endsection
