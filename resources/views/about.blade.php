@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'من نحن | '.$brand->name('ar') : 'About Us | '.$brand->name('en'))

@section('content')
<div class="relative w-full overflow-hidden">
    <!-- Hero Header Section -->
    <div class="relative bg-[#1C1816] text-white py-20 lg:py-28 overflow-hidden border-b border-white/10">
        <!-- Background Image with Dark Gradient Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80"
                 alt="Luxury Architecture"
                 class="w-full h-full object-cover opacity-20 filter blur-[1px] scale-105">
            <div class="absolute inset-0 bg-gradient-to-b from-[#1C1816]/95 via-[#1C1816]/85 to-[#1C1816]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_center,rgba(140,120,105,0.25),transparent_70%)]"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black text-white leading-tight tracking-tight mb-6">
                {{ $brand->name() }}
            </h1>

            <!-- Subtitle Paragraph -->
            <p class="text-base sm:text-lg lg:text-xl text-stone-300 font-light leading-relaxed max-w-3xl mx-auto mb-10">
                {{ app()->getLocale() === 'ar' ? 'بوابتكم الأولى للتملك والاستثمار العقاري في الجمهورية اليمنية. نقدم حلولاً متكاملة للشراء، البيع، الإيجار، وتوثيق البصائر بأعلى معايير الأمان والشفافية.' : 'Your premier gateway for property ownership and investment in Yemen. We deliver end-to-end solutions for buying, selling, leasing, and verified deed documentation with absolute trust.' }}
            </p>

            <!-- Quick Action Links -->
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('property.list') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-sm font-bold shadow-lg shadow-[#8C7869]/20 transition-all hover:scale-[1.02]">
                    <span>{{ app()->getLocale() === 'ar' ? 'استكشف عروض العقارات' : 'Browse Properties' }}</span>
                    <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                </a>
                <a href="{{ route('contact') }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-sm font-bold border border-white/20 backdrop-blur-md transition-all">
                    <span>{{ app()->getLocale() === 'ar' ? 'تواصل مع مستشارينا' : 'Contact Advisory' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Bar (Numbers of Trust) -->
    <div class="relative z-20 container mx-auto px-4 lg:px-8 -mt-10 mb-16">
        <div class="max-w-5xl mx-auto bg-white dark:bg-[#1C1816] rounded-3xl p-6 sm:p-8 shadow-xl border border-stone-200/80 dark:border-stone-800">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 divide-y sm:divide-y-0 sm:divide-x rtl:sm:divide-x-reverse divide-stone-200 dark:divide-stone-800 text-center">
                <!-- Stat 1 -->
                <div class="pt-4 sm:pt-0 sm:px-6 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white font-mono tracking-tight mb-1">
                        100%
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-stone-600 dark:text-stone-300">
                        {{ app()->getLocale() === 'ar' ? 'بصائر موثقة ومفحوصة' : 'Verified Deeds' }}
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="pt-4 sm:pt-0 sm:px-6 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white font-mono tracking-tight mb-1">
                        +500
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-stone-600 dark:text-stone-300">
                        {{ app()->getLocale() === 'ar' ? 'عقار ومخطط سكني' : 'Listed Properties' }}
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="pt-4 sm:pt-0 sm:px-6 flex flex-col items-center">
                    <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-stone-900 dark:text-white font-mono tracking-tight mb-1">
                        24/7
                    </div>
                    <div class="text-xs sm:text-sm font-bold text-stone-600 dark:text-stone-300">
                        {{ app()->getLocale() === 'ar' ? 'دعم واستشارات' : 'Client Advisory' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vision & Mission Showcase -->
    <div class="container mx-auto px-4 lg:px-8 py-8 mb-16">
        <div class="max-w-6xl mx-auto bg-white dark:bg-[#1C1816] rounded-3xl p-8 lg:p-14 shadow-sm border border-stone-200/80 dark:border-stone-800">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Text Column (7 cols) -->
                <div class="lg:col-span-7">
                    <h2 class="text-2xl sm:text-4xl font-black text-stone-900 dark:text-white leading-tight mb-6">
                        {{ app()->getLocale() === 'ar' ? 'رؤيتنا ورسالتنا العقارية' : 'Our Vision & Real Estate Mission' }}
                    </h2>

                    <p class="text-stone-600 dark:text-stone-300 text-base sm:text-lg leading-relaxed mb-8 font-light">
                        {{ app()->getLocale() === 'ar' ? 'تأسست '.$brand->name('ar').' لتكون الشريك الموثوق للأفراد، المستثمرين، والمطورين العقاريين في مختلف المحافظات اليمنية (المكلا، صنعاء، عدن، مأرب، تعز). ندمج بين الخبرة الميدانية العريقة وأحدث التقنيات الرقمية كالجولات الافتراضية والنماذج ثلاثية الأبعاد.' : $brand->name('en').' was founded to be the trusted partner for homeowners, investors, and property developers across Yemen (Mukalla, Sana\'a, Aden, Marib, Taiz). We combine profound local market expertise with modern real estate technology, virtual tours, and 3D property models.' }}
                    </p>

                    <!-- City Tags -->
                    <div class="mb-6">
                        <span class="block text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">
                            {{ app()->getLocale() === 'ar' ? 'نطاق خدماتنا الميدانية:' : 'Regional Coverage:' }}
                        </span>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['المكلا', 'صنعاء', 'عدن', 'مأرب', 'تعز'] as $city)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-100 dark:bg-stone-800/80 text-stone-700 dark:text-stone-300 text-xs font-bold border border-stone-200 dark:border-stone-700">
                                    <svg class="w-3.5 h-3.5 text-[#8C7869]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $city }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tech Feature Badges -->
                    <div class="flex flex-wrap gap-3 pt-6 border-t border-stone-100 dark:border-stone-800">
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 dark:text-stone-300">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'جولات افتراضية 360°' : '360° Virtual Tours' }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 dark:text-stone-300">
                            <span class="w-2 h-2 rounded-full bg-[#8C7869]"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'نماذج ثلاثية الأبعاد 3D' : 'Interactive 3D Models' }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 text-xs font-bold text-stone-700 dark:text-stone-300">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'فحص شرعي وقانوني' : 'Legal & Deed Auditing' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Column (5 cols) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-stone-200 dark:border-stone-700 aspect-[4/5]">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=80" 
                             alt="{{ $brand->name() }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1C1816] via-transparent to-transparent opacity-80"></div>
                        
                        <!-- Floating Glass Card -->
                        <div class="absolute bottom-6 inset-x-6 p-5 rounded-2xl bg-[#1C1816]/85 backdrop-blur-md border border-white/15 text-white">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#8C7869] flex items-center justify-center text-white shrink-0 shadow-md">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">{{ app()->getLocale() === 'ar' ? 'شريككم العقاري المعتمد' : 'Certified Real Estate Partner' }}</div>
                                    <div class="text-xs text-stone-300">{{ app()->getLocale() === 'ar' ? 'أمان، مصداقية، وجودة استثمارية' : 'Trust, Security & Investment Quality' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Commitments & Values Section -->
    <div class="container mx-auto px-4 lg:px-8 mb-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-4xl font-black text-stone-900 dark:text-white tracking-tight mb-3">
                    {{ app()->getLocale() === 'ar' ? 'قيمنا الجوهرية والالتزام' : 'Our Core Commitments' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-sm leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'نلتزم بأرقى معايير النزاهة والاحترافية لضمان راحة عملائنا وسلامة استثماراتهم.' : 'We uphold the highest standards of integrity to safeguard our clients and their investments.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Commitment 1 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'الأمان والتوثيق القانوني:' : 'Legal Security & Deeds:' }}
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-sm leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'فحص شامل لتسلسل البصائر الشرعية وخلو العقار من أي نزاعات.' : 'Thorough verification of deeds, title chains, and legal clearance.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 dark:border-stone-800 text-xs font-bold text-[#8C7869]">
                        {{ app()->getLocale() === 'ar' ? 'حماية قانونية كاملة' : 'Full Legal Protection' }}
                    </div>
                </div>

                <!-- Commitment 2 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'التقييم العادل والشفافية:' : 'Fair Valuation & Clarity:' }}
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-sm leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'تثمين دقيق ومطابق لأسعار السوق الواقعية دون مبالغات.' : 'Accurate market valuation reflecting genuine property value.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 dark:border-stone-800 text-xs font-bold text-[#8C7869]">
                        {{ app()->getLocale() === 'ar' ? 'تسعير سوقي واقعي' : 'Objective Market Price' }}
                    </div>
                </div>

                <!-- Commitment 3 -->
                <div class="bg-white dark:bg-[#1C1816] rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-6 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'خدمة متميزة للعملاء والمغتربين:' : 'Diaspora & Local Client Care:' }}
                        </h3>
                        <p class="text-stone-600 dark:text-stone-400 text-sm leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'حلول خاصة لإدارة واستثمار عقارات المغتربين اليمنيين بالخارج بأمان تام.' : 'Tailored real estate investment and management services for overseas Yemenis.' }}
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-stone-100 dark:border-stone-800 text-xs font-bold text-[#8C7869]">
                        {{ app()->getLocale() === 'ar' ? 'رعاية خاصة للمغتربين' : 'Expatriate Portfolio' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Geographic Coverage & Regional Branches -->
    <section class="container mx-auto px-4 lg:px-8 mb-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-4xl font-black text-stone-900 dark:text-white tracking-tight mb-3">
                    {{ app()->getLocale() === 'ar' ? 'تغطيتنا وفروعنا في اليمن' : 'Our Branches & Nationwide Coverage' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-sm leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'مكاتبنا الميدانية وكوادرنا الاستشارية في خدمتكم في أهم المراكز الحضرية' : 'Our physical branches and local advisory teams are at your service' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Branch 1: Mukalla (HQ) -->
                <div class="relative bg-white dark:bg-[#1C1816] p-8 rounded-3xl border-2 border-[#8C7869]/40 dark:border-[#8C7869]/50 shadow-lg flex flex-col justify-between group hover:border-[#8C7869] transition-all">
                    <!-- Headquarters Badge -->
                    <div class="absolute -top-3.5 inset-x-0 flex justify-center">
                        <span class="px-3.5 py-1 rounded-full bg-[#8C7869] text-white text-[11px] font-black uppercase tracking-wider shadow-md">
                            {{ app()->getLocale() === 'ar' ? 'المقر الرئيسي' : 'Headquarters' }}
                        </span>
                    </div>

                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-[#8C7869]/10 text-[#8C7869] flex items-center justify-center mb-5 mt-2">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'الفرع الرئيسي - المكلا (حضرموت)' : 'Headquarters - Mukalla (Hadhramout)' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-600 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ $brand->address() }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 dark:border-stone-800">
                        <a href="tel:+{{ $brand->phoneDigits() }}" 
                           class="inline-flex items-center gap-2 text-xs font-mono font-bold text-[#8C7869] hover:text-[#7A6759] transition-colors">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span dir="ltr">{{ $brand->phone() }}</span>
                        </a>
                    </div>
                </div>

                <!-- Branch 2: Sana'a -->
                <div class="bg-white dark:bg-[#1C1816] p-8 rounded-3xl border border-stone-200/80 dark:border-stone-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 flex items-center justify-center mb-5 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'فرع العاصمة - صنعاء' : 'Capital Branch - Sana\'a' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-600 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'شارع حدة - مجمع حضرموت الاستثماري. يغطي حدة، بيت بوس، الستين، ومشاريع التطوير السكني الفاخر.' : 'Hadda Street - Hadhramaut Investment Complex. Covering Hadda, Bayt Baws, and premium residential projects.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 dark:border-stone-800">
                        <a href="tel:+9671400000" 
                           class="inline-flex items-center gap-2 text-xs font-mono font-bold text-[#8C7869] hover:text-[#7A6759] transition-colors">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+967 1 400 000</span>
                        </a>
                    </div>
                </div>

                <!-- Branch 3: Aden -->
                <div class="bg-white dark:bg-[#1C1816] p-8 rounded-3xl border border-stone-200/80 dark:border-stone-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-stone-100 dark:bg-stone-800 text-stone-700 dark:text-stone-300 flex items-center justify-center mb-5 group-hover:bg-[#8C7869] group-hover:text-white transition-colors duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-3">
                            {{ app()->getLocale() === 'ar' ? 'فرع العاصمة المؤقتة - عدن' : 'Aden Branch' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-stone-600 dark:text-stone-400 leading-relaxed mb-6 font-light">
                            {{ app()->getLocale() === 'ar' ? 'المعلا - الشارع الرئيسي. يغطي خور مكسر، كريتر، إنماء، والمنطقة الحرة للمشاريع التجارية.' : 'Mualla - Main Street. Covering Khormaksar, Crater, Inma, and Free Zone investments.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 dark:border-stone-800">
                        <a href="tel:+9672200000" 
                           class="inline-flex items-center gap-2 text-xs font-mono font-bold text-[#8C7869] hover:text-[#7A6759] transition-colors">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+967 2 200 000</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Banner -->
    <div class="container mx-auto px-4 lg:px-8 mb-16">
        <div class="max-w-6xl mx-auto rounded-3xl bg-gradient-to-br from-[#1C1816] via-[#26201D] to-[#1C1816] text-white p-8 sm:p-12 border border-white/10 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(140,120,105,0.3),transparent_70%)]"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-start">
                <div class="max-w-2xl">
                    <h3 class="text-2xl sm:text-3xl font-black text-white mb-3">
                        {{ app()->getLocale() === 'ar' ? 'جاهز لبدء استثمارك العقاري القادم؟' : 'Ready to Start Your Real Estate Journey?' }}
                    </h3>
                    <p class="text-stone-300 text-sm leading-relaxed font-light">
                        {{ app()->getLocale() === 'ar' ? 'فريقنا الاستشاري متواجد للإجابة على جميع استفساراتك وتوجيهك نحو أفضل الفرص العقارية في اليمن.' : 'Our advisory team is ready to answer all your questions and direct you to the prime opportunities.' }}
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-4 shrink-0">
                    <a href="{{ route('contact') }}" 
                       class="px-8 py-3.5 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-sm font-bold shadow-lg shadow-[#8C7869]/25 transition-all hover:scale-105">
                        {{ app()->getLocale() === 'ar' ? 'تواصل معنا الآن' : 'Contact Us Now' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
