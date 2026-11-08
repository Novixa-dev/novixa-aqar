<div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إنشاء تقرير مخصص' : 'Generate Custom Report' }}</h2>

    <form wire:submit.prevent="generateReport">
        <div class="mb-4">
            <label for="reportType" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'نوع التقرير' : 'Report Type' }}</label>
            <select wire:model="reportType" id="reportType" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر نوع التقرير' : 'Select a report type' }}</option>
                <option value="financial">{{ app()->getLocale() === 'ar' ? 'تقرير مالي' : 'Financial Report' }}</option>
                <option value="occupancy">{{ app()->getLocale() === 'ar' ? 'تقرير الإشغال' : 'Occupancy Report' }}</option>
                <option value="maintenance">{{ app()->getLocale() === 'ar' ? 'تقرير الصيانة' : 'Maintenance Report' }}</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date' }}</label>
            <input type="date" wire:model="startDate" id="startDate" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div class="mb-4">
            <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'تاريخ النهاية' : 'End Date' }}</label>
            <input type="date" wire:model="endDate" id="endDate" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>

        <div class="mb-4">
            <label for="properties" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'العقارات' : 'Properties' }}</label>
            <select wire:model="selectedProperties" id="properties" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->location }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label for="tenants" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'المستأجرون' : 'Tenants' }}</label>
            <select wire:model="selectedTenants" id="tenants" multiple class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-between">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ app()->getLocale() === 'ar' ? 'إنشاء التقرير' : 'Generate Report' }}
            </button>
        </div>
    </form>
</div>
