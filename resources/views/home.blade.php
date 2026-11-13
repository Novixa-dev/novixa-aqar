@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'رسو العقارية | نصنع الفخامة في كل تفصيلة' : 'RUSU Real Estate | Crafting Luxury in Every Detail')

@push('meta')
    <script type="application/ld+json">{!! $brand->homepageJsonLd() !!}</script>
@endpush

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
<!-- Hero Section (Full 100% Match to Mockup Screenshot) -->
<style>
    #hero-viewport-section {
        min-height: calc(100vh - 5rem);
        min-height: calc(100dvh - 5rem);
    }
    @media (min-width: 768px) {
        #hero-viewport-section {
            height: calc(100vh - 5rem);
            height: calc(100dvh - 5rem);
        }
    }
</style>
<div id="hero-viewport-section" class="relative w-full bg-[#1C1816] text-white overflow-hidden flex flex-col justify-center items-center py-6 sm:py-8">
    <!-- Hero Background Image (Luxury Modern Villa with Pool matching mockup) -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1920&q=80"
             alt="RUSU Real Estate Luxury Villa"
             class="w-full h-full object-cover object-center">
        <!-- Dark Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#1C1816]/75 via-[#1C1816]/50 to-[#1C1816]/95"></div>
    </div>

    <!-- Main Hero Center Content -->
    <div class="relative z-10 container mx-auto px-4 lg:px-8 py-6 text-center max-w-4xl my-auto">
        <!-- Main Headline -->
        <h1 class="text-5xl sm:text-7xl lg:text-8xl font-black text-white leading-[1.05] tracking-tight font-sans mb-6">
            {{ app()->getLocale() === 'ar' ? 'نصنع الفخامة' : 'Crafting Luxury' }}<br>
            <span class="text-stone-200 font-extrabold text-4xl sm:text-6xl lg:text-7xl block mt-2">
                {{ app()->getLocale() === 'ar' ? 'في كل تفصيلة' : 'In Every Detail' }}
            </span>
        </h1>

        <!-- Subtitle Paragraph -->
        <p class="text-xs sm:text-sm lg:text-base text-stone-200/90 mb-8 max-w-2xl mx-auto font-light leading-relaxed">
            {{ app()->getLocale() === 'ar' ? 'رسو الاستثمارية تضع معايير جديدة للسكن الفاخر والاستثمار الموثوق، اكتشف مشاريعنا التي صممت لتلائم أسلوب حياتك.' : 'RUSU Real Estate sets new standards for luxury residential living and trusted investments. Explore properties designed to match your lifestyle.' }}
        </p>

        <!-- CTA Pill Buttons -->
        <div class="flex flex-wrap items-center justify-center gap-4 mb-10">
            <!-- Secondary Contact Button -->
            <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-black/30 hover:bg-black/50 text-white border border-white/30 font-bold text-xs sm:text-sm rounded-full backdrop-blur-md transition-all shadow-md">
                {{ app()->getLocale() === 'ar' ? 'تواصل معنا' : 'Contact Us' }}
            </a>

            <!-- Primary Explore Button (Warm Taupe Pill) -->
            <a href="#featured-projects" class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold text-xs sm:text-sm rounded-full shadow-xl transition-all active:scale-95 border border-white/10">
                <span>{{ app()->getLocale() === 'ar' ? 'استكشف المشاريع' : 'Explore Projects' }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <!-- Small Title Above Search Bar -->
        <p class="text-xs text-stone-300 font-bold mb-3 tracking-wide">
            {{ app()->getLocale() === 'ar' ? 'ابحث في مشاريعنا' : 'Search in Our Projects' }}
        </p>

        <!-- White Pill Search Input Container -->
        <div class="w-full max-w-4xl mx-auto">
            <form action="{{ route('property.list') }}" method="GET" class="bg-white rounded-2xl md:rounded-full p-2 shadow-2xl flex flex-col md:flex-row items-stretch md:items-center gap-2 md:gap-0 border border-white/80">
                <!-- Search Input with Icon on Right (RTL context) -->
                <div class="flex-1 flex items-center gap-3 px-4 min-w-[180px]">
                    <svg class="w-5 h-5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن مشروع، موقع، أو حي...' : 'Search for project, location, or neighborhood...' }}" 
                           class="w-full py-2.5 bg-transparent border-none focus:outline-none text-xs sm:text-sm text-stone-900 placeholder-stone-400 font-medium">
                </div>

                <!-- Select Filters (Status & Property Type) -->
                <div class="flex items-center border-t md:border-t-0 md:border-s border-stone-200 pt-2 md:pt-0">
                    <!-- Status Filter: الكل (بيع وإيجار) -->
                    <div class="relative flex-1 md:flex-initial flex items-center px-3">
                        <select name="status" class="w-full md:w-auto appearance-none bg-transparent border-0 border-none text-stone-700 text-xs sm:text-sm font-bold focus:outline-none focus:ring-0 focus:border-0 shadow-none cursor-pointer pe-6 ps-1 py-2" style="border: 0 !important; outline: none !important; box-shadow: none !important;">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'الكل (بيع وإيجار)' : 'All (Sale & Rent)' }}</option>
                            <option value="for_sale">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</option>
                            <option value="for_rent">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</option>
                        </select>
                        <div class="absolute end-2 top-1/2 -translate-y-1/2 pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Divider Between Status and Property Type -->
                    <div class="w-px h-6 md:h-7 bg-stone-200 shrink-0"></div>

                    <!-- Property Type Filter: جميع الأنواع -->
                    <div class="relative flex-1 md:flex-initial flex items-center px-3">
                        <select name="property_type" class="w-full md:w-auto appearance-none bg-transparent border-0 border-none text-stone-700 text-xs sm:text-sm font-bold focus:outline-none focus:ring-0 focus:border-0 shadow-none cursor-pointer pe-6 ps-1 py-2" style="border: 0 !important; outline: none !important; box-shadow: none !important;">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                            <option value="Apartment">{{ app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Apartment' }}</option>
                            <option value="Villa">{{ app()->getLocale() === 'ar' ? 'فيلا فاخرة' : 'Luxury Villa' }}</option>
                            <option value="Land">{{ app()->getLocale() === 'ar' ? 'أرض' : 'Land' }}</option>
                            <option value="Commercial">{{ app()->getLocale() === 'ar' ? 'عقار تجاري' : 'Commercial' }}</option>
                            <option value="Building">{{ app()->getLocale() === 'ar' ? 'عمارة' : 'Building' }}</option>
                        </select>
                        <div class="absolute end-2 top-1/2 -translate-y-1/2 pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Submit Pill Button on Left (RTL context) -->
                <button type="submit" class="px-8 py-3 bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold text-xs sm:text-sm rounded-xl md:rounded-full shadow-md transition-all active:scale-95 shrink-0 text-center">
                    {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bottom Statistics Dark Bar (Appears on scroll down) -->
<div class="relative z-20 w-full bg-[#1C1816] border-y border-white/10 py-8 sm:py-10 shadow-2xl">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-white/10 rtl:divide-x-reverse">
            <!-- Stat 1 -->
            <div class="flex items-center justify-center gap-3 p-2">
                <div class="text-2xl sm:text-4xl font-black text-white font-mono">350+</div>
                <div class="flex flex-col items-start text-start">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-stone-300 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <span class="text-[11px] text-stone-300 font-bold">{{ app()->getLocale() === 'ar' ? 'عميل سعيد' : 'Happy Clients' }}</span>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="flex items-center justify-center gap-3 p-2">
                <div class="text-2xl sm:text-4xl font-black text-white font-mono">5+</div>
                <div class="flex flex-col items-start text-start">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-stone-300 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    </div>
                    <span class="text-[11px] text-stone-300 font-bold">{{ app()->getLocale() === 'ar' ? 'مشاريع جديدة' : 'New Projects' }}</span>
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="flex items-center justify-center gap-3 p-2">
                <div class="text-2xl sm:text-4xl font-black text-white font-mono">15+</div>
                <div class="flex flex-col items-start text-start">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-stone-300 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <span class="text-[11px] text-stone-300 font-bold">{{ app()->getLocale() === 'ar' ? 'سنة خبرة' : 'Years Experience' }}</span>
                </div>
            </div>

            <!-- Stat 4 -->
            <div class="flex items-center justify-center gap-3 p-2">
                <div class="text-2xl sm:text-4xl font-black text-white font-mono">400+</div>
                <div class="flex flex-col items-start text-start">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-stone-300 mb-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <span class="text-[11px] text-stone-300 font-bold">{{ app()->getLocale() === 'ar' ? 'وحدة سكنية' : 'Housing Units' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Section 2: Featured Projects ("استثمر في التميز") -->
    <section id="featured-projects" class="mb-20">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div>
                <h2 class="text-3xl sm:text-4xl font-black text-[#26201D] dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'استثمر في التميز' : 'Invest in Excellence' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm mt-1 max-w-xl font-light">
                    {{ app()->getLocale() === 'ar' ? 'نخبة من المشاريع العقارية الفاخرة المخصصة لتلبية أعلى مستويات التوقعات.' : 'Handpicked luxury real estate developments designed to exceed your highest expectations.' }}
                </p>
            </div>
            
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-[#8C7869] text-[#8C7869] dark:text-stone-200 hover:bg-[#8C7869] hover:text-white rounded-full text-xs font-bold transition-all shadow-xs">
                <span>{{ app()->getLocale() === 'ar' ? 'جميع المشاريع' : 'All Projects' }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Project Card 1: RUSU Residence -->
            <div class="bg-white dark:bg-stone-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-[#EBE6DF] dark:border-stone-800 group flex flex-col">
                <div class="relative h-64 w-full overflow-hidden bg-stone-100 dark:bg-stone-800">
                    <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=800&q=80" 
                         alt="RUSU Residence" 
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute top-4 start-4 flex gap-2 z-10">
                        <span class="px-3.5 py-1 bg-[#8C7869] text-white text-[11px] font-bold rounded-full shadow-md">
                            {{ app()->getLocale() === 'ar' ? 'مباع بالكامل' : 'Sold Out' }}
                        </span>
                    </div>
                    <div class="absolute top-4 end-4 z-10">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-stone-800 text-[11px] font-bold rounded-full shadow-xs">
                            {{ app()->getLocale() === 'ar' ? 'فيلا' : 'Villa' }}
                        </span>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-md bg-[#8C7869] text-white flex items-center justify-center text-[10px] font-black">R</div>
                            <h3 class="text-xl font-bold text-[#26201D] dark:text-white group-hover:text-[#8C7869] transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'رسو ريزيدنس' : 'RUSU Residence' }}
                            </h3>
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mb-2 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'الرياض، حي النرجس' : 'Riyadh, Al Nargis' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                            {{ app()->getLocale() === 'ar' ? 'فيلا مستقلة بتصميم عصري راقٍ، حديقة خاصة ومواقف سيارات مغطاة، في موقع متميز بحي النرجس قريب من الخدمات الرئيسية.' : 'A standalone villa with elegant modern design, a private garden, and covered parking, in a prime Al Nargis location near key amenities.' }}
                        </p>
                    </div>

                    <div>
                        <div class="pt-4 border-t border-stone-100 dark:border-stone-800 grid grid-cols-3 gap-2 text-center text-xs text-stone-700 dark:text-stone-300 mb-4">
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'التسليم' : 'Delivery' }}</span>
                                <span class="font-black">2026</span>
                            </div>
                            <div class="border-x border-stone-100 dark:border-stone-800">
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'Units' }}</span>
                                <span class="font-black">12</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                <span class="font-black">180 م²</span>
                            </div>
                        </div>

                        <a href="{{ route('property.list') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold rounded-xl text-xs transition-all duration-200 shadow-md hover:shadow-[#8C7869]/25">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل والحجز' : 'View Details & Book' }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Project Card 2: RUSU Gardens -->
            <div class="bg-white dark:bg-stone-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-[#EBE6DF] dark:border-stone-800 group flex flex-col">
                <div class="relative h-64 w-full overflow-hidden bg-stone-100 dark:bg-stone-800">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=800&q=80" 
                         alt="RUSU Gardens" 
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute top-4 start-4 flex gap-2 z-10">
                        <span class="px-3.5 py-1 bg-[#8C7869] text-white text-[11px] font-bold rounded-full shadow-md">
                            {{ app()->getLocale() === 'ar' ? 'متاح للبيع' : 'For Sale' }}
                        </span>
                        <span class="px-3 py-1 bg-emerald-500 text-white text-[11px] font-bold rounded-full shadow-md">
                            {{ app()->getLocale() === 'ar' ? 'جديد' : 'New' }}
                        </span>
                    </div>
                    <div class="absolute top-4 end-4 z-10">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-stone-800 text-[11px] font-bold rounded-full shadow-xs">
                            {{ app()->getLocale() === 'ar' ? 'تاون هاوس' : 'Townhouse' }}
                        </span>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-md bg-[#8C7869] text-white flex items-center justify-center text-[10px] font-black">R</div>
                            <h3 class="text-xl font-bold text-[#26201D] dark:text-white group-hover:text-[#8C7869] transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'رسو جاردنز' : 'RUSU Gardens' }}
                            </h3>
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mb-2 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'الرياض، حي الياسمين' : 'Riyadh, Al Yasmin' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                            {{ app()->getLocale() === 'ar' ? 'تاون هاوس عائلي بتصميم عصري من 3 طوابق ومدخل خاص، ضمن مجمع سكني مسوّر بحي الياسمين قريب من المدارس والمرافق.' : 'A modern 3-floor family townhouse with a private entrance, within a gated community in Al Yasmin near schools and amenities.' }}
                        </p>
                    </div>

                    <div>
                        <div class="pt-4 border-t border-stone-100 dark:border-stone-800 grid grid-cols-3 gap-2 text-center text-xs text-stone-700 dark:text-stone-300 mb-4">
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'التسليم' : 'Delivery' }}</span>
                                <span class="font-black">2025</span>
                            </div>
                            <div class="border-x border-stone-100 dark:border-stone-800">
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'Units' }}</span>
                                <span class="font-black">3</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                <span class="font-black">48 م²</span>
                            </div>
                        </div>

                        <a href="{{ route('property.list') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold rounded-xl text-xs transition-all duration-200 shadow-md hover:shadow-[#8C7869]/25">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل والحجز' : 'View Details & Book' }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Project Card 3: RUSU Heights -->
            <div class="bg-white dark:bg-stone-900 rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-[#EBE6DF] dark:border-stone-800 group flex flex-col">
                <div class="relative h-64 w-full overflow-hidden bg-stone-100 dark:bg-stone-800">
                    <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80" 
                         alt="RUSU Heights" 
                         loading="lazy"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute top-4 start-4 flex gap-2 z-10">
                        <span class="px-3.5 py-1 bg-amber-600 text-white text-[11px] font-bold rounded-full shadow-md">
                            {{ app()->getLocale() === 'ar' ? 'قيد الإنشاء' : 'Under Construction' }}
                        </span>
                    </div>
                    <div class="absolute top-4 end-4 z-10">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md text-stone-800 text-[11px] font-bold rounded-full shadow-xs">
                            {{ app()->getLocale() === 'ar' ? 'سكني' : 'Residential' }}
                        </span>
                    </div>
                </div>
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded-md bg-[#8C7869] text-white flex items-center justify-center text-[10px] font-black">R</div>
                            <h3 class="text-xl font-bold text-[#26201D] dark:text-white group-hover:text-[#8C7869] transition-colors">
                                {{ app()->getLocale() === 'ar' ? 'رسو هايتس' : 'RUSU Heights' }}
                            </h3>
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mb-2 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ app()->getLocale() === 'ar' ? 'الرياض، حي الملقا' : 'Riyadh, Al Malqa' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                            {{ app()->getLocale() === 'ar' ? 'مجمع سكني متكامل الخدمات بحي الملقا، يضم وحدات متنوعة المساحات مع مرافق مشتركة ومساحات خضراء واسعة.' : 'A fully-serviced residential complex in Al Malqa, featuring units of varying sizes with shared facilities and spacious green areas.' }}
                        </p>
                    </div>

                    <div>
                        <div class="pt-4 border-t border-stone-100 dark:border-stone-800 grid grid-cols-3 gap-2 text-center text-xs text-stone-700 dark:text-stone-300 mb-4">
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'التسليم' : 'Delivery' }}</span>
                                <span class="font-black">2026</span>
                            </div>
                            <div class="border-x border-stone-100 dark:border-stone-800">
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'Units' }}</span>
                                <span class="font-black">28</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-stone-400">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                <span class="font-black">120 م²</span>
                            </div>
                        </div>

                        <a href="{{ route('property.list') }}" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold rounded-xl text-xs transition-all duration-200 shadow-md hover:shadow-[#8C7869]/25">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل والحجز' : 'View Details & Book' }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Why Choose Us ("نرتقي بتجربة الاستثمار العقاري") -->
    <section class="mb-20 bg-white/70 dark:bg-stone-900/60 rounded-[2.5rem] p-8 sm:p-12 border border-[#EBE6DF] dark:border-stone-800 shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Features -->
            <div>
                <h2 class="text-3xl sm:text-4xl font-black text-[#26201D] dark:text-white leading-tight mb-4">
                    {{ app()->getLocale() === 'ar' ? 'نرتقي بتجربة الاستثمار العقاري' : 'Elevating Real Estate Investment' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm mb-8 font-light leading-relaxed">
                    {{ app()->getLocale() === 'ar' ? 'نحن لا نبني مجرد مبان، بل نبتكر أسلوب حياة يرتقي بتطلعاتك وقيم استثمارك.' : 'We don’t just build structures; we innovate a lifestyle that elevates your aspirations.' }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Feature 1 -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-stone-800 border border-[#EBE6DF]/70 dark:border-stone-700/80 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center text-lg mb-3">
                            <svg class="w-5 h-5 text-stone-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#26201D] dark:text-white mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الجودة والاستدامة' : 'Quality & Sustainability' }}
                        </h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'نلتزم بأعلى معايير البناء المستدام والتصاميم العصرية الممتازة.' : 'We adhere to the highest sustainable construction standards.' }}
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-stone-800 border border-[#EBE6DF]/70 dark:border-stone-700/80 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center text-lg mb-3">
                            <svg class="w-5 h-5 text-stone-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#26201D] dark:text-white mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الثقة والشفافية' : 'Trust & Transparency' }}
                        </h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'نضع رضا العميل أولاً عبر التعامل الشفاف الموثوق في كافة المراحل.' : 'Putting clients first with transparent and trustworthy operations.' }}
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-stone-800 border border-[#EBE6DF]/70 dark:border-stone-700/80 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center text-lg mb-3">
                            <svg class="w-5 h-5 text-stone-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#26201D] dark:text-white mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الابتكار والتصميم' : 'Innovation & Design' }}
                        </h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'تصاميم معمارية فريدة تلبي احتياجات وطموحات الحياة الحديثة.' : 'Architectural designs crafted for contemporary lifestyle.' }}
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-5 rounded-2xl bg-white dark:bg-stone-800 border border-[#EBE6DF]/70 dark:border-stone-700/80 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#8C7869] text-white flex items-center justify-center text-lg mb-3">
                            <svg class="w-5 h-5 text-stone-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#26201D] dark:text-white mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الاستجابة السريعة' : 'Rapid Response' }}
                        </h3>
                        <p class="text-xs text-stone-500 dark:text-stone-400 leading-relaxed font-light">
                            {{ app()->getLocale() === 'ar' ? 'خدمة عملاء متكاملة تضمن تجربة سلسة من الاستفسار حتى التسليم.' : 'Dedicated customer care ensuring seamless service.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Showcase Image with Floating Badges -->
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl border border-stone-200 dark:border-stone-800">
                    <img src="https://images.unsplash.com/photo-1600566752355-35792bedcfea?auto=format&fit=crop&w=800&q=80" 
                         alt="Interior Showcase" 
                         class="w-full h-[480px] object-cover">
                </div>
                
                <!-- Floating Badge Top Right -->
                <div class="absolute top-6 end-6 bg-white/90 dark:bg-stone-900/90 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl border border-white/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#8C7869] text-white flex items-center justify-center font-bold text-xs">ISO</div>
                    <div>
                        <p class="text-xs font-bold text-[#26201D] dark:text-white">{{ app()->getLocale() === 'ar' ? 'جودة معتمدة' : 'Certified Quality' }}</p>
                        <p class="text-[10px] text-stone-500">ISO 9001</p>
                    </div>
                </div>

                <!-- Floating Badge Bottom Left -->
                <div class="absolute bottom-6 start-6 bg-white/95 dark:bg-stone-900/95 backdrop-blur-md p-4 rounded-2xl shadow-xl border border-white/50 flex items-center gap-4">
                    <div class="text-2xl font-black text-[#26201D] dark:text-white font-mono">98%</div>
                    <div class="text-xs">
                        <div class="flex text-amber-500 mb-0.5">★★★★★</div>
                        <p class="font-bold text-stone-700 dark:text-stone-300 text-[11px]">{{ app()->getLocale() === 'ar' ? 'نسبة رضا العملاء' : 'Client Satisfaction' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 4: About Company ("رسو.. أكثر من مجرد عقار") -->
    <section class="mb-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1 rounded-3xl overflow-hidden shadow-xl border border-stone-200 dark:border-stone-800">
                <img src="https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=80" 
                     alt="RUSU Real Estate Story" 
                     class="w-full h-[420px] object-cover">
            </div>

            <div class="order-1 lg:order-2">
                <h2 class="text-3xl sm:text-4xl font-black text-[#26201D] dark:text-white leading-tight mb-4">
                    {{ app()->getLocale() === 'ar' ? 'رسو.. أكثر من مجرد عقار' : 'RUSU.. More Than Real Estate' }}
                </h2>
                <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm leading-relaxed mb-6 font-light">
                    {{ app()->getLocale() === 'ar' ? 'منذ عام 2009 ونحن نعيد تعريف مفهوم التطوير العقاري في المملكة العربية السعودية، حيث ندمج بين الجمال المعماري والوظيفة العملية، لنقدم مجتمعات سكنية فاخرة تلبي تطلعات المستقبل.' : 'Since 2009, we have been redefining real estate development by fusing architectural elegance with functionality.' }}
                </p>

                <!-- Value Pill Toggles -->
                <div class="space-y-3 mb-8">
                    <div class="p-3.5 rounded-2xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 flex items-center justify-between text-xs font-bold text-[#26201D] dark:text-stone-200 shadow-xs">
                        <span>• {{ app()->getLocale() === 'ar' ? 'الرؤية والهدف' : 'Vision & Goals' }}</span>
                        <span class="text-stone-400">&larr;</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 flex items-center justify-between text-xs font-bold text-[#26201D] dark:text-stone-200 shadow-xs">
                        <span>• {{ app()->getLocale() === 'ar' ? 'الابتكار والحلول' : 'Innovation & Solutions' }}</span>
                        <span class="text-stone-400">&larr;</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 flex items-center justify-between text-xs font-bold text-[#26201D] dark:text-stone-200 shadow-xs">
                        <span>• {{ app()->getLocale() === 'ar' ? 'الشغف والاستدامة' : 'Passion & Sustainability' }}</span>
                        <span class="text-stone-400">&larr;</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="px-5 py-2.5 rounded-full bg-[#8C7869] text-white text-xs font-mono font-bold">
                        2009 {{ app()->getLocale() === 'ar' ? 'تأسست عام' : 'Founded' }}
                    </div>
                    <a href="{{ route('about') }}" class="text-xs font-bold text-[#8C7869] dark:text-stone-200 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'تعرف على المزيد عنا ↗' : 'Learn More About Us ↗' }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 5: Testimonials ("يقولون عن تجربتهم") -->
    <section class="mb-20">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl sm:text-4xl font-black text-[#26201D] dark:text-white mb-2">
                {{ app()->getLocale() === 'ar' ? 'يقولون عن تجربتهم' : 'What Our Clients Say' }}
            </h2>
            <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm font-light">
                {{ app()->getLocale() === 'ar' ? 'انطباعات وآراء عملائنا الذين وثقوا في مشاريعنا العقارية.' : 'Feedback and experiences from clients who trusted our real estate developments.' }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 rounded-3xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <div class="text-amber-500 mb-3 text-sm">★★★★★</div>
                    <p class="text-xs text-stone-600 dark:text-stone-300 leading-relaxed font-light">
                        "تجربة الاستثمار مع شركة رسو كانت ممتازة جداً. الدقة في المواعيد وجودة التشطيب في مشروع رسو ريزيدنس تفوقت على توقعاتي."
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <div class="w-9 h-9 rounded-full bg-[#8C7869] text-white flex items-center justify-center font-bold text-xs">ع</div>
                    <div>
                        <h4 class="text-xs font-bold text-[#26201D] dark:text-white">عبدالله الشمري</h4>
                        <p class="text-[10px] text-stone-400">مستثمر عقاري</p>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <div class="text-amber-500 mb-3 text-sm">★★★★★</div>
                    <p class="text-xs text-stone-600 dark:text-stone-300 leading-relaxed font-light">
                        "خدمة العملاء والشفافية في التعامل كانت رائعة. استلمنا الوحدة السكنية في الوقت المحدد وبالمواصفات المتفق عليها بالكامل."
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <div class="w-9 h-9 rounded-full bg-[#8C7869] text-white flex items-center justify-center font-bold text-xs">س</div>
                    <div>
                        <h4 class="text-xs font-bold text-[#26201D] dark:text-white">سارة العتيبي</h4>
                        <p class="text-[10px] text-stone-400">مالكة تاون هاوس</p>
                    </div>
                </div>
            </div>

            <div class="p-6 rounded-3xl bg-white dark:bg-stone-900 border border-[#EBE6DF] dark:border-stone-800 shadow-sm flex flex-col justify-between">
                <div class="mb-4">
                    <div class="text-amber-500 mb-3 text-sm">★★★★★</div>
                    <p class="text-xs text-stone-600 dark:text-stone-300 leading-relaxed font-light">
                        "الموقع الممتاز والتصاميم الحديثة لمشاريع رسو جعلت القرار الاستثماري سهلاً ومربحاً للغاية."
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-stone-100 dark:border-stone-800">
                    <div class="w-9 h-9 rounded-full bg-[#8C7869] text-white flex items-center justify-center font-bold text-xs">ف</div>
                    <div>
                        <h4 class="text-xs font-bold text-[#26201D] dark:text-white">م. فيصل المطيري</h4>
                        <p class="text-[10px] text-stone-400">مشتري وحدة سكنية</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Property Map Section -->
    <section class="mb-16">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <h2 class="text-2xl sm:text-3xl font-black text-[#26201D] dark:text-white mb-2">{{ __('Interactive Property Map') }}</h2>
            <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm font-light">{{ __('Explore listings geographically across cities and neighborhoods') }}</p>
        </div>
        <div class="rounded-3xl overflow-hidden shadow-lg border border-[#EBE6DF] dark:border-stone-800">
            <x-property-map :properties="$mapProperties" />
        </div>
    </section>
</div>
@endsection
