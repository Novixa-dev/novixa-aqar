<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-5">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'تقديم طلب بحث مخصص عن عقار' : 'Submit a Custom Property Search Request' }}</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ app()->getLocale() === 'ar' ? 'لم تجد العقار المناسب لمتطلباتك؟ حدد مواصفات العقار المطلوب والميزانية، وسيتم مطابقة طلبك وإشعارك فور توفره.' : "Couldn't find a property that fits your needs? Specify what you're looking for and your budget, and we'll notify you as soon as a match becomes available." }}
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
        {{-- Contact Details --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'بيانات التواصل' : 'Contact Details' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الاسم الكامل' : 'Full Name' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model="name" placeholder="{{ app()->getLocale() === 'ar' ? 'محمد صالح العمودي' : 'John Smith' }}"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'رقم الهاتف / الواتساب' : 'Phone / WhatsApp Number' }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="phone" wire:model="phone" placeholder="+967 770 000 000"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني (اختياري)' : 'Email (optional)' }}</label>
                    <input type="email" id="email" wire:model="email" placeholder="name@domain.com"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Desired Specs & Location --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'مواصفات العقار المطلوب' : 'Desired Property Specifications' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="property_type" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }} <span class="text-red-500">*</span>
                    </label>
                    <select id="property_type" wire:model="property_type"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
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
                    <label for="neighborhood_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الحي / المنطقة المفضلة (المكلا)' : 'Preferred Neighborhood / Area (Mukalla)' }}</label>
                    <select id="neighborhood_id" wire:model="neighborhood_id"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جميع أحياء المكلا' : 'All Mukalla neighborhoods' }}</option>
                        @foreach($neighborhoods as $nh)
                            <option value="{{ $nh->id }}">{{ $nh->name }}</option>
                        @endforeach
                    </select>
                    @error('neighborhood_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Budget Range --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'الميزانية المتوقعة (ر.ي)' : 'Expected Budget (YER)' }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_price" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الحد الأدنى للسعر (ر.ي)' : 'Minimum Price (YER)' }}</label>
                    <input type="number" id="min_price" wire:model="min_price" placeholder="10,000,000"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('min_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="max_price" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'الحد الأقصى للسعر (ر.ي)' : 'Maximum Price (YER)' }}</label>
                    <input type="number" id="max_price" wire:model="max_price" placeholder="50,000,000"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('max_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Detailed Specs & Notes --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">{{ app()->getLocale() === 'ar' ? 'تفاصيل وملاحظات إضافية' : 'Additional Details & Notes' }}</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="bedrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'عدد غرف النوم المطلوبة' : 'Desired Number of Bedrooms' }}</label>
                    <input type="number" id="bedrooms" wire:model="bedrooms" min="0" placeholder="3"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label for="bathrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'عدد دورات المياه المطلوبة' : 'Desired Number of Bathrooms' }}</label>
                    <input type="number" id="bathrooms" wire:model="bathrooms" min="0" placeholder="2"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ app()->getLocale() === 'ar' ? 'أي اشتراطات خاصة (القرب من الخدمات، الدور، الإطلالة)' : 'Any special requirements (proximity to amenities, floor, view)' }}</label>
                <textarea id="notes" wire:model="notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل أي مواصفات أو متطلبات خاصة ترغب بها...' : 'Enter any special specifications or requirements you have...' }}"
                    class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="py-3 px-6 text-sm font-bold text-white bg-primary-700 hover:bg-primary-800 rounded-xl shadow-md transition-all duration-150 disabled:opacity-75 disabled:cursor-not-allowed inline-flex items-center gap-2">
                <svg wire:loading wire:target="submit" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ app()->getLocale() === 'ar' ? 'إرسال طلب البحث' : 'Send Search Request' }}
            </button>
        </div>
    </form>
</div>
