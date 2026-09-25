@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'خدماتنا العقارية المتكاملة | دار حضرموت العقارية' : 'Our Real Estate Services | Dar Hadhramaut Real Estate')

@section('content')
<div class="relative w-full overflow-hidden bg-[#FAF8F5] dark:bg-[#141211]">
    <!-- 1. Hero Header Section -->
    <div class="relative bg-[#1C1816] text-white py-20 lg:py-28 overflow-hidden border-b border-white/10">
        <!-- Background Image with Luxury Dark Gradient Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80" 
                 alt="Luxury Real Estate" 
                 class="w-full h-full object-cover opacity-20 filter blur-[1px] scale-105">
            <div class="absolute inset-0 bg-gradient-to-b from-[#1C1816]/95 via-[#1C1816]/85 to-[#1C1816]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_center,rgba(140,120,105,0.25),transparent_70%)]"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white leading-tight tracking-tight mb-6">
                {{ app()->getLocale() === 'ar' ? 'منظومة خدمات عقارية متكاملة' : __('Our Real Estate Services') }}
            </h1>

            <!-- Subtitle Paragraph -->
            <p class="text-base sm:text-lg lg:text-xl text-stone-300 font-light leading-relaxed max-w-3xl mx-auto mb-10">
                {{ app()->getLocale() === 'ar' ? 'نقدم حلولاً عقارية متطورة وشاملة تبدأ من التسويق والوساطة، وصولاً إلى إدارة الأملاك، التقييم السعري المعتمد، وتوثيق البصائر بأعلى معايير الأمان والشفافية.' : __('We provide comprehensive real estate management and marketing solutions') }}
            </p>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('contact') }}" 
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-sm font-bold shadow-lg shadow-[#8C7869]/20 transition-all hover:scale-[1.02]">
                    <span>{{ app()->getLocale() === 'ar' ? 'طلب استشارة عقارية' : 'Request Advisory' }}</span>
                    <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                </a>
                <a href="{{ route('calculators') }}" 
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-bold border border-white/20 backdrop-blur-md transition-all">
                    <span>{{ app()->getLocale() === 'ar' ? 'حاسبات التثمين والتمويل' : 'Financial Calculators' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Trust Pillars Bar (Overlapping Hero) -->
    <div class="relative z-20 container mx-auto px-4 lg:px-8 -mt-10 mb-20">
        <div class="max-w-6xl mx-auto bg-white dark:bg-[#1C1816] rounded-3xl shadow-xl border border-stone-200/80 dark:border-stone-800 p-6 lg:p-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 divide-y md:divide-y-0 md:divide-x rtl:md:divide-x-reverse divide-stone-100 dark:divide-stone-800">
                <!-- Pillar 1 -->
                <div class="flex items-center gap-4 pt-4 md:pt-0">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-stone-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'تراخيص رسمية معتمدة' : 'Officially Licensed' }}
                        </h4>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'وساطة موثقة قانونياً' : 'Compliant brokerage' }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="flex items-center gap-4 pt-4 md:pt-0 md:ps-6">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-stone-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'تقييم تسعيري عادل' : 'Fair Valuation' }}
                        </h4>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'مبني على بيانات السوق' : 'Market-based pricing' }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="flex items-center gap-4 pt-4 md:pt-0 md:ps-6">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-stone-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'توثيق البصائر والعقود' : 'Deed Verification' }}
                        </h4>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'حماية قانونية كاملة' : 'Legally secured' }}
                        </p>
                    </div>
                </div>

                <!-- Pillar 4 -->
                <div class="flex items-center gap-4 pt-4 md:pt-0 md:ps-6">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-stone-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'خدمة المغتربين الخاصة' : 'Diaspora Care' }}
                        </h4>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'إدارة عن بُعد بأمان' : 'Remote management' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Core Services Grid (The 6 Main Services) -->
    <section class="container mx-auto px-4 lg:px-8 mb-24">
        <div class="max-w-6xl mx-auto">
            <!-- Section Header (NO PILL TAG) -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white tracking-tight mb-4">
                    {{ app()->getLocale() === 'ar' ? 'خدماتنا العقارية المتخصصة' : 'Our Professional Real Estate Services' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-sm sm:text-base leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'باقة من الخدمات المتكاملة المصممة خصيصاً لتلبية احتياجات الملاك والمشترين والمستثمرين في السوق اليمني' : 'A tailored suite of comprehensive solutions for property owners, buyers, and investors' }}
                </p>
            </div>

            <!-- The Grid of 6 Services -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1: Property Sales -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M3 21l3.75-9M21 21l-3.75-9M6.75 21h10.5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Property Sales') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'تسويق احترافي للعقارات عبر شبكة قنوات متقدمة وتقييم تسعيري عادل يضمن سرعة البيع بأعلى عائد.' : 'Professional property marketing across advanced channels with accurate valuations ensuring fast closing at top market value.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تسويق رقمي واسع وتصوير احترافي للعقار' : 'Targeted multi-channel marketing & photography' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'ربط مباشر بين المشترين الجادين والملاك' : 'Direct connection with qualified buyers' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'إشراف على المفاوضات لضمان أفضل سعر' : 'Expert price negotiation and deal closing' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'بيع وشراء موثق' : 'Verified Sales' }}</span>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Contact Us') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Service 2: Property Rental & Leasing -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Property Rental & Leasing') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'إدارة عقود الإيجار الرقمية، فحص ومطابقة المستأجرين، وتحصيل الإيجارات تلقائياً بدون عناء.' : 'Digital lease management, tenant vetting and verification, and automated rent collection with zero hassle.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'فحص خلفية المستأجرين والقدرة المالية' : 'Rigorous tenant background & solvency checks' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'عقود إيجار إلكترونية تحفظ حقوق الطرفين' : 'Standardized legal digital lease agreements' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'متابعة التحصيل الدوري والإيداع المباشر' : 'Automated collection & timely disbursement' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'إيجارات موثوقة' : 'Verified Rentals' }}</span>
                        <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Properties') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Service 3: Property Valuation & Analytics -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Property Valuation & Analytics') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'تقييم عقاري دقيق مدعوم بتحليل مقارن لبيانات السوق والمعاملات التاريخية في المنطقة.' : 'Accurate property valuations backed by comparative market analysis and historical transaction data for the area.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تقارير تثمين رسمية ومعتمدة للبنوك والأفراد' : 'Certified valuation reports for banks and investors' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تحليل مقارن لأسعار المتر في مختلف الأحياء' : 'Comparative price per sqm micro-analysis' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'حاسبات رقمية فورية لتقدير القيمة والعائد' : 'Interactive ROI and mortgage calculators' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'تحليلات ذكية' : 'Market Analytics' }}</span>
                        <a href="{{ route('calculators') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Calculators') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Service 4: Legal & Documentation -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Legal & Documentation') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'عقود موثقة وتوقيع إلكتروني آمن يضمن حقوق جميع الأطراف من ملاك ومستأجرين ومشترين وفق القوانين.' : 'Documented contracts and secure digital signatures protecting all stakeholders (landlords, tenants, buyers) with complete compliance.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'فحص ومطابقة البصائر وسلاسل الملكية' : 'Title deed examination & ownership chain audits' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'توثيق رسمي لدى الجهات والمحاكم المختصة' : 'Official registration with municipal authorities' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'استشارات وقائية للحد من النزاعات العقارية' : 'Preventative legal counsel on title disputes' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'حماية تشريعية' : 'Legal Security' }}</span>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Contact Us') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Service 5: Property Management -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Property Management') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'إشراف دوري وصيانة وقائية مع شبكة مقاولين معتمدين وتقارير دورية لأصحاب العقارات.' : 'Periodic maintenance oversight with vetted certified contractors and comprehensive operational owner reporting.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'إشراف ميداني وصيانة وقائية دورية' : 'Regular site visits & preventive maintenance' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'إدارة الفواتير والخدمات وشبكة مقاولين معتمدة' : 'Utility billing & certified contractor network' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تقارير مالية وتشغيلية شفافة للمالك شهرياً' : 'Transparent monthly operational & financial reports' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'رعاية أصول متكاملة' : 'Asset Management' }}</span>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Contact Us') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Service 6: Virtual Tours & 3D Models -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1.5 hover:border-[#8C7869]/50">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ __('Virtual Tours & 3D Models') }}
                        </h3>
                        <p class="text-sm text-stone-500 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'جولات افتراضية تفاعلية ونماذج ثلاثية الأبعاد تتيح لك معاينة العقار من أي مكان قبل الزيارة.' : 'Interactive virtual tours and 3D property models letting you preview a listing from anywhere before visiting.' }}
                        </p>
                        <!-- Bullet Points -->
                        <ul class="space-y-2.5 mb-8 text-xs text-stone-600 dark:text-stone-300 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تصوير بانورامي 360 درجة عالي الدقة' : 'High-definition 360-degree panoramic tours' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'مخططات تفاعلية لأبعاد ومساحات الغرف' : 'Interactive floor plans & spatial measurement' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8C7869]"></span>
                                <span>{{ app()->getLocale() === 'ar' ? 'تجربة استكشاف حية للمغتربين والمستثمرين عن بعد' : 'Remote exploration for overseas buyers' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                        <span class="text-xs text-stone-400">{{ app()->getLocale() === 'ar' ? 'تقنية ثلاثية الأبعاد' : '3D Reality' }}</span>
                        <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#8C7869] hover:underline group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform">
                            <span>{{ __('Properties') }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Specialized Section: Expatriates & Overseas Investors -->
    <section class="container mx-auto px-4 lg:px-8 mb-24">
        <div class="max-w-6xl mx-auto bg-gradient-to-br from-[#1C1816] via-[#26201D] to-[#1C1816] rounded-3xl p-8 sm:p-12 lg:p-16 text-white border border-[#8C7869]/30 relative overflow-hidden shadow-2xl">
            <!-- Background Watermark / Accent Glow -->
            <div class="absolute -end-24 -bottom-24 w-96 h-96 rounded-full bg-[#8C7869]/20 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7">
                    <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight mb-4">
                        {{ app()->getLocale() === 'ar' ? 'خدمات مخصصة للمغتربين والمستثمرين بالخارج' : 'Specialized Services for Overseas Yemenis & Investors' }}
                    </h2>
                    <p class="text-stone-300 text-sm sm:text-base leading-relaxed font-light mb-8">
                        {{ app()->getLocale() === 'ar' ? 'ندرك التحديات التي يواجهها المغترب في متابعة عقاراته في اليمن. لذلك صممنا منظومة وكالة ومتابعة شاملة تتيح لك الشراء، البيع، البناء، أو تحصيل الإيجارات وأنت في بلد إقامتك بأعلى معايير الشفافية وبإشراف قانوني معتمد.' : 'We understand the unique challenges faced by expatriates managing assets in Yemen. We provide end-to-end proxy representation, acquisition, legal verification, and rental management with live updates.' }}
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="flex items-start gap-3 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <div class="w-8 h-8 rounded-xl bg-[#8C7869]/20 text-[#8C7869] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white mb-0.5">{{ app()->getLocale() === 'ar' ? 'وكالات شرعية رسمية' : 'Legal Power of Attorney' }}</h4>
                                <p class="text-[11px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'توثيق الإجراءات عبر القنصليات والمحاكم' : 'Legally registered proxies' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <div class="w-8 h-8 rounded-xl bg-[#8C7869]/20 text-[#8C7869] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white mb-0.5">{{ app()->getLocale() === 'ar' ? 'تقارير فيديو وصور دورية' : 'Live Media Reports' }}</h4>
                                <p class="text-[11px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'معاينة حالة العقار والبناء لحظة بلحظة' : 'Real-time property condition audits' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <div class="w-8 h-8 rounded-xl bg-[#8C7869]/20 text-[#8C7869] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white mb-0.5">{{ app()->getLocale() === 'ar' ? 'تحويل الإيجارات لحسابك' : 'Direct Rent Remittances' }}</h4>
                                <p class="text-[11px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'تحصيل وإيداع آمن وفق العملة المتفق عليها' : 'Secure cross-border disbursements' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <div class="w-8 h-8 rounded-xl bg-[#8C7869]/20 text-[#8C7869] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white mb-0.5">{{ app()->getLocale() === 'ar' ? 'فرص استثمارية ذات عائد' : 'High-Yield Opportunities' }}</h4>
                                <p class="text-[11px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'دراسات جدوى لأراضي ومشاريع تجارية' : 'Commercial land feasibility studies' }}</p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-sm font-bold shadow-lg shadow-[#8C7869]/30 transition-all hover:scale-[1.02]">
                        <span>{{ app()->getLocale() === 'ar' ? 'تواصل مع مسؤول خدمة المغتربين' : 'Speak to Diaspora Consultant' }}</span>
                        <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                    </a>
                </div>

                <div class="lg:col-span-5 relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-white/20 aspect-[4/3] relative">
                        <img src="https://images.unsplash.com/photo-1582407947304-fd86f028f716?auto=format&fit=crop&w=1000&q=80" 
                             alt="Luxury Architecture" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 start-6 end-6 bg-black/60 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center font-bold text-lg shrink-0">
                                    ✓
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white">{{ app()->getLocale() === 'ar' ? 'ضمان الأمانة والشفافية' : 'Integrity & Security' }}</div>
                                    <div class="text-[11px] text-stone-300">{{ app()->getLocale() === 'ar' ? 'سجل حافل بالنجاح مع مئات العملاء بالخارج' : 'Proven track record with overseas clients' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Process Section: 4 Simple Steps to Get Service -->
    <section class="container mx-auto px-4 lg:px-8 mb-24">
        <div class="max-w-6xl mx-auto">
            <!-- Section Header (NO PILL TAG) -->
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white tracking-tight mb-4">
                    {{ app()->getLocale() === 'ar' ? 'كيف نعمل - خطوات الحصول على الخدمة' : 'How We Work - 4 Simple Steps' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-sm sm:text-base leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'إجراءات واضحة ومنظمة تضمن سلاسة التعامل وسرعة الإنجاز من البداية وحتى التسليم النهائي' : 'A transparent and streamlined process ensuring speed, security, and precision from day one' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative bg-white dark:bg-[#1C1816] rounded-3xl p-8 border border-stone-200/80 dark:border-stone-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#8C7869] text-white font-black text-lg flex items-center justify-center mb-6 shadow-md shadow-[#8C7869]/20 font-mono">
                            01
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-2">
                            {{ app()->getLocale() === 'ar' ? 'تقديم الطلب أو الاستشارة' : 'Consultation Request' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'تواصل معنا هاتفياً أو عبر الموقع لتحديد متطلباتك العقارية (شراء، بيع، إدارة، أو تقييم).' : 'Contact our team via phone or website to specify your real estate needs and objectives.' }}
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-white dark:bg-[#1C1816] rounded-3xl p-8 border border-stone-200/80 dark:border-stone-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#8C7869] text-white font-black text-lg flex items-center justify-center mb-6 shadow-md shadow-[#8C7869]/20 font-mono">
                            02
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المعاينة والدراسة الفنية' : 'Inspection & Appraisal' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'يقوم خبراؤنا بمعاينة العقار ميدانياً، وفحص وثائقه وسلاسل الملكية، وتقدير سعره العادل وفق السوق.' : 'Our experts conduct on-site evaluations, verify ownership documents, and analyze pricing.' }}
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-white dark:bg-[#1C1816] rounded-3xl p-8 border border-stone-200/80 dark:border-stone-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#8C7869] text-white font-black text-lg flex items-center justify-center mb-6 shadow-md shadow-[#8C7869]/20 font-mono">
                            03
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-2">
                            {{ app()->getLocale() === 'ar' ? 'صياغة العقود والتوثيق' : 'Contract Documentation' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'إعداد العقود القانونية المعتمدة ببنود واضحة وصريحة تحفظ حقوق جميع الأطراف وتصديقها رسمياً.' : 'Drafting comprehensive legal contracts with clear terms to protect rights and register deeds.' }}
                        </p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative bg-white dark:bg-[#1C1816] rounded-3xl p-8 border border-stone-200/80 dark:border-stone-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#8C7869] text-white font-black text-lg flex items-center justify-center mb-6 shadow-md shadow-[#8C7869]/20 font-mono">
                            04
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-2">
                            {{ app()->getLocale() === 'ar' ? 'التنفيذ والمتابعة الدائمة' : 'Closing & Ongoing Care' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'إتمام تسليم العقار وتحويل المدفوعات، أو بدء الإشراف الدوري وإرسال التقارير الشهرية.' : 'Final hand-over, secure funds transfer, and ongoing maintenance with periodic reports.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. FAQ Section (Accordion) -->
    <section class="container mx-auto px-4 lg:px-8 mb-24">
        <div class="max-w-4xl mx-auto">
            <!-- Section Header (NO PILL TAG) -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white tracking-tight mb-4">
                    {{ app()->getLocale() === 'ar' ? 'الأسئلة الشائعة حول خدماتنا' : 'Frequently Asked Questions' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-sm sm:text-base leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'إجابات واضحة ومباشرة لأبرز التساؤلات حول آليات التعاقد والضمانات العقارية' : 'Direct answers to the most common questions regarding our real estate services' }}
                </p>
            </div>

            <!-- Accordion container with Alpine.js -->
            <div class="space-y-4" x-data="{ active: null }">
                <!-- FAQ 1 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-2xl border border-stone-200/80 dark:border-stone-800 overflow-hidden shadow-sm transition-all">
                    <button @click="active = (active === 1 ? null : 1)" 
                            class="w-full p-6 text-start flex items-center justify-between gap-4 font-bold text-stone-900 dark:text-white hover:text-[#8C7869] dark:hover:text-[#8C7869] transition-colors">
                        <span class="text-base sm:text-lg">
                            {{ app()->getLocale() === 'ar' ? 'كيف تضمن دار حضرموت العقارية حقوق الطرفين في البيع والشراء؟' : 'How does Dar Hadhramaut guarantee buyer and seller rights?' }}
                        </span>
                        <span class="w-8 h-8 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center text-[#8C7869] shrink-0 text-sm transition-transform duration-300"
                              :class="active === 1 ? 'rotate-180 bg-[#8C7869] text-white' : ''">
                            &darr;
                        </span>
                    </button>
                    <div x-show="active === 1" x-transition.duration.300ms class="px-6 pb-6 text-stone-600 dark:text-stone-400 text-sm leading-relaxed border-t border-stone-100 dark:border-stone-800 pt-4 font-light">
                        {{ app()->getLocale() === 'ar' ? 'نعتمد إجراءات تدقيق صارمة تبدأ بفحص أصول البصائر وسلامة المخططات لدى مكاتب الأراضي والتوثيق، مع اشتراط حسابات بنكية موثوقة أو شيكات مصدقة، وصياغة عقود بيع معتمدة تضمن عدم وجود أي نزاعات أو التزامات مالية مسبقة على العقار.' : 'We implement rigorous due diligence by verifying original deeds and municipal zoning plans, enforcing certified escrow/bank drafts, and executing legally verified sales agreements with zero prior encumbrances.' }}
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-2xl border border-stone-200/80 dark:border-stone-800 overflow-hidden shadow-sm transition-all">
                    <button @click="active = (active === 2 ? null : 2)" 
                            class="w-full p-6 text-start flex items-center justify-between gap-4 font-bold text-stone-900 dark:text-white hover:text-[#8C7869] dark:hover:text-[#8C7869] transition-colors">
                        <span class="text-base sm:text-lg">
                            {{ app()->getLocale() === 'ar' ? 'هل تقدمون خدمات توثيق ومطابقة البصائر القديمة والأراضي المتوارثة؟' : 'Do you offer deed verification for inherited or historical land plots?' }}
                        </span>
                        <span class="w-8 h-8 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center text-[#8C7869] shrink-0 text-sm transition-transform duration-300"
                              :class="active === 2 ? 'rotate-180 bg-[#8C7869] text-white' : ''">
                            &darr;
                        </span>
                    </button>
                    <div x-show="active === 2" x-transition.duration.300ms class="px-6 pb-6 text-stone-600 dark:text-stone-400 text-sm leading-relaxed border-t border-stone-100 dark:border-stone-800 pt-4 font-light">
                        {{ app()->getLocale() === 'ar' ? 'نعم، نمتلك قسماً قانونياً متخصصاً بالتعاون مع أمناء شرعيين ومحامين معتمدين لدراسة سلاسل البصائر ومطابقة الحدود الجغرافية مع السجلات الرسمية وإصدار بصائر حديثة موثقة بموجب القانون.' : 'Yes, our legal department collaborates with licensed public notaries to trace title chains, survey exact geographic boundaries against municipal maps, and issue formally registered modern deeds.' }}
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-2xl border border-stone-200/80 dark:border-stone-800 overflow-hidden shadow-sm transition-all">
                    <button @click="active = (active === 3 ? null : 3)" 
                            class="w-full p-6 text-start flex items-center justify-between gap-4 font-bold text-stone-900 dark:text-white hover:text-[#8C7869] dark:hover:text-[#8C7869] transition-colors">
                        <span class="text-base sm:text-lg">
                            {{ app()->getLocale() === 'ar' ? 'كيف يستفيد العميل المقيم في الخارج من خدمة إدارة الأملاك؟' : 'How does an overseas client benefit from property management?' }}
                        </span>
                        <span class="w-8 h-8 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center text-[#8C7869] shrink-0 text-sm transition-transform duration-300"
                              :class="active === 3 ? 'rotate-180 bg-[#8C7869] text-white' : ''">
                            &darr;
                        </span>
                    </button>
                    <div x-show="active === 3" x-transition.duration.300ms class="px-6 pb-6 text-stone-600 dark:text-stone-400 text-sm leading-relaxed border-t border-stone-100 dark:border-stone-800 pt-4 font-light">
                        {{ app()->getLocale() === 'ar' ? 'بموجب وكالة شرعية رسمية، يتولى فريقنا كل المهام الميدانية: اختيار المستأجرين، صيانة العقار، دفع الفواتير، وتحصيل الإيجارات وإيداعها مباشرة في حسابك البنكي، مع تزويدك بتقارير وصور وفيديوهات دورية للمتابعة.' : 'Under a formal legal proxy, our team handles all on-ground responsibilities: tenant vetting, maintenance, bill clearing, rent collection, and direct remittance, along with regular visual audit reports.' }}
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-2xl border border-stone-200/80 dark:border-stone-800 overflow-hidden shadow-sm transition-all">
                    <button @click="active = (active === 4 ? null : 4)" 
                            class="w-full p-6 text-start flex items-center justify-between gap-4 font-bold text-stone-900 dark:text-white hover:text-[#8C7869] dark:hover:text-[#8C7869] transition-colors">
                        <span class="text-base sm:text-lg">
                            {{ app()->getLocale() === 'ar' ? 'كيف يمكنني طلب تقييم عقاري رسمي أو استخدام حاسبة الأسعار؟' : 'How can I request a formal valuation or use your calculators?' }}
                        </span>
                        <span class="w-8 h-8 rounded-full bg-stone-100 dark:bg-stone-800 flex items-center justify-center text-[#8C7869] shrink-0 text-sm transition-transform duration-300"
                              :class="active === 4 ? 'rotate-180 bg-[#8C7869] text-white' : ''">
                            &darr;
                        </span>
                    </button>
                    <div x-show="active === 4" x-transition.duration.300ms class="px-6 pb-6 text-stone-600 dark:text-stone-400 text-sm leading-relaxed border-t border-stone-100 dark:border-stone-800 pt-4 font-light">
                        {{ app()->getLocale() === 'ar' ? 'يمكنك استخدام حاسباتنا التفاعلية المجانية عبر الموقع لتقدير تقريبي، أو التواصل معنا لإرسال مقيّم عقاري معتمد للموقع لإعداد تقرير تسعيري شامل ومعتمد لتقديمه للجهات المالية أو لغرض البيع.' : 'You can use our free online calculators for an instant estimate, or contact us to dispatch a certified appraiser for a formal and bank-compliant valuation report.' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. Call To Action (Bottom Luxury Banner) -->
    <div class="container mx-auto px-4 lg:px-8 mb-20">
        <div class="max-w-6xl mx-auto bg-gradient-to-r from-[#1C1816] via-[#26201D] to-[#1C1816] rounded-3xl p-10 sm:p-14 text-center text-white border border-[#8C7869]/30 shadow-2xl relative overflow-hidden">
            <div class="relative z-10 max-w-3xl mx-auto">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black mb-4 tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'جاهز للبدء في خطوتك العقارية القادمة؟' : 'Ready to Take Your Next Real Estate Step?' }}
                </h2>
                <p class="text-stone-300 text-sm sm:text-base leading-relaxed font-light mb-8 max-w-2xl mx-auto">
                    {{ app()->getLocale() === 'ar' ? 'فريقنا المتخصص في مكاتب حضرموت، عدن، وصنعاء جاهز لخدمتكم وتقديم الاستشارات الدقيقة بأعلى درجات المصداقية والاحتراف.' : 'Our dedicated team across Hadhramaut, Aden, and Sana\'a is ready to assist you with precision, trust, and unparalleled market expertise.' }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-sm font-bold shadow-xl shadow-[#8C7869]/30 transition-all hover:scale-[1.02]">
                        <span>{{ app()->getLocale() === 'ar' ? 'تواصل مع مستشارينا الآن' : 'Contact Us Today' }}</span>
                        <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                    </a>
                    <a href="{{ route('property.list') }}" 
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-bold border border-white/20 backdrop-blur-md transition-all">
                        <span>{{ app()->getLocale() === 'ar' ? 'استعراض العقارات المتاحة' : 'Explore Properties' }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
