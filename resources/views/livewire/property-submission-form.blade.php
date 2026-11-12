<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-5">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إضافة عقار جديد للبيع أو الإيجار' : 'List a New Property for Sale or Rent' }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ app()->getLocale() === 'ar' ? 'قم بتعبئة بيانات عقارك، وسيتم مراجعة الطلب من قبل مستشارينا في المكتب قبل نشره رسمياً على منصة عقاري.' : 'Fill in your property details; our office consultants will review the submission before it is officially published on the platform.' }}
        </p>
    </div>

    @if (session()->has('message'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-2xl bg-green-50 border border-green-200 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 flex items-center gap-3" role="alert">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-semibold">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6 bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        {{-- Basic Property Information --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'المعلومات الأساسية' : 'Basic Information' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'عنوان العقار' : 'Property Title' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" wire:model="title"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: شقة فاخرة للإيجار حي السلام بالمكلا' : 'e.g. Luxury apartment for rent, Al Salam, Mukalla' }}"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'نوع العرض' : 'Listing Type' }} <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 p-3 text-sm rounded-xl border cursor-pointer transition-colors {{ $listing_type === 'for_sale' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-bold' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                            <input type="radio" wire:model.live="listing_type" value="for_sale" class="sr-only">
                            {{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}
                        </label>
                        <label class="flex items-center justify-center gap-2 p-3 text-sm rounded-xl border cursor-pointer transition-colors {{ $listing_type === 'for_rent' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-bold' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}">
                            <input type="radio" wire:model.live="listing_type" value="for_rent" class="sr-only">
                            {{ app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent' }}
                        </label>
                    </div>
                    @error('listing_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="property_type" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }} <span class="text-red-500">*</span>
                    </label>
                    <select id="property_type" wire:model="property_type"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر نوع العقار' : 'Select property type' }}</option>
                        <option value="apartment">{{ app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Apartment' }}</option>
                        <option value="villa">{{ app()->getLocale() === 'ar' ? 'فيلا' : 'Villa' }}</option>
                        <option value="land">{{ app()->getLocale() === 'ar' ? 'أرض سكنية / تجارية' : 'Residential / Commercial Land' }}</option>
                        <option value="commercial">{{ app()->getLocale() === 'ar' ? 'محل تجاري / مكتب' : 'Shop / Office' }}</option>
                        <option value="building">{{ app()->getLocale() === 'ar' ? 'عمارة كاملة' : 'Full Building' }}</option>
                    </select>
                    @error('property_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="price" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ $listing_type === 'for_rent' ? (app()->getLocale() === 'ar' ? 'الإيجار الشهري (ر.ي)' : 'Monthly Rent (YER)') : (app()->getLocale() === 'ar' ? 'السعر المطلوب (ر.ي)' : 'Asking Price (YER)') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="price" wire:model="price" placeholder="0.00"
                            class="w-full p-3 pe-12 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <span class="absolute inset-y-0 end-0 flex items-center pe-3 text-xs font-bold text-gray-500 dark:text-gray-400">
                            {{ app()->getLocale() === 'ar' ? 'ر.ي' : 'YER' }}
                        </span>
                    </div>
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Location & Neighborhood --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'الموقع والحي' : 'Location & Neighborhood' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="neighborhood_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الحي / المنطقة (المكلا)' : 'Neighborhood / Area (Mukalla)' }}
                    </label>
                    <select id="neighborhood_id" wire:model="neighborhood_id"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الحي' : 'Select neighborhood' }}</option>
                        @foreach($neighborhoods as $nh)
                            <option value="{{ $nh->id }}">{{ $nh->name }}</option>
                        @endforeach
                    </select>
                    @error('neighborhood_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="location" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'العنوان التفصيلي' : 'Detailed Address' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="location" wire:model="location" placeholder="{{ app()->getLocale() === 'ar' ? 'المكلا - الشرج / فوه / السلام' : 'e.g. Mukalla - Al Sharj / Fuwwah / Al Salam' }}"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Specifications & Specs --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'المواصفات الفنية' : 'Technical Specifications' }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="bedrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'غرف النوم' : 'Bedrooms' }}</label>
                    <input type="number" id="bedrooms" wire:model="bedrooms" min="0" placeholder="3"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('bedrooms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="bathrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'دورات المياه' : 'Bathrooms' }}</label>
                    <input type="number" id="bathrooms" wire:model="bathrooms" min="0" placeholder="2"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('bathrooms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="area_sqft" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'المساحة (م²)' : 'Area (sqm)' }}</label>
                    <input type="number" id="area_sqft" wire:model="area_sqft" min="0" placeholder="150"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('area_sqft') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="year_built" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'سنة البناء' : 'Year Built' }}</label>
                    <input type="number" id="year_built" wire:model="year_built" min="1800" max="2099" placeholder="{{ date('Y') }}"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('year_built') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="owner_phone" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'رقم للتواصل (المالك)' : 'Contact Number (Owner)' }}</label>
                    <input type="text" id="owner_phone" wire:model="owner_phone" placeholder="+967 770 000 000"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('owner_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Description & Details --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'الوصف والتفاصيل' : 'Description & Details' }}</h2>
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ app()->getLocale() === 'ar' ? 'وصف العقار والخدمات المتوفرة' : 'Property Description & Available Amenities' }} <span class="text-red-500">*</span>
                </label>
                <textarea id="description" wire:model="description" rows="4" placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب وصفاً كاملاً للعقار ومميزاته والقرب من الخدمات...' : 'Write a full description of the property, its features, and nearby amenities...' }}"
                    class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Media Attachments --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'الصور والفيديو' : 'Photos & Video' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="images" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'صور العقار (يمكن تحديد عدة صور)' : 'Property Photos (multiple allowed)' }}</label>
                    <input type="file" id="images" wire:model="images" multiple accept="image/*"
                        class="w-full text-xs text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    @error('images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="video" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'فيديو للعقار (اختياري)' : 'Property Video (optional)' }}</label>
                    <input type="file" id="video" wire:model="video" accept="video/mp4,video/quicktime"
                        class="w-full text-xs text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    @error('video') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Submission Actions --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="py-3 px-6 text-sm font-bold text-white bg-primary-700 hover:bg-primary-800 rounded-xl shadow-md transition-all duration-150 disabled:opacity-75 disabled:cursor-not-allowed inline-flex items-center gap-2">
                <svg wire:loading wire:target="submit" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'تقديم طلب الإدراج' : 'Submit Listing Request' }}
            </button>
        </div>
    </form>
</div>
