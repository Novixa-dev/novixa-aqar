<div>
@push('meta')
    {{-- title, description, Open Graph and Twitter tags come from the layout (see PropertyDetail::render). --}}
    <!-- Schema.org JSON-LD Structured Data -->
    @php
        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => strip_tags($property->title),
            'description' => \Illuminate\Support\Str::limit(strip_tags($property->description), 250),
            'url' => url()->current(),
            'image' => $property->getFirstMediaUrl('images') ?: asset('images/property-placeholder.png'),
            'offers' => [
                '@type' => 'Offer',
                'price' => $property->price,
                'priceCurrency' => $property->currency ?: 'YER',
                'availability' => 'https://schema.org/InStock',
            ],
            // JSON_HEX_TAG escapes < and > as </>, which alone
            // fully blocks a "</script>" breakout regardless of slash
            // escaping (a prior version relied on JSON_UNESCAPED_SLASHES
            // for that instead, which left "</script>" unescaped -- a
            // stored-XSS path reachable through the public, unauthenticated
            // property-submission form). JSON_UNESCAPED_SLASHES is safe to
            // keep alongside JSON_HEX_TAG and restores plain URLs like
            // "https://schema.org" instead of "https:\/\/schema.org".
        ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_UNESCAPED_SLASHES);
    @endphp
    <script type="application/ld+json">{!! $jsonLd !!}</script>
