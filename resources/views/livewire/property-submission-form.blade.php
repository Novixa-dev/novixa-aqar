<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8" dir="rtl">
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-5">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white">إضافة عقار جديد للبيع أو الإيجار</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            قم بتعبئة بيانات عقارك، وسيتم مراجعة الطلب من قبل مستشارينا في المكتب قبل نشره رسمياً على منصة عقاري.
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
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">المعلومات الأساسية</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="title" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        عنوان العقار <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" wire:model="title" 
                        placeholder="مثال: شقة فاخرة للإيجار حي السلام بالمكلا"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="property_type" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        نوع العقار <span class="text-red-500">*</span>
                    </label>
                    <select id="property_type" wire:model="property_type" 
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">اختر نوع العقار</option>
                        <option value="apartment">شقة سكينة</option>
                        <option value="villa">فيلا</option>
                        <option value="land">أرض سكنية / تجارية</option>
                        <option value="commercial">محل تجاري / مكتب</option>
                        <option value="building">عمارة كاملة</option>
                    </select>
                    @error('property_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="price" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        السعر المطلوب (ر.ي) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="price" wire:model="price" placeholder="0.00"
                            class="w-full p-3 pe-12 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <span class="absolute inset-y-0 end-0 flex items-center pe-3 text-xs font-bold text-gray-500 dark:text-gray-400">
                            ر.ي
                        </span>
                    </div>
                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Location & Neighborhood --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">الموقع والحي</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="neighborhood_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        الحي / المنطقة (المكلا)
                    </label>
                    <select id="neighborhood_id" wire:model="neighborhood_id" 
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                        <option value="">اختر الحي</option>
                        @foreach($neighborhoods as $nh)
                            <option value="{{ $nh->id }}">{{ $nh->name }}</option>
                        @endforeach
                    </select>
                    @error('neighborhood_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="location" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        العنوان التفصيلي <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="location" wire:model="location" placeholder="المكلا - الشرج / فوه / السلام"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                    @error('location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Specifications & Specs --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">المواصفات الفنية</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="bedrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">غرف النوم</label>
                    <input type="number" id="bedrooms" wire:model="bedrooms" min="0" placeholder="3"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('bedrooms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="bathrooms" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">دورات المياه</label>
                    <input type="number" id="bathrooms" wire:model="bathrooms" min="0" placeholder="2"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('bathrooms') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="area_sqft" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">المساحة (م²)</label>
                    <input type="number" id="area_sqft" wire:model="area_sqft" min="0" placeholder="150"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('area_sqft') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="year_built" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">سنة البناء</label>
                    <input type="number" id="year_built" wire:model="year_built" min="1800" max="2099" placeholder="{{ date('Y') }}"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('year_built') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="owner_phone" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">رقم للتواصل (المالك)</label>
                    <input type="text" id="owner_phone" wire:model="owner_phone" placeholder="+967 770 000 000"
                        class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    @error('owner_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Description & Details --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">الوصف والتفاصيل</h2>
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    وصف العقار والخدمات المتوفرة <span class="text-red-500">*</span>
                </label>
                <textarea id="description" wire:model="description" rows="4" placeholder="اكتب وصفاً كاملاً للعقار ومميزاته والقرب من الخدمات..."
                    class="w-full p-3 text-sm rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Media Attachments --}}
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">الصور والفيديو</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="images" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">صور العقار (يمكن تحديد عدة صور)</label>
                    <input type="file" id="images" wire:model="images" multiple accept="image/*"
                        class="w-full text-xs text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    @error('images.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="video" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">فيديو للعقار (اختياري)</label>
                    <input type="file" id="video" wire:model="video" accept="video/mp4,video/quicktime"
                        class="w-full text-xs text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                    @error('video') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Submission Actions --}}
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
            <button type="submit" 
                class="py-3 px-6 text-sm font-bold text-white bg-primary-700 hover:bg-primary-800 rounded-xl shadow-md transition-all duration-150">
                تقديم طلب الإدراج
            </button>
        </div>
    </form>
</div>
