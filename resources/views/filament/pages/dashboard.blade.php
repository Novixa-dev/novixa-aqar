<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'إجمالي العقارات' : 'Total Properties' }}</h2>
                <p class="text-3xl font-bold">{{ $this->totalProperties }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'الإعلانات النشطة' : 'Active Listings' }}</h2>
                <p class="text-3xl font-bold">{{ $this->activeListings }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'إجمالي الحجوزات' : 'Total Bookings' }}</h2>
                <p class="text-3xl font-bold">{{ $this->totalBookings }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'إجمالي الإيرادات' : 'Total Revenue' }}</h2>
                <p class="text-3xl font-bold">{{ number_format($this->totalRevenue, 2) }}</p>
            </x-filament::card>
        </div>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'آخر نشاط للتطبيق' : 'App Recent Activity' }}</h2>
        <!-- Add app-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>
