<div>
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-blue-50 to-gray-50 dark:from-blue-950/40 dark:via-gray-900 dark:to-gray-900 py-12 border-b border-gray-200 dark:border-gray-800">
        <div class="container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-200 dark:border-blue-700/50 shadow-inner">
                {{ app()->getLocale() === 'ar' ? 'بوابة العقارات المتميزة في اليمن' : 'Premier Real Estate Portal in Yemen' }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'ar' ? 'استكشف أفضل العقارات المتاحة للبيع والإيجار' : 'Explore Properties for Sale & Rent' }}
            </h1>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 leading-relaxed font-normal">
                {{ app()->getLocale() === 'ar' ? 'تشكيلة واسعة من الشقق، الفلل، والأراضي في حضرموت وصنعاء وعدن مع فحص قانوني وتثمين دقيق.' : 'A wide selection of verified apartments, villas, and plots in Hadramout, Sanaa, and Aden.' }}
            </p>
        </div>
    </div>

    <!-- Main Content & Filters -->
    <div class="container mx-auto px-4 lg:px-8 py-10">
        <!-- Filter Panel -->
        <div class="mb-10 bg-white/95 dark:bg-gray-900/90 backdrop-blur-xl p-6 rounded-3xl shadow-sm dark:shadow-xl border border-gray-200 dark:border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search Input -->
                <div class="relative col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'البحث بالاسم أو الحي' : 'Search by title or area' }}
                    </label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن شقة، فيلا، حي فوه، الديس، حدة...' : 'Search apartment, villa, Mukalla, Sanaa...' }}">
                        <span class="absolute end-3 top-3.5 text-gray-400"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg></span>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'نوع العملية' : 'Purpose' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="status"
                            class="w-full ps-4 pe-10 py-3 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-gray-900 dark:text-white appearance-none cursor-pointer">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'الكل (بيع وإيجار)' : 'All (Sale & Rent)' }}</option>
                            <option value="For Sale">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</option>
                            <option value="For Rent">{{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}</option>
                        </select>
                        <div class="absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Property Type Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="propertyType"
                            class="w-full ps-4 pe-10 py-3 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-gray-900 dark:text-white appearance-none cursor-pointer">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                            <option value="Apartment">{{ app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Apartment' }}</option>
                            <option value="Villa">{{ app()->getLocale() === 'ar' ? 'فيلا فاخرة' : 'Luxury Villa' }}</option>
                            <option value="Land">{{ app()->getLocale() === 'ar' ? 'أرض سكنية / تجارية' : 'Land / Plot' }}</option>
                            <option value="Commercial">{{ app()->getLocale() === 'ar' ? 'عقار تجاري / مكتب' : 'Commercial / Office' }}</option>
                            <option value="Building">{{ app()->getLocale() === 'ar' ? 'عمارة استثمارية' : 'Building' }}</option>
                        </select>
                        <div class="absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Filters: Bedrooms & Price -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800/80 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'غرف النوم (الحد الأدنى)' : 'Min Bedrooms' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="minBedrooms"
                            class="w-full ps-3 pe-8 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-xs appearance-none cursor-pointer">
                            <option value="0">{{ app()->getLocale() === 'ar' ? 'أي عدد' : 'Any' }}</option>
                            <option value="1">1+ {{ app()->getLocale() === 'ar' ? 'غرفة' : 'Bed' }}</option>
                            <option value="2">2+ {{ app()->getLocale() === 'ar' ? 'غرف' : 'Beds' }}</option>
                            <option value="3">3+ {{ app()->getLocale() === 'ar' ? 'غرف' : 'Beds' }}</option>
                            <option value="4">4+ {{ app()->getLocale() === 'ar' ? 'غرف' : 'Beds' }}</option>
                        </select>
                        <div class="absolute end-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورات المياه' : 'Min Bathrooms' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="minBathrooms"
                            class="w-full ps-3 pe-8 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-xs appearance-none cursor-pointer">
                            <option value="0">{{ app()->getLocale() === 'ar' ? 'أي عدد' : 'Any' }}</option>
                            <option value="1">1+ {{ app()->getLocale() === 'ar' ? 'حمام' : 'Bath' }}</option>
                            <option value="2">2+ {{ app()->getLocale() === 'ar' ? 'حمامات' : 'Baths' }}</option>
                            <option value="3">3+ {{ app()->getLocale() === 'ar' ? 'حمامات' : 'Baths' }}</option>
                        </select>
                        <div class="absolute end-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'السعر الأدنى (ر.ي)' : 'Min Price' }}
                    </label>
                    <input type="number" wire:model.live.debounce.400ms="minPrice" placeholder="0"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 dark:text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'السعر الأقصى (ر.ي)' : 'Max Price' }}
                    </label>
                    <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="100,000,000"
                        class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white text-xs">
                </div>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($properties->total()) }}</span>
                {{ app()->getLocale() === 'ar' ? 'عقار متاح' : 'properties available' }}
            </p>
            @if ($this->active_filters_count > 0)
                <button type="button" wire:click="resetFilters" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    {{ app()->getLocale() === 'ar' ? 'مسح الفلاتر (' . $this->active_filters_count . ')' : 'Clear filters (' . $this->active_filters_count . ')' }}
                </button>
            @endif
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            @forelse($properties as $property)
                <div class="bg-white dark:bg-gray-800/90 rounded-2xl shadow-sm hover:shadow-2xl hover:border-blue-500/50 transition-all duration-300 border border-gray-100 dark:border-gray-700/80 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Property Image with Badges -->
                        <div class="relative h-56 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                            <a href="{{ route('property.detail', $property->id) }}">
                                <img src="{{ $property->image_url }}"
                                    alt="{{ $property->title }}" 
                                    loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                            </a>
                            <div class="absolute top-3 start-3 flex flex-wrap gap-1.5 z-10">
                                <span class="rounded-lg px-3 py-1 text-xs font-black shadow-md {{ $property->status === 'For Rent' ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white' }}">
                                    {{ $property->status === 'For Rent' ? (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') : (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') }}
                                </span>
                                @if($property->is_featured)
                                    <span class="rounded-lg px-2.5 py-1 text-xs font-bold bg-amber-500 text-gray-900 shadow-md">
                                        {{ app()->getLocale() === 'ar' ? 'مميز' : 'Featured' }}
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-3 start-3 end-3 flex justify-between items-center z-10">
                                <span class="bg-white/90 dark:bg-gray-900/80 backdrop-blur-md text-gray-700 dark:text-gray-200 text-xs px-2.5 py-1 rounded-md font-medium">
                                    {{ $property->translated_type }}
                                </span>
                                <span class="bg-white/90 dark:bg-gray-900/80 backdrop-blur-md text-blue-600 dark:text-blue-400 text-xs px-2.5 py-1 rounded-md font-semibold">
                                    <svg class="w-4 h-4 inline-block align-[-2px]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> {{ Str::limit($property->location, 20) }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5">
                            <div class="flex items-baseline justify-between mb-2">
                                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400">
                                    {{ $property->formatted_price }}
                                </div>
                            </div>

                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                <a href="{{ route('property.detail', $property->id) }}">
                                    {{ $property->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                {{ $property->description }}
                            </p>

                            <!-- Specs Pill Row -->
                            <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-700 dark:text-gray-300 py-3 border-t border-gray-100 dark:border-gray-700/80 bg-gray-50 dark:bg-gray-900/40 rounded-xl px-2">
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الغرف' : 'Beds' }}</span>
                                    <span class="font-bold inline-flex items-center gap-1"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-5a2 2 0 012-2h14a2 2 0 012 2v5m0 0v2m0-2H3m0 0v2m3.5-10V8a2 2 0 012-2h7a2 2 0 012 2v2" /></svg> {{ $property->bedrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الحمامات' : 'Baths' }}</span>
                                    <span class="font-bold inline-flex items-center gap-1"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16v2a4 4 0 01-4 4H8a4 4 0 01-4-4v-2zm2 0V6a2 2 0 012-2h1.5M6 18l-1 2m13-2l1 2" /></svg> {{ $property->bathrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-400 text-[10px]">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                    <span class="font-bold inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 4.5H4.5M9 4.5L3.75 9.75M15 9V4.5M15 4.5h4.5M15 4.5l5.25 5.25M9 15v4.5M9 19.5H4.5M9 19.5l-5.25-5.25M15 15v4.5M15 19.5h4.5M15 19.5l5.25-5.25" /></svg> {{ $property->area_sqft ?? 150 }} م²</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer Action -->
                    <div class="p-5 pt-0">
                        <a href="{{ route('property.detail', $property->id) }}"
                           class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-xs transition-all duration-200 shadow-md hover:shadow-blue-500/25">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل والحجز' : 'View Details & Book' }}</span>
                            <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-gray-50 dark:bg-gray-900/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-800">
                    <span class="inline-block mb-3 text-gray-300 dark:text-gray-600"><svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" /></svg></span>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد عقارات مطابقة' : 'No properties found' }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mb-4">
                        {{ app()->getLocale() === 'ar' ? 'يرجى تجربة معايير بحث أخرى أو إعادة تعيين الفلاتر.' : 'Try changing your search filters to find more properties.' }}
                    </p>
                    @if ($this->active_filters_count > 0)
                        <button type="button" wire:click="resetFilters"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-colors">
                            {{ app()->getLocale() === 'ar' ? 'إعادة تعيين الفلاتر' : 'Reset filters' }}
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Custom Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>
</div>
