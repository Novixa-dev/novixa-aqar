@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-8">
    <!-- Hero Banner & Smart Search -->
    <div class="bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 rounded-3xl p-8 lg:p-14 text-white shadow-2xl mb-12 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-4xl lg:max-w-5xl mx-auto text-center relative z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md text-blue-200 text-xs font-semibold uppercase tracking-wider mb-6 border border-white/10">
                ✨ {{ app()->getLocale() === 'ar' ? 'الجيل القادم من المنصات العقارية' : 'Next Generation Real Estate Platform' }}
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight mb-6">
                {{ __('Find Your Dream Property in Yemen & Beyond') }}
            </h1>
            <p class="text-base sm:text-lg text-blue-100/90 mb-10 max-w-2xl mx-auto font-light leading-relaxed">
                {{ __('Explore wide range of residential and commercial properties with Dar Hadhramaut Real Estate.') }}
            </p>

            <!-- Search Form Card -->
            <form action="{{ route('property.list') }}" method="GET" class="bg-gray-900/90 backdrop-blur-xl p-4 sm:p-6 rounded-3xl shadow-2xl text-white flex flex-col md:flex-row gap-3 border border-gray-700/80 items-center">
                <!-- Keyword Input -->
                <div class="flex-1 relative w-full">
                    <div class="absolute start-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن عقارات، مناطق، أحياء...' : 'Search properties, areas, neighborhoods...' }}" 
                           class="w-full ps-11 pe-4 py-3.5 bg-gray-800/90 border border-gray-700/80 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white placeholder-gray-400 transition-all">
                </div>
                
                <!-- Status Select Dropdown -->
                <div class="relative w-full md:w-48 group">
                    <select name="status" 
                            style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                            class="w-full ps-4 pe-10 py-3.5 bg-gray-800/90 border border-gray-700/80 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white appearance-none cursor-pointer [background-image:none] transition-all">
                        <option value="" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'الكل (بيع وإيجار)' : 'All (Sale & Rent)' }}</option>
                        <option value="For Sale" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</option>
                        <option value="For Rent" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</option>
                    </select>
                    <div class="absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Property Type Select Dropdown -->
                <div class="relative w-full md:w-56 group">
                    <select name="property_type" 
                            style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                            class="w-full ps-4 pe-10 py-3.5 bg-gray-800/90 border border-gray-700/80 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white appearance-none cursor-pointer [background-image:none] transition-all">
                        <option value="" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                        <option value="Apartment" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Apartment' }}</option>
                        <option value="Villa" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'فيلا فاخرة' : 'Villa' }}</option>
                        <option value="Land" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'أرض سكنية / تجارية' : 'Land / Plot' }}</option>
                        <option value="Commercial" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'عقار تجاري' : 'Commercial' }}</option>
                        <option value="Building" class="bg-gray-800 text-white py-2">{{ app()->getLocale() === 'ar' ? 'عمارة استثمارية' : 'Building' }}</option>
                    </select>
                    <div class="absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full md:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-2xl shadow-lg hover:shadow-blue-500/30 transition-all flex items-center justify-center gap-2 text-sm shrink-0 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Featured Properties Grid -->
    <section class="mb-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'عقارات مميزة ومختارة' : 'Featured Properties' }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">{{ app()->getLocale() === 'ar' ? 'استكشف نخبة العروض العقارية المفحوصة والموثقة' : 'Explore our handpicked verified premium listings' }}</p>
            </div>
            <a href="{{ route('property.list') }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                <span>{{ app()->getLocale() === 'ar' ? 'عرض كافة العقارات' : 'Browse All Properties' }}</span>
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredProperties as $property)
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm hover:shadow-2xl hover:border-blue-500/50 transition-all duration-300 border border-gray-100 dark:border-gray-700/80 overflow-hidden flex flex-col group">
                    <div class="relative h-60 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                        <a href="{{ route('property.detail', $property->id) }}">
                            <img src="{{ $property->image_url }}" 
                                 alt="{{ $property->title }}" 
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </a>
                        <div class="absolute top-3 start-3 flex gap-1.5 z-10">
                            <span class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg shadow-sm">
                                {{ $property->status === 'For Rent' ? (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') : (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') }}
                            </span>
                            @if($property->is_featured)
                                <span class="px-2.5 py-1 bg-amber-500 text-gray-900 text-xs font-black rounded-lg shadow-sm">
                                    {{ app()->getLocale() === 'ar' ? 'مميز' : 'Featured' }}
                                </span>
                            @endif
                        </div>
                        <div class="absolute bottom-3 start-3 end-3 flex justify-between items-center z-10">
                            <span class="bg-gray-900/80 backdrop-blur-md text-gray-200 text-xs px-2.5 py-1 rounded-md font-medium">
                                {{ $property->translated_type }}
                            </span>
                            <span class="bg-gray-900/80 backdrop-blur-md text-blue-400 text-xs px-2.5 py-1 rounded-md font-semibold">
                                📍 {{ Str::limit($property->location, 20) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-baseline justify-between mb-2">
                                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                                    {{ $property->formatted_price }}
                                </div>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="{{ route('property.detail', $property->id) }}">
                                    {{ $property->title }}
                                </a>
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-2 leading-relaxed">
                                {{ $property->description }}
                            </p>
                        </div>

                        <div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-700 dark:text-gray-300 py-3 border-t border-gray-100 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-900/40 rounded-xl px-2 mb-4">
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الغرف' : 'Beds' }}</span>
                                    <span class="font-bold">🛏️ {{ $property->bedrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الحمامات' : 'Baths' }}</span>
                                    <span class="font-bold">🚿 {{ $property->bathrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                    <span class="font-bold">📐 {{ $property->area_sqft ?? 150 }} م²</span>
                                </div>
                            </div>

                            <a href="{{ route('property.detail', $property->id) }}" 
                               class="w-full block text-center py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm hover:shadow transition-all">
                                {{ app()->getLocale() === 'ar' ? 'تفاصيل العقار والتواصل' : 'View Details & Contact' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-gray-800/50 rounded-3xl border border-dashed border-gray-700">
                    <p class="text-gray-400 text-sm">{{ app()->getLocale() === 'ar' ? 'لا توجد عقارات مميزة حالياً.' : 'No featured properties available.' }}</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Interactive Property Map Section -->
    <section class="mb-16">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-2">{{ __('Interactive Property Map') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">{{ __('Explore listings geographically across cities and neighborhoods') }}</p>
        </div>
        <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-700">
            <x-property-map :properties="$mapProperties" />
        </div>
    </section>

    <!-- Why Choose Us Features Grid -->
    <section class="mb-12 py-12 bg-gray-50 dark:bg-gray-800/40 rounded-3xl p-8 lg:p-12 border border-gray-100 dark:border-gray-800">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mb-3">{{ __('Why Choose Dar Hadhramaut?') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base">{{ __('We provide comprehensive real estate management and marketing solutions') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl mb-4">
                    🤖
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Smart Solutions') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __('Interactive virtual tours, 3D property models and expert valuations.') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700">
                <div class="w-12 h-12 rounded-xl bg-green-50 dark:bg-green-900/40 text-green-600 dark:text-green-400 flex items-center justify-center text-2xl mb-4">
                    🛡️
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Verified Listings') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __('Every listing is reviewed and validated for maximum security and peace of mind.') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xs border border-gray-100 dark:border-gray-700">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl mb-4">
                    ⚡
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Seamless Experience') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __('Effortless search, direct agent communication and automated bookings.') }}</p>
            </div>
        </div>
    </section>
</div>
@endsection
