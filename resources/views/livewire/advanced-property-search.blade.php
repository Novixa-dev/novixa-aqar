<div>
    <form wire:submit.prevent="search" class="bg-white dark:bg-gray-800 shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}</label>
                <input type="text" id="search" wire:model.live.debounce.300ms="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="postalCode" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'الرمز البريدي' : 'Postal Code' }}</label>
                <input type="text" id="postalCode" wire:model.live.debounce.300ms="postalCode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('postalCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="propertyType" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'نوع العقار' : 'Property Type' }}</label>
                <select id="propertyType" wire:model="propertyType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                    <option value="apartment">{{ app()->getLocale() === 'ar' ? 'شقة' : 'Apartment' }}</option>
                    <option value="house">{{ app()->getLocale() === 'ar' ? 'منزل' : 'House' }}</option>
                    <option value="condo">{{ app()->getLocale() === 'ar' ? 'شقة سكنية' : 'Condo' }}</option>
                </select>
            </div>
            <div>
                <label for="minPrice" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل سعر' : 'Min Price' }}</label>
                <input type="number" id="minPrice" wire:model="minPrice" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="maxPrice" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أعلى سعر' : 'Max Price' }}</label>
                <input type="number" id="maxPrice" wire:model="maxPrice" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="minBedrooms" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل عدد غرف نوم' : 'Min Bedrooms' }}</label>
                <input type="number" id="minBedrooms" wire:model="minBedrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="maxBedrooms" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أعلى عدد غرف نوم' : 'Max Bedrooms' }}</label>
                <input type="number" id="maxBedrooms" wire:model="maxBedrooms" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="yearBuilt" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'سنة البناء (بعد)' : 'Year Built (After)' }}</label>
                <input type="number" id="yearBuilt" wire:model="yearBuilt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
                <select id="status" wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="for_sale">{{ app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale' }}</option>
                    <option value="sold">{{ app()->getLocale() === 'ar' ? 'تم البيع' : 'Sold' }}</option>
                    <option value="Pending">{{ app()->getLocale() === 'ar' ? 'قيد المراجعة' : 'Pending' }}</option>
                </select>
            </div>

            <!-- Enhanced Filters -->
            <div>
                <label for="energyRating" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'كفاءة الطاقة' : 'Energy Rating' }}</label>
                <select id="energyRating" wire:model="energyRating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Ratings' }}</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="F">F</option>
                    <option value="G">G</option>
                </select>
            </div>
            <div>
                <label for="minEnergyScore" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل مؤشر كفاءة طاقة' : 'Min Energy Score' }}</label>
                <input type="number" id="minEnergyScore" wire:model="minEnergyScore" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0-100">
            </div>
            <div>
                <label for="minWalkabilityScore" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل مؤشر إمكانية المشي' : 'Min Walkability Score' }}</label>
                <input type="number" id="minWalkabilityScore" wire:model="minWalkabilityScore" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0-100">
            </div>
            <div>
                <label for="minTransitScore" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل مؤشر نقل عام' : 'Min Transit Score' }}</label>
                <input type="number" id="minTransitScore" wire:model="minTransitScore" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0-100">
            </div>
            <div>
                <label for="minBikeScore" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'أقل مؤشر ملاءمة الدراجات' : 'Min Bike Score' }}</label>
                <input type="number" id="minBikeScore" wire:model="minBikeScore" min="0" max="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="0-100">
            </div>
            <div>
                <label for="country" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}</label>
                <select id="country" wire:model="country" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الدول' : 'All Countries' }}</option>
                    <option value="Yemen">{{ app()->getLocale() === 'ar' ? 'اليمن' : 'Yemen' }}</option>
                </select>
            </div>
            <div>
                <label for="featuredOnly" class="flex items-center">
                    <input type="checkbox" id="featuredOnly" wire:model="featuredOnly" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <span class="ms-2 text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'العقارات المميزة فقط' : 'Featured Properties Only' }}</span>
                </label>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="w-full bg-[#8C7869] hover:bg-[#7A6759] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                {{ app()->getLocale() === 'ar' ? 'بحث عن عقارات' : 'Search Properties' }}
            </button>
        </div>
    </form>

    <div class="mt-4">
        <label for="sortBy" class="block text-sm font-medium text-gray-700">{{ app()->getLocale() === 'ar' ? 'ترتيب حسب' : 'Sort By' }}</label>
        <select id="sortBy" wire:model="sortBy" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="created_at">{{ app()->getLocale() === 'ar' ? 'الأحدث' : 'Newest' }}</option>
            <option value="price_asc">{{ app()->getLocale() === 'ar' ? 'السعر (من الأقل للأعلى)' : 'Price (Low to High)' }}</option>
            <option value="price_desc">{{ app()->getLocale() === 'ar' ? 'السعر (من الأعلى للأقل)' : 'Price (High to Low)' }}</option>
            <option value="bedrooms">{{ app()->getLocale() === 'ar' ? 'غرف النوم' : 'Bedrooms' }}</option>
        </select>
    </div>

    <div class="mt-8">
        <x-property-map />
    </div>

    <!-- ... (keep existing property list display) ... -->
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('propertiesUpdated', function (properties) {
            // This event is emitted when the properties are updated
            // It will trigger the map update in the property-map component
        });
    });
</script>
@endpush
