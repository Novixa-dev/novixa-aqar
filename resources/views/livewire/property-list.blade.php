<div>
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-blue-900/20 via-gray-900 to-gray-900 py-12 border-b border-gray-800">
        <div class="container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-blue-900/60 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-700/50 shadow-inner">
                {{ app()->getLocale() === 'ar' ? '✨ بوابة العقارات المتميزة في اليمن' : '✨ Premier Real Estate Portal in Yemen' }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'ar' ? 'استكشف أفضل العقارات المتاحة للبيع والإيجار' : 'Explore Properties for Sale & Rent' }}
            </h1>
            <p class="text-sm sm:text-base text-gray-400 leading-relaxed font-normal">
                {{ app()->getLocale() === 'ar' ? 'تشكيلة واسعة من الشقق، الفلل، والأراضي في حضرموت وصنعاء وعدن مع فحص قانوني وتثمين دقيق.' : 'A wide selection of verified apartments, villas, and plots in Hadramout, Sanaa, and Aden.' }}
            </p>
        </div>
    </div>

    <!-- Main Content & Filters -->
    <div class="container mx-auto px-4 lg:px-8 py-10">
        <!-- Advanced Filter Box -->
        <div class="mb-10 bg-gray-900/90 backdrop-blur-xl p-6 rounded-3xl shadow-xl border border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Search Input -->
                <div class="relative col-span-1 md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'البحث بالاسم أو الحي' : 'Search by title or area' }}
                    </label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="w-full px-4 py-3 bg-gray-800/80 border border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white placeholder-gray-500"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن شقة، فيلا، حي فوه، الديس، حدة...' : 'Search apartment, villa, Mukalla, Sanaa...' }}">
                        <span class="absolute end-3 top-3.5 text-gray-400">🔍</span>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'نوع العملية' : 'Purpose' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="status"
                            class="w-full ps-4 pe-10 py-3 bg-gray-800/80 border border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white appearance-none cursor-pointer">
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
                    <label class="block text-xs font-bold text-gray-400 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="propertyType"
                            class="w-full ps-4 pe-10 py-3 bg-gray-800/80 border border-gray-700 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none text-sm text-white appearance-none cursor-pointer">
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-800/80 text-xs">
                <div>
                    <label class="block font-bold text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'غرف النوم (الحد الأدنى)' : 'Min Bedrooms' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="minBedrooms"
                            class="w-full ps-3 pe-8 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-xs appearance-none cursor-pointer">
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
                    <label class="block font-bold text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورات المياه' : 'Min Bathrooms' }}
                    </label>
                    <div class="relative">
                        <select wire:model.live="minBathrooms"
                            class="w-full ps-3 pe-8 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-xs appearance-none cursor-pointer">
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
                    <label class="block font-bold text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'السعر الأدنى (ر.ي)' : 'Min Price' }}
                    </label>
                    <input type="number" wire:model.live.debounce.400ms="minPrice" placeholder="0"
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-xs">
                </div>

                <div>
                    <label class="block font-bold text-gray-400 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'السعر الأقصى (ر.ي)' : 'Max Price' }}
                    </label>
                    <input type="number" wire:model.live.debounce.400ms="maxPrice" placeholder="100,000,000"
                        class="w-full px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white text-xs">
                </div>
            </div>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            @forelse($properties as $property)
                <div class="bg-gray-800/90 rounded-2xl shadow-lg hover:shadow-2xl hover:border-blue-500/50 transition-all duration-300 border border-gray-700/80 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Property Image with Badges -->
                        <div class="relative h-56 w-full overflow-hidden bg-gray-900">
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
                                <span class="bg-gray-900/80 backdrop-blur-md text-gray-200 text-xs px-2.5 py-1 rounded-md font-medium">
                                    {{ $property->translated_type }}
                                </span>
                                <span class="bg-gray-900/80 backdrop-blur-md text-blue-400 text-xs px-2.5 py-1 rounded-md font-semibold">
                                    📍 {{ Str::limit($property->location, 20) }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5">
                            <div class="flex items-baseline justify-between mb-2">
                                <div class="text-xl font-black text-emerald-400">
                                    {{ number_format($property->price, 0) }} <span class="text-xs font-normal text-gray-400">{{ app()->getLocale() === 'ar' ? 'ر.ي' : 'YER' }}</span>
                                </div>
                                <div class="text-xs text-gray-400 font-mono">
                                    ≈ ${{ number_format($property->price / 530, 0) }}
                                </div>
                            </div>

                            <h3 class="text-base font-bold text-white mb-2 line-clamp-1 group-hover:text-blue-400 transition-colors">
                                <a href="{{ route('property.detail', $property->id) }}">
                                    {{ $property->title }}
                                </a>
                            </h3>

                            <p class="text-xs text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                {{ $property->description }}
                            </p>

                            <!-- Specs Pill Row -->
                            <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-300 py-3 border-t border-gray-700/80 bg-gray-900/40 rounded-xl px-2">
                                <div>
                                    <span class="block text-gray-500 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الغرف' : 'Beds' }}</span>
                                    <span class="font-bold">🛏️ {{ $property->bedrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 text-[10px]">{{ app()->getLocale() === 'ar' ? 'الحمامات' : 'Baths' }}</span>
                                    <span class="font-bold">🚿 {{ $property->bathrooms ?? 1 }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 text-[10px]">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</span>
                                    <span class="font-bold">📐 {{ $property->area_sqft ?? 150 }} م²</span>
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
                <div class="col-span-full text-center py-20 bg-gray-900/50 rounded-3xl border border-dashed border-gray-800">
                    <span class="text-4xl block mb-3">🏡</span>
                    <h3 class="text-lg font-bold text-white mb-1">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد عقارات مطابقة' : 'No properties found' }}
                    </h3>
                    <p class="text-gray-400 text-xs">
                        {{ app()->getLocale() === 'ar' ? 'يرجى تجربة معايير بحث أخرى أو إعادة تعيين الفلاتر.' : 'Try changing your search filters to find more properties.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Custom Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>
</div>
