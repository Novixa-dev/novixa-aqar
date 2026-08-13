<div>
    <!-- Hero Banner -->
    <div class="bg-gradient-to-b from-blue-50/50 to-white dark:from-gray-900 dark:to-gray-800 py-12 border-b border-gray-100 dark:border-gray-800">
        <div class="container mx-auto px-4 lg:px-8 text-center max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
                {{ __('Properties') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'ar' ? 'العقارات المتاحة للبيع والإيجار' : 'Properties for Sale & Rent' }}
            </h1>
            <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
                {{ app()->getLocale() === 'ar' ? 'استعرض أحدث الفرص العقارية السكنية والتجارية المفحوصة مع خيارات فلترة متقدمة وتواصل مباشر.' : 'Explore verified residential and commercial properties with smart filtering and direct booking.' }}
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 lg:px-8 py-10">
        <!-- Search Bar -->
        <div class="mb-8 bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="relative w-full">
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white"
                    placeholder="{{ __('Search properties...') }}">
            </div>
        </div>

        <!-- Property Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
            @forelse($properties as $property)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col justify-between group">
                    <div>
                        <div class="relative h-52 w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                            <a href="{{ route('property.detail', $property->id) }}">
                                <img src="{{ $property->getFirstMediaUrl('images') ?: asset('images/property-placeholder.png') }}"
                                    alt="{{ $property->title }}" 
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </a>
                            <div class="absolute top-3 start-3 flex flex-wrap gap-1.5">
                                <span class="rounded-lg px-2.5 py-1 text-xs font-bold bg-blue-600/90 text-white backdrop-blur-md shadow-xs">
                                    {{ $property->status === 'For Rent' ? __('For Rent') : __('For Sale') }}
                                </span>
                                @if($property->is_featured)
                                    <span class="rounded-lg px-2.5 py-1 text-xs font-bold bg-amber-500 text-white shadow-xs">
                                        {{ __('Featured') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="text-xl font-black text-blue-600 dark:text-blue-400 mb-2">
                                ${{ number_format($property->price, 0) }}
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-blue-600 transition-colors">
                                <a href="{{ route('property.detail', $property->id) }}">
                                    {{ $property->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                                {{ $property->description }}
                            </p>

                            <div class="flex items-center justify-between text-xs font-medium text-gray-500 dark:text-gray-400 py-2.5 border-t border-gray-100 dark:border-gray-700">
                                <span>🛏️ {{ $property->bedrooms ?? 0 }} {{ __('Beds') }}</span>
                                <span>🚿 {{ $property->bathrooms ?? 0 }} {{ __('Baths') }}</span>
                                <span>📐 {{ $property->area_sqft ?? 0 }} {{ __('Sq Ft') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 pt-0">
                        <a href="{{ route('property.detail', $property->id) }}"
                           class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-gray-50 dark:bg-gray-700/60 hover:bg-blue-600 hover:text-white text-gray-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition-all duration-200">
                            <span>{{ __('View Details') }}</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد عقارات مطابقة لخيارات البحث.' : 'No properties found matching your criteria.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $properties->links() }}
        </div>
    </div>
</div>