@endpush
    @php
        $currency = app(\App\Services\CurrencyService::class)->symbol($property->currency);
    @endphp
    
        <section>
            <section class="py-8 bg-white md:py-12 dark:bg-gray-900 antialiased">
                <div class="max-w-screen-xl px-4 mx-auto 2xl:px-0">
                    <div class="lg:grid lg:grid-cols-2 lg:gap-8 xl:gap-12 items-start">
                        <!-- Left: Property Hero Media -->
                        @php
                            $galleryMedia = $property->getMedia('images');
                        @endphp
                        <div class="shrink-0 w-full mx-auto space-y-4" x-data="{ activeImage: @js($property->image_url) }">
                            <div class="relative overflow-hidden rounded-3xl shadow-2xl bg-gray-900 border border-gray-800">
                                <img :src="activeImage"
                                    alt="{{ $property->title }}"
                                    class="w-full h-80 sm:h-96 md:h-[450px] object-cover transition-transform duration-500 hover:scale-105">
                                <div class="absolute top-4 start-4 flex gap-2">
                                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-black shadow-lg {{ $property->status === 'for_rent' ? 'bg-emerald-600 text-white' : 'bg-blue-600 text-white' }}">
                                        {{ $property->status === 'for_rent' ? (app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent') : (app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale') }}
                                    </span>
                                    <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900/80 backdrop-blur-md text-white shadow-lg">
                                        {{ $property->translated_type }}
                                    </span>
                                </div>
                                @if($property->hasVirtualExperience())
                                    <a href="#virtual-tour-section"
                                        class="absolute top-4 end-4 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-black text-white bg-purple-600/90 backdrop-blur-md shadow-lg hover:bg-purple-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" /></svg>
                                        {{ app()->getLocale() === 'ar' ? 'جولة افتراضية / 3D' : 'VR / 3D Tour' }}
                                    </a>
                                @endif
                            </div>

                            @if($galleryMedia->count() > 1)
                                <div class="grid grid-cols-4 sm:grid-cols-5 gap-2" role="list" aria-label="{{ app()->getLocale() === 'ar' ? 'معرض صور العقار' : 'Property photo gallery' }}">
                                    @foreach($galleryMedia as $media)
                                        <button type="button"
                                            @click="activeImage = @js($media->getUrl())"
                                            :class="activeImage === @js($media->getUrl()) ? 'ring-2 ring-blue-600' : 'ring-1 ring-gray-200 dark:ring-gray-700 hover:ring-blue-400'"
                                            class="relative rounded-xl overflow-hidden aspect-square transition-all focus:outline-none focus:ring-2 focus:ring-blue-600">
                                            <img src="{{ $media->getUrl() }}"
                                                alt="{{ app()->getLocale() === 'ar' ? $property->title . ' - صورة ' . ($loop->iteration) : $property->title . ' - photo ' . ($loop->iteration) }}"
                                                class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif

                            @if($property->hasMedia('3d_models'))
                                <div class="mt-6">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <svg class="w-5 h-5 inline me-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            {{ app()->getLocale() === 'ar' ? 'نموذج العقار ثلاثي الأبعاد' : '3D Property Model' }}
                                        </h3>
                                    </div>
                                    <model-viewer 
                                        src="{{ $property->getFirstMediaUrl('3d_models') }}"
                                        alt="3D model of {{ $property->title }}"
                                        camera-controls
                                        touch-action="pan-y"
                                        auto-rotate
                                        shadow-intensity="1"
                                        class="w-full h-80 bg-gray-100 dark:bg-gray-800 rounded-2xl">
                                    </model-viewer>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Property Info & Actions -->
                        <div class="mt-6 sm:mt-8 lg:mt-0">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-bold text-blue-500 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/40 px-3 py-1 rounded-lg">
                                    {{ $property->translated_type }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'كود العقار:' : 'Ref ID:' }} #{{ $property->id }}
                                </span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white leading-snug mb-3">
                                {{ $property->title }}
                            </h1>

                            <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mb-4">
                                <span class="text-blue-500"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></span>
                                <span>{{ $property->location ?: 'المكلا - حضرموت' }}</span>
                            </p>

                            <!-- Price Display -->
                            <div class="p-4 bg-gray-900/90 rounded-2xl border border-gray-800 mb-6">
                                <div class="flex items-baseline justify-between">
                                    <div>
                                        <span class="text-xs text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'السعر المطلوب' : 'Asking Price' }}</span>
                                        <span class="text-3xl font-black text-emerald-400">
                                            {{ $property->formatted_price }}
                                        </span>
                                    </div>
                                    @if($property->area_sqft)
                                        <div class="text-end">
                                            <span class="text-xs text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'السعر لكل م²' : 'Price per m²' }}</span>
                                            <span class="text-base font-mono text-gray-300">
                                                {{ app(\App\Services\CurrencyService::class)->format($property->price / $property->area_sqft, $property->currency) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Key Specs Grid --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6 p-4 bg-gray-50 dark:bg-gray-800/80 rounded-2xl border border-gray-200 dark:border-gray-700">
                                <div class="p-3 bg-white dark:bg-gray-900 rounded-xl shadow-2xs border border-gray-100 dark:border-gray-800 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'المساحة' : 'Area' }}</p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $property->area_sqft ?? 150 }} م²</p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-900 rounded-xl shadow-2xs border border-gray-100 dark:border-gray-800 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'غرف النوم' : 'Bedrooms' }}</p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $property->bedrooms ?? 1 }} {{ app()->getLocale() === 'ar' ? 'غرف' : 'Beds' }}</p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-900 rounded-xl shadow-2xs border border-gray-100 dark:border-gray-800 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'دورات المياه' : 'Bathrooms' }}</p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $property->bathrooms ?? 1 }} {{ app()->getLocale() === 'ar' ? 'حمامات' : 'Baths' }}</p>
                                </div>
                                <div class="p-3 bg-white dark:bg-gray-900 rounded-xl shadow-2xs border border-gray-100 dark:border-gray-800 text-center">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }}</p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white mt-1">{{ $property->translated_type }}</p>
                                </div>
                            </div>

                            {{-- WhatsApp & Call Quick Actions --}}
                            <div class="mb-6">
                                @php
                                    $contactPhone = $property->contact_phone ?? app(\App\Settings\GeneralSettings::class)->site_phone;
                                    $whatsappUrl = \App\Helpers\AqarContactHelper::getWhatsAppUrl($contactPhone, $property->title, $property->id);
                                    $callUrl = \App\Helpers\AqarContactHelper::formatForCall($contactPhone);
                                    $shareUrls = \App\Helpers\AqarContactHelper::getShareUrls(request()->url(), $property->title);
                                @endphp

                                <div class="flex flex-wrap gap-3 mb-4">
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                        class="flex-1 min-w-[160px] inline-flex items-center justify-center py-3.5 px-5 text-sm font-bold text-white bg-green-600 hover:bg-green-500 rounded-2xl shadow-lg hover:shadow-green-600/30 transition-all duration-150">
                                        <svg class="w-5 h-5 me-2 fill-current" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'تواصل عبر الواتساب' : 'Chat on WhatsApp' }}
                                    </a>

                                    <a href="tel:{{ $callUrl }}"
                                        class="flex-1 min-w-[140px] inline-flex items-center justify-center py-3.5 px-5 text-sm font-bold text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 rounded-2xl shadow-xs dark:bg-gray-800 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 transition-all duration-150">
                                        <svg class="w-5 h-5 me-2 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'اتصال بالمكتب' : 'Call Office' }}
                                    </a>
                                </div>

                                {{-- Social Sharing Toolbar --}}
                                <div class="p-3 bg-gray-50 dark:bg-gray-800/60 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between gap-2 flex-wrap">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 me-2 flex items-center">
                                        <svg class="w-4 h-4 me-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'مشاركة العقار:' : 'Share Property:' }}
                                    </span>
                                    <div class="flex items-center gap-1.5" x-data="{ copied: false }">
                                        <a href="{{ $shareUrls['whatsapp'] }}" target="_blank" rel="noopener noreferrer" title="WhatsApp"
                                            class="p-2 text-green-600 hover:bg-green-100 rounded-lg dark:hover:bg-green-900/40 transition-colors">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ $shareUrls['twitter'] }}" target="_blank" rel="noopener noreferrer" title="Twitter / X"
                                            class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 rounded-lg dark:hover:bg-gray-700 transition-colors">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                            </svg>
                                        </a>
                                        <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)" 
                                            class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 rounded-lg dark:hover:bg-gray-700 transition-colors flex items-center gap-1 text-xs">
                                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            <span x-text="copied ? ({{ app()->getLocale() === 'ar' ? '\'تم النسخ!\'' : '\'Copied!\'' }}) : ({{ app()->getLocale() === 'ar' ? '\'نسخ الرابط\'' : '\'Copy Link\'' }})" class="font-medium text-xs"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Buttons Bar -->
                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <button wire:click="toggleFavorite" wire:loading.attr="disabled" wire:target="toggleFavorite"
                                    class="inline-flex items-center justify-center py-3 px-5 text-xs font-bold rounded-2xl border transition-all disabled:opacity-60 disabled:cursor-not-allowed
                                    {{ $isFavorited
                                        ? 'text-white bg-red-600 border-red-600 hover:bg-red-700 shadow-md'
                                        : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700'
                                    }}">
                                    <svg class="w-4 h-4 -ms-1 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="{{ $isFavorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                    </svg>
                                    {{ $isFavorited ? (app()->getLocale() === 'ar' ? 'في المفضلة' : 'In Wishlist') : (app()->getLocale() === 'ar' ? 'إضافة للمفضلة' : 'Add to Wishlist') }}
                                </button>
                                
                                <button wire:click="openInquiryModal" wire:loading.attr="disabled" wire:target="openInquiryModal" type="button"
                                     class="inline-flex items-center justify-center py-3 px-5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-500 rounded-2xl shadow-md transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                                     <svg class="w-4 h-4 me-1.5 stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                     </svg>
                                     {{ app()->getLocale() === 'ar' ? 'إرسال استفسار' : 'Send Inquiry' }}
                                 </button>

                                @if($property->hasVirtualExperience())
                                    <a href="#virtual-tour-section"
                                        class="inline-flex items-center justify-center py-3 px-5 text-xs font-bold text-white bg-purple-600 hover:bg-purple-500 rounded-2xl shadow-md transition-colors">
                                        <svg class="w-4 h-4 me-1.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" /></svg>
                                        {{ app()->getLocale() === 'ar' ? 'جولة افتراضية / 3D' : 'VR / 3D Tour' }}
                                    </a>
                                @endif

                                <a href="#" title="" data-modal-target="scheduleViewingModal"
                                    data-modal-toggle="scheduleViewingModal"
                                    class="inline-flex items-center justify-center py-3 px-5 text-xs font-bold text-gray-800 bg-white dark:bg-gray-800 dark:text-gray-200 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-xs hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                    role="button">
                                    <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ app()->getLocale() === 'ar' ? 'حجز معاينة' : 'Book Viewing' }}
                                </a>

                                @if (app(\App\Services\ComponentSettingsService::class)->isEnabled('valuation-booking'))
                                    <button type="button" data-modal-target="bookValuationModal"
                                        data-modal-toggle="bookValuationModal"
                                        class="inline-flex items-center justify-center py-3 px-5 text-xs font-bold text-emerald-700 dark:text-emerald-300 bg-white dark:bg-gray-800 border border-emerald-300 dark:border-emerald-700 rounded-2xl shadow-xs hover:bg-emerald-50 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 14h6m-5 4h4M9 6h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'تقييم عقاري مجاني' : 'Free Valuation' }}
                                    </button>
                                @endif
                            </div>

                            <hr class="my-6 border-gray-200 dark:border-gray-800" />
                            <div>
                                @php
                                    // property_categories.name is a single, English, raw
                                    // column ('sales'/'lettings'/'hmo') — the fallback
                                    // branch below was already bilingual for a null
                                    // category, but a real category (the common case)
                                    // bypassed it entirely and printed the raw English
                                    // value even on the Arabic-locale page.
                                    $categoryLabels = app()->getLocale() === 'ar'
                                        ? ['sales' => 'مبيعات', 'lettings' => 'إيجارات', 'hmo' => 'سكن جماعي']
                                        : ['sales' => 'Sales', 'lettings' => 'Rentals', 'hmo' => 'Shared Housing (HMO)'];
                                    $categoryName = $property->category
                                        ? ($categoryLabels[$property->category->name] ?? $property->category->name)
                                        : ($property->status === 'for_rent' ? $categoryLabels['lettings'] : $categoryLabels['sales']);
                                @endphp
                                <div class="flex items-center gap-2 mb-4">
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'التصنيف:' : 'Category:' }}</span>
                                    <span class="bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 text-xs font-bold px-3 py-1 rounded-lg">
                                        {{ $categoryName }}
                                    </span>
                                </div>
                                <div class="my-4">
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-2">{{ app()->getLocale() === 'ar' ? 'المزايا والخدمات المتوفرة:' : 'Features & Amenities:' }}</h3>
                                    <ul class="grid grid-cols-2 gap-2 text-xs text-gray-700 dark:text-gray-300">
                                        @forelse($property->features ?? [] as $feature)
                                            <li class="flex items-center gap-1.5">
                                                <span class="text-emerald-500"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg></span>
                                                <span>{{ $feature->feature_name }}</span>
                                            </li>
                                        @empty
                                            <li class="text-gray-400 col-span-2">{{ app()->getLocale() === 'ar' ? 'تشمل: كهرباء، مياه، موقف سيارات، حراسة أمنية' : 'Includes: Electricity, Water, Parking, Security' }}</li>
                                        @endforelse
                                    </ul>
                                </div>
                                <hr class="my-2 md:my-2 border-gray-200 dark:border-gray-800" />
                                <div class="my-2">
                                    <span class="text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'الحي' : 'Neighborhood' }}</span>
                                    @if ($neighborhood)
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'نظرة عامة' : 'Overview' }}</h3>
                                                <p class="text-gray-600 dark:text-gray-300">{{ $neighborhood->description }}</p>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إحصائيات رئيسية' : 'Key Statistics' }}</h3>
                                                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                                                    <li>{{ app()->getLocale() === 'ar' ? 'عدد السكان' : 'Population' }}: {{ number_format($neighborhood->population) }}</li>
                                                    <li>{{ app()->getLocale() === 'ar' ? 'متوسط الدخل الشهري للأسرة' : 'Avg. Monthly Household Income' }}: {{ app(\App\Services\CurrencyService::class)->format($neighborhood->median_income) }}
                                                    </li>
                                                    <li>{{ app()->getLocale() === 'ar' ? 'مؤشر إمكانية المشي' : 'Walk Score' }}: {{ $neighborhood->walk_score }}/100</li>
                                                    <li>{{ app()->getLocale() === 'ar' ? 'مؤشر النقل العام' : 'Transit Score' }}: {{ $neighborhood->transit_score }}/100</li>
                                                </ul>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'المدارس' : 'Schools' }}</h3>
                                                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                                                    @foreach ($neighborhood->schools as $school)
                                                        <li>{{ $school['name'] }} - {{ $school['rating'] }}/10</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'المرافق' : 'Amenities' }}</h3>
                                                <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                                                    @foreach ($neighborhood->amenities as $amenity)
                                                        <li>{{ $amenity }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Last updated' }}:
                                            {{ $neighborhood->last_updated->translatedFormat('M d, Y H:i') }}</p>

                                        <!-- Neighborhood Reviews Section -->
                                        <div class="mt-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'تقييمات الحي' : 'Neighborhood Reviews' }}</h3>
                                                @if($neighborhoodReviews && $neighborhoodReviews->count() > 0)
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <svg class="w-5 h-5 {{ $i <= round($neighborhoodAverageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($neighborhoodAverageRating, 1) }} ({{ $neighborhoodReviews->count() }} {{ Str::plural('review', $neighborhoodReviews->count()) }})</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($neighborhoodReviews && $neighborhoodReviews->count() > 0)
                                                <div class="space-y-4 mb-4">
                                                    @foreach($neighborhoodReviews->take(3) as $review)
                                                        <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                                            <div class="flex items-start justify-between mb-2">
                                                                <div>
                                                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $review->title }}</h4>
                                                                    <div class="flex items-center gap-2 mt-1">
                                                                        <div class="flex">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 24 24">
                                                                                    <path d="M13.849 4.22c-.684-1.626-3.014-1.626-3.698 0L8.397 8.387l-4.552.361c-1.775.14-2.495 2.331-1.142 3.477l3.468 2.937-1.06 4.392c-.413 1.713 1.472 3.067 2.992 2.149L12 19.35l3.897 2.354c1.52.918 3.405-.436 2.992-2.15l-1.06-4.39 3.468-2.938c1.353-1.146.633-3.336-1.142-3.477l-4.552-.36-1.754-4.17Z" />
                                                                                </svg>
                                                                            @endfor
                                                                        </div>
                                                                        <span class="text-sm text-gray-600 dark:text-gray-400">by {{ $review->user->name ?? 'Anonymous' }}</span>
                                                                        <span class="text-sm text-gray-500">• {{ $review->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $review->comment }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ app()->getLocale() === 'ar' ? 'لا توجد تقييمات بعد. كن أول من يقيّم هذا الحي!' : 'No reviews yet. Be the first to review this neighborhood!' }}</p>
                                            @endif
                                            
                                            <!-- Neighborhood Review Form -->
                                            @auth
                                                @livewire('neighborhood-review-form', ['neighborhoodId' => $neighborhood->id])
                                            @else
                                                <p class="mt-4 text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'يرجى' : 'Please' }} <a href="{{ url('/login') }}" class="text-primary-600 hover:underline">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'login' }}</a> {{ app()->getLocale() === 'ar' ? 'لإضافة تقييم عن الحي.' : 'to leave a neighborhood review.' }}</p>
                                            @endauth
                                        </div>
                                    @else
                                        <ul class="list-disc list-inside grid grid-cols-2 gap-2">
                                            <li class="text-gray-500">No neighborhood details available</li>
                                        </ul>
                                    @endif
                                </div>

                                <hr class="my-2 md:my-2 border-gray-200 dark:border-gray-800" />
                                <div class="my-2">
                                    <span class="text-gray-500 font-semibold">{{ app()->getLocale() === 'ar' ? 'مؤشرات إمكانية المشي' : 'Walkability Scores' }}</span>
                                    @if ($property->walkability_score)
                                        <div class="mt-3 space-y-3">
                                            <!-- Walk Score -->
                                            <div class="flex items-center">
                                                <div class="w-16 h-16 rounded-lg flex flex-col items-center justify-center text-white font-bold me-4"
                                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                    <span class="text-2xl">{{ $property->walkability_score }}</span>
                                                    <span class="text-xs">{{ app()->getLocale() === 'ar' ? 'مشي' : 'Walk' }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 dark:text-white">
                                                        @if(app()->getLocale() === 'ar')
                                                            @php $s = $property->walkability_score; @endphp
                                                            {{ $s >= 90 ? 'جنة المشاة' : ($s >= 70 ? 'ملائم جداً للمشي' : ($s >= 50 ? 'ملائم نوعاً ما للمشي' : ($s >= 25 ? 'يعتمد على السيارة' : 'يعتمد كلياً على السيارة'))) }}
                                                        @else
                                                            {{ $property->walkability_description }}
                                                        @endif
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'المشاوير اليومية والمرافق القريبة' : 'Daily errands and amenities' }}</p>
                                                </div>
                                            </div>

                                            <!-- Transit Score -->
                                            @if ($property->transit_score)
                                            <div class="flex items-center">
                                                <div class="w-16 h-16 rounded-lg flex flex-col items-center justify-center text-white font-bold me-4"
                                                    style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                                    <span class="text-2xl">{{ $property->transit_score }}</span>
                                                    <span class="text-xs">{{ app()->getLocale() === 'ar' ? 'نقل' : 'Transit' }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 dark:text-white">
                                                        @if(app()->getLocale() === 'ar')
                                                            @php $s = $property->transit_score; @endphp
                                                            {{ $s >= 90 ? 'جنة الركاب' : ($s >= 70 ? 'نقل ممتاز' : ($s >= 50 ? 'نقل جيد' : ($s >= 25 ? 'نقل محدود' : 'نقل ضئيل'))) }}
                                                        @else
                                                            {{ $property->transit_description }}
                                                        @endif
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'خيارات النقل العام' : 'Public transportation options' }}</p>
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Bike Score -->
                                            @if ($property->bike_score)
                                            <div class="flex items-center">
                                                <div class="w-16 h-16 rounded-lg flex flex-col items-center justify-center text-white font-bold me-4"
                                                    style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                                    <span class="text-2xl">{{ $property->bike_score }}</span>
                                                    <span class="text-xs">{{ app()->getLocale() === 'ar' ? 'دراجة' : 'Bike' }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-semibold text-gray-900 dark:text-white">
                                                        @if(app()->getLocale() === 'ar')
                                                            @php $s = $property->bike_score; @endphp
                                                            {{ $s >= 90 ? 'جنة الدراجات' : ($s >= 70 ? 'ملائم جداً للدراجات' : ($s >= 50 ? 'ملائم للدراجات' : 'ملائم نوعاً ما للدراجات')) }}
                                                        @else
                                                            {{ $property->bike_description }}
                                                        @endif
                                                    </p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'ممرات الدراجات والبنية التحتية' : 'Bike lanes and biking infrastructure' }}</p>
                                                </div>
                                            </div>
                                            @endif

                                            <p class="text-xs text-gray-500 mt-2">
                                                {{ app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Last updated' }}: {{ $property->walkability_updated_at ? $property->walkability_updated_at->translatedFormat('M d, Y') : (app()->getLocale() === 'ar' ? 'غير متوفر' : 'N/A') }}
                                            </p>
                                        </div>
                                    @else
                                        <p class="text-gray-500 mt-2">{{ app()->getLocale() === 'ar' ? 'مؤشرات إمكانية المشي غير متوفرة لهذا العقار' : 'Walkability scores not available for this property' }}</p>
                                    @endif
                                </div>

                                <hr class="my-2 md:my-2 border-gray-200 dark:border-gray-800" />
                                <div class="my-2">
                                    <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'كفاءة الطاقة' : 'Energy Efficiency' }}</span>
                                    @if ($property->energy_rating && $property->energy_score)
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl me-4"
                                                style="background-color: {{ $this->getEnergyRatingColor($property->energy_rating) }}">
                                                {{ $property->energy_rating }}
                                            </div>
                                            <div>
                                                <p class="font-semibold">{{ app()->getLocale() === 'ar' ? 'مؤشر كفاءة الطاقة' : 'Energy Efficiency Score' }}:
                                                    {{ $property->energy_score }}/100</p>
                                                <p class="text-sm text-gray-600">{{ app()->getLocale() === 'ar' ? 'آخر تحديث' : 'Last updated' }}:
                                                    {{ $property->energy_rating_date->translatedFormat('d M Y') }}</p>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600">
                                            @if(app()->getLocale() === 'ar')
                                                يتم تصنيف العقارات من A (الأعلى كفاءة) إلى G (الأقل كفاءة). لمزيد من المعلومات حول <a href="#" class="text-blue-600 hover:underline" @click="$dispatch('open-modal', 'energy-efficiency-info')">تصنيفات كفاءة الطاقة</a>.
                                            @else
                                                Properties are rated from A (most efficient) to G (least efficient). Learn more about <a href="#" class="text-blue-600 hover:underline" @click="$dispatch('open-modal', 'energy-efficiency-info')">energy efficiency ratings</a>.
                                            @endif
                                        </p>
                                    @else
                                        <ul class="list-disc list-inside grid grid-cols-2 gap-2">
                                            <li class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'لا تتوفر تفاصيل كفاءة الطاقة' : 'No Energy efficiency details available' }}</li>
                                        </ul>
                                    @endif
                                </div>
                                <hr class="my-2 md:my-2 border-gray-200 dark:border-gray-800" />
                                <div class="">
                                    <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'الفرع/الفريق' : 'Branch/Team' }}</span>
                                    <span
                                        class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $team->name ?? (app()->getLocale() === 'ar' ? 'لا تتوفر معلومات عن الفريق' : 'No team information available') }}</span>
                                </div>
                                
                                <!-- Investment Analytics Section -->
                                @if($investmentAnalytics)
                                <hr class="my-2 md:my-2 border-gray-200 dark:border-gray-800" />
                                <div class="my-2">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-500 font-semibold">{{ app()->getLocale() === 'ar' ? 'تحليلات الاستثمار' : 'Investment Analytics' }}</span>
                                        <span class="text-xs text-gray-400">{{ app()->getLocale() === 'ar' ? 'رؤى مدعومة بالذكاء الاصطناعي' : 'AI-Powered Insights' }}</span>
                                    </div>
                                    
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-4 space-y-4">
                                        <!-- Investment Prediction -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ app()->getLocale() === 'ar' ? 'العائد المتوقع' : 'Predicted ROI' }}</h4>
                                                <p class="text-3xl font-bold {{ $investmentAnalytics['prediction']['predicted_roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ number_format($investmentAnalytics['prediction']['predicted_roi'], 2) }}%
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'العائد المتوقع خلال 5 سنوات' : 'Expected return over 5 years' }}</p>
                                            </div>

                                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ app()->getLocale() === 'ar' ? 'مؤشر المخاطرة' : 'Risk Score' }}</h4>
                                                <div class="flex items-center">
                                                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                                        {{ number_format($investmentAnalytics['prediction']['risk_score'], 1) }}/10
                                                    </p>
                                                    <div class="ms-3 flex-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($investmentAnalytics['prediction']['risk_score'] / 10) * 100 }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1">{{ app()->getLocale() === 'ar' ? 'كلما قلّ كان أفضل' : 'Lower is better' }}</p>
                                            </div>
                                        </div>

                                        <!-- Cash Flow Analysis -->
                                        @if(isset($investmentAnalytics['cash_flow_analysis']))
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">{{ app()->getLocale() === 'ar' ? 'توقعات التدفق النقدي' : 'Cash Flow Projection' }}</h4>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <div>
                                                    <p class="text-xs text-gray-500">{{ app()->getLocale() === 'ar' ? 'الإيجار السنوي المقدر' : 'Est. Annual Rent' }}</p>
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ $currency }}{{ number_format($investmentAnalytics['cash_flow_analysis']['estimated_annual_rent'], 0) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">{{ app()->getLocale() === 'ar' ? 'المصاريف المقدرة' : 'Est. Expenses' }}</p>
                                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ $currency }}{{ number_format($investmentAnalytics['cash_flow_analysis']['estimated_expenses'], 0) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">{{ app()->getLocale() === 'ar' ? 'صافي التدفق النقدي' : 'Net Cash Flow' }}</p>
                                                    <p class="text-lg font-semibold text-green-600">
                                                        {{ $currency }}{{ number_format($investmentAnalytics['cash_flow_analysis']['net_cash_flow'], 0) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">{{ app()->getLocale() === 'ar' ? 'عائد نقدي على نقدي' : 'Cash-on-Cash' }}</p>
                                                    <p class="text-lg font-semibold text-blue-600">
                                                        {{ number_format($investmentAnalytics['cash_flow_analysis']['cash_on_cash_return'], 2) }}%
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Market Position -->
                                        @if(isset($investmentAnalytics['market_position']))
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ app()->getLocale() === 'ar' ? 'الموقع في السوق' : 'Market Position' }}</h4>
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getPositionBadgeClass($investmentAnalytics['market_position']['position']) }}">
                                                    {{ ucfirst(str_replace('_', ' ', $investmentAnalytics['market_position']['position'])) }}
                                                </span>
                                                <span class="text-sm {{ $investmentAnalytics['market_position']['price_vs_market'] < 0 ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                                    {{ $investmentAnalytics['market_position']['price_vs_market'] >= 0 ? '+' : '' }}{{ number_format($investmentAnalytics['market_position']['price_vs_market'], 1) }}% {{ app()->getLocale() === 'ar' ? 'مقارنة بالسوق' : 'vs market' }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $investmentAnalytics['market_position']['competitive_advantage'] }}
                                            </p>
                                        </div>
                                        @endif
                                        
                                        <!-- Investment Simulator Link -->
                                        <div class="text-center pt-2">
                                            <button wire:click="toggleInvestmentSimulation" 
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                @if(app()->getLocale() === 'ar')
                                                    {{ $showInvestmentSimulation ? 'إخفاء' : 'عرض' }} محاكي الاستثمار المتقدم
                                                @else
                                                    {{ $showInvestmentSimulation ? 'Hide' : 'Show' }} Advanced Investment Simulator
                                                @endif
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <hr class="my-6 md:my-8 border-gray-200 dark:border-gray-800" />
                            
                            <!-- Property Tax Estimator Section -->
                            <div class="mt-6">
                                @livewire('property-tax-estimator', ['property' => $property])
                            </div>
                        </div>
                    </div>

                    <div class="w-full">
                        <p class="mb-6 text-gray-500 dark:text-gray-400">
                            {{ $property->description }}
                        </p>
                    </div>

                    {{-- Scroll target shared by every VR/3D entry point below (hero badge,
                         Interactive Buttons Bar button) regardless of which of the three
                         independent mechanisms (virtual_tour_url, model_3d_url, '3d_models'
                         media) actually renders further down the page. --}}
                    @if($property->hasVirtualExperience())
                        <div id="virtual-tour-section"></div>
                    @endif

                    {{-- Virtual Tour Section --}}
                    @if($property->virtual_tour_url)
                        <div class="w-full mt-8 mb-8">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Virtual Tour') }}</h2>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" wire:click="toggleVirtualTour"
                                        class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                                        {{ $showVirtualTour ? __('Hide Virtual Tour') : __('View Virtual Tour') }}
                                    </button>
                                    @if($property->live_tour_available)
                                        <button type="button" wire:click="openScheduleLiveTourModal"
                                            class="inline-flex items-center px-4 py-2 rounded-lg border border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400 hover:bg-blue-50 dark:hover:bg-gray-800 text-sm font-medium transition-colors">
                                            {{ __('Schedule Live Tour') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            @if($showVirtualTour)
                                <div class="aspect-video w-full rounded-lg overflow-hidden bg-black">
                                    {!! $property->getVirtualTourEmbed() !!}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- 3D Model Viewer Section --}}
                    @if($property->model_3d_url)
                        <div class="w-full mt-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'نموذج العقار ثلاثي الأبعاد' : '3D Property Model' }}</h2>
                            <x-model-3d-viewer :modelUrl="$property->model_3d_url" :propertyTitle="$property->title" />
                        </div>
                    @endif

                    {{-- Property History Section --}}
                    @if($propertyHistory->count() > 0 || $priceHistory->count() > 0 || $salesHistory->count() > 0)
                        <div class="w-full mt-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'سجل العقار' : 'Property History' }}</h2>
                            
                            {{-- Price History --}}
                            @if($priceHistory->count() > 0)
                                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 me-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                        </svg>
                                        Price Changes
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($priceHistory as $history)
                                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $history->event_date->translatedFormat('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">
                                                        {{ app(\App\Services\CurrencyService::class)->format($history->old_price, $property->currency) }}
                                                        <svg class="w-4 h-4 inline mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                                        </svg>
                                                        {{ app(\App\Services\CurrencyService::class)->format($history->new_price, $property->currency) }}
                                                    </p>
                                                </div>
                                                <div class="text-end">
                                                    @php
                                                        $change = $history->getPriceChangePercentage();
                                                        $isIncrease = $change >= 0;
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $isIncrease ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' }}">
                                                        {{ $isIncrease ? '+' : '' }}{{ number_format($change, 2) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Sales History --}}
                            @if($salesHistory->count() > 0)
                                <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 me-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'المبيعات السابقة' : 'Past Sales' }}
                                    </h3>
                                    <div class="space-y-3">
                                        @foreach($salesHistory as $sale)
                                            <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-3">
                                                <div>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $sale->event_date->translatedFormat('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white">{{ $sale->description }}</p>
                                                </div>
                                                <div class="text-end">
                                                    <p class="text-lg font-bold text-green-600">
                                                        {{ app(\App\Services\CurrencyService::class)->format($sale->new_price, $property->currency) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- General History Timeline --}}
                            @if($propertyHistory->count() > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                        <svg class="w-5 h-5 me-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ app()->getLocale() === 'ar' ? 'الجدول الزمني للنشاط' : 'Activity Timeline' }}
                                    </h3>
                                    <div class="space-y-4">
                                        @foreach($propertyHistory as $event)
                                            <div class="flex gap-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                                                        {{ $event->event_type === 'sale' ? 'bg-green-100 dark:bg-green-800' : 
                                                           ($event->event_type === 'price_change' ? 'bg-blue-100 dark:bg-blue-800' : 
                                                           ($event->event_type === 'status_change' ? 'bg-yellow-100 dark:bg-yellow-800' : 'bg-gray-100 dark:bg-gray-700')) }}">
                                                        @if($event->event_type === 'sale')
                                                            <svg class="w-5 h-5 text-green-600 dark:text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @elseif($event->event_type === 'price_change')
                                                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                                            </svg>
                                                        @elseif($event->event_type === 'status_change')
                                                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->event_date->translatedFormat('M d, Y') }}</p>
                                                    <p class="text-gray-900 dark:text-white font-medium">
                                                        @if(app()->getLocale() === 'ar')
                                                            @switch($event->event_type)
                                                                @case('listing')
                                                                    تم إدراج العقار بسعر {{ app(\App\Services\CurrencyService::class)->format($event->new_price, $property->currency) }}
                                                                    @break
                                                                @case('sale')
                                                                    تم بيع العقار بسعر {{ app(\App\Services\CurrencyService::class)->format($event->new_price, $property->currency) }}
                                                                    @break
                                                                @case('price_change')
                                                                    {{ $event->new_price >= $event->old_price ? 'ارتفع السعر' : 'انخفض السعر' }}
                                                                    من {{ app(\App\Services\CurrencyService::class)->format($event->old_price, $property->currency) }}
                                                                    إلى {{ app(\App\Services\CurrencyService::class)->format($event->new_price, $property->currency) }}
                                                                    ({{ number_format(abs((($event->new_price - $event->old_price) / max($event->old_price, 1)) * 100), 2) }}%)
                                                                    @break
                                                                @case('status_change')
                                                                    تغيّرت الحالة من {{ $event->old_status }} إلى {{ $event->new_status }}
                                                                    @break
                                                                @default
                                                                    {{ $event->description }}
                                                            @endswitch
                                                        @else
                                                            {{ $event->description }}
                                                        @endif
                                                    </p>
                                                    @if($event->event_type === 'update' && $event->changes)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ app()->getLocale() === 'ar' ? 'الحقول المحدّثة' : 'Updated fields' }}: {{ implode(', ', array_keys($event->changes)) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Community Events Calendar Section --}}
                    @if($communityEvents->count() > 0)
                        <div class="w-full mt-8 mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                <svg class="w-6 h-6 me-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Community Events Calendar
                            </h2>
                            
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <!-- Calendar Navigation -->
                                <div class="flex items-center justify-between mb-6">
                                    <button wire:click="changeMonth('prev')"
                                        aria-label="{{ app()->getLocale() === 'ar' ? 'الشهر السابق' : 'Previous month' }}"
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y') }}
                                    </h3>
                                    <button wire:click="changeMonth('next')"
                                        aria-label="{{ app()->getLocale() === 'ar' ? 'الشهر التالي' : 'Next month' }}"
                                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Events List -->
                                <div class="space-y-4">
                                    @foreach($communityEvents as $event)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex items-start gap-4">
                                                <!-- Event Date Badge -->
                                                <div class="flex-shrink-0 w-16 h-16 bg-blue-600 rounded-lg flex flex-col items-center justify-center text-white">
                                                    <span class="text-xs font-semibold uppercase">{{ $event->event_date->translatedFormat('M') }}</span>
                                                    <span class="text-2xl font-bold">{{ $event->event_date->format('d') }}</span>
                                                </div>
                                                
                                                <!-- Event Details -->
                                                <div class="flex-1">
                                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                        {{ $event->title }}
                                                    </h4>
                                                    
                                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $event->event_date->translatedFormat('g:i A') }}
                                                        @if($event->end_date)
                                                            - {{ $event->end_date->translatedFormat('g:i A') }}
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $event->location }}
                                                        @if(isset($event->distance_from_property))
                                                            <span class="ms-2 text-xs text-blue-600 dark:text-blue-400">
                                                                ({{ number_format($event->distance_from_property, 1) }} km away)
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($event->category)
                                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mb-2">
                                                            {{ ucfirst($event->category) }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($event->description)
                                                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                                                            {{ Str::limit($event->description, 150) }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($event->organizer)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                            Organized by: {{ $event->organizer }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($event->website_url)
                                                        <a href="{{ $event->website_url }}" target="_blank" 
                                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mt-2">
                                                            Learn more
                                                            <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                        Showing {{ $communityEvents->count() }} upcoming events within 10 km of this property
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Interactive Floor Plan -->
                    <x-floor-plan-viewer :floor-plan-data="$property->floor_plan_data" />
                    

                    {{-- Property Video Section --}}
                    @php
                        $video = $property->getFirstMedia('videos');
                    @endphp
                    @if($video)
                        <div class="w-full mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'فيديو العقار' : 'Property Video' }}</h2>
                            {{-- 16:9 aspect ratio container (9/16 * 100 = 56.25%) --}}
                            <div class="relative w-full" style="padding-bottom: 56.25%;">
                                <video 
                                    class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                                    controls
                                    preload="metadata"
                                    aria-label="Property video tour for {{ $property->title }}">
                                    <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>
                    @endif

                    <!-- Advanced Investment Simulator -->
                    @if($showInvestmentSimulation)
                    <div class="w-full mb-8">
                        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6">
                            @livewire('investment-analysis-component', ['property' => $property])
                        </div>
                    </div>
                    @endif
                    

                    <div class=""></div>
                    @auth
                        @livewire('property-review-form', ['propertyId' => $property->id])
                    @else
                        <p class="mt-4 text-gray-600">{{ app()->getLocale() === 'ar' ? 'يرجى' : 'Please' }} <a href="{{ url('/login') }}"
                                class="text-blue-500 hover:underline">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'login' }}</a> {{ app()->getLocale() === 'ar' ? 'لإضافة تقييم.' : 'to leave a review.' }}</p>
                    @endauth
                </div>
            </section>

            <div id="bookValuationModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'احجز تقييماً عقارياً' : 'Book a Property Valuation' }}
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="bookValuationModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">{{ app()->getLocale() === 'ar' ? 'إغلاق النافذة' : 'Close modal' }}</span>
                            </button>
                        </div>
                        <div class="p-4 md:p-5 space-y-4">
                            @if (app(\App\Services\ComponentSettingsService::class)->isEnabled('valuation-booking'))
                                @livewire('valuation-booking')
                            @endif

                            @if ($isLettingsProperty)
                                <div class="mt-8">
                                    <a href="{{ route('rental.apply', ['property_id' => $property->id]) }}"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                        {{ app()->getLocale() === 'ar' ? 'التقديم على الإيجار' : 'Apply for Tenancy' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button data-modal-hide="bookValuationModal" type="button"
                                class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="scheduleViewingModal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div
                            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'جدولة معاينة' : 'Schedule viewing' }}
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                data-modal-hide="scheduleViewingModal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">{{ app()->getLocale() === 'ar' ? 'إغلاق النافذة' : 'Close modal' }}</span>
                            </button>
                        </div>
                        <div class="p-4 md:p-5 space-y-4">
                            @if (app(\App\Services\ComponentSettingsService::class)->isEnabled('property-booking'))
                                @livewire('property-booking', ['propertyId' => $property->id])
                            @endif

                            @if ($isLettingsProperty)
                                <div class="mt-8">
                                    <a href="{{ route('rental.apply', ['property_id' => $property->id]) }}"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                        {{ app()->getLocale() === 'ar' ? 'التقديم على الإيجار' : 'Apply for Tenancy' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Live Virtual Tour Modal -->
            @if($showScheduleLiveTourModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900 bg-opacity-50" wire:click.self="closeScheduleLiveTourModal">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <form wire:submit.prevent="scheduleLiveTour" class="space-y-4">
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'حجز جولة افتراضية مباشرة' : 'Schedule Live Virtual Tour' }}
                                </h3>
                                <button type="button" wire:click="closeScheduleLiveTourModal"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">{{ app()->getLocale() === 'ar' ? 'إغلاق النافذة' : 'Close modal' }}</span>
                                </button>
                            </div>
                            <div class="p-4 md:p-5 space-y-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ app()->getLocale() === 'ar'
                                        ? 'احجز جولة افتراضية مباشرة مع أحد وكلائنا العقاريين الذين سيرشدونك عبر العقار بشكل مباشر عن طريق مكالمة فيديو.'
                                        : 'Schedule a live virtual tour with one of our agents who will guide you through the property in real-time via video call.' }}
                                </p>

                                <div>
                                    <label for="tourDate" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'التاريخ المفضل' : 'Preferred Date' }}</label>
                                    <input type="date" id="tourDate" wire:model="tourDate"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        required>
                                    @error('tourDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="tourTime" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'الوقت المفضل' : 'Preferred Time' }}</label>
                                    <input type="time" id="tourTime" wire:model="tourTime"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        required>
                                    @error('tourTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="tourNotes" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'ملاحظات إضافية (اختياري)' : 'Additional Notes (Optional)' }}</label>
                                    <textarea id="tourNotes" wire:model="tourNotes" rows="3"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="{{ app()->getLocale() === 'ar' ? 'أي مناطق معينة ترغب بالتركيز عليها أو أسئلة لديك...' : "Any specific areas you'd like to focus on or questions you have..." }}"></textarea>
                                    @error('tourNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                                <button type="submit"
                                    class="text-white bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    {{ app()->getLocale() === 'ar' ? 'حجز الجولة' : 'Schedule Tour' }}
                                </button>
                                <button type="button" wire:click="closeScheduleLiveTourModal"
                                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            <!-- Property Web Inquiry Modal -->
            @if($showInquiryModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-gray-900/60 backdrop-blur-xs p-4" wire:click.self="closeInquiryModal">
                <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ __('lead.form.inquiry_title') ?? 'إرسال استفسار حول العقار' }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $property->title }} (#{{ $property->id }})
                            </p>
                        </div>
                        <button type="button" wire:click="closeInquiryModal"
                            aria-label="{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                            <svg class="w-4 h-4" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="submitLeadForm" class="p-6 space-y-4">
                        <div>
                            <label for="lead_name" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('lead.attributes.name') ?? 'الاسم الكامل' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="lead_name" wire:model="name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل اسمك الكامل' : 'Enter your full name' }}" required>
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="lead_email" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('lead.attributes.email') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="lead_email" wire:model="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="name@example.com" required>
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="lead_phone" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('lead.attributes.phone') ?? 'رقم الهاتف' }}
                            </label>
                            <input type="text" id="lead_phone" wire:model="phone"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="+967 770 000 000">
                            @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="lead_message" class="block mb-1.5 text-xs font-semibold text-gray-700 dark:text-gray-300">
                                {{ __('lead.attributes.notes') ?? 'تفاصيل الاستفسار' }}
                            </label>
                            <textarea id="lead_message" wire:model="message" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="{{ app()->getLocale() === 'ar' ? 'أكتب أي أسئلة أو ملاحظات تود الاستفسار عنها...' : 'Write any questions or notes you would like to ask about...' }}"></textarea>
                            @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" wire:click="closeInquiryModal"
                                class="py-2.5 px-4 text-xs font-medium text-gray-700 bg-white rounded-xl border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                                {{ __('lead.form.cancel') }}
                            </button>
                            <button type="submit"
                                class="py-2.5 px-5 text-xs font-bold text-white bg-primary-700 hover:bg-primary-800 rounded-xl shadow-md transition-all duration-150">
                                {{ __('lead.form.send_inquiry') ?? 'إرسال الاستفسار' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </section>
    
</div>
