<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'عقاراتي' : 'My Properties' }}</h2>
                <p class="text-3xl font-bold">{{ $this->myProperties }}</p>
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
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'آخر نشاط للبائع' : 'Seller Recent Activity' }}</h2>
        <!-- Add seller-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>
