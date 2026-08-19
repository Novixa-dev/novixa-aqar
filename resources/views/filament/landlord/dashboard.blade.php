<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'إجمالي العقارات' : 'Total Properties' }}</h2>
                <p class="text-3xl font-bold">{{ $this->totalProperties }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'العقارات المؤجرة' : 'Occupied Properties' }}</h2>
                <p class="text-3xl font-bold">{{ $this->occupiedProperties }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'إجمالي المستأجرين' : 'Total Tenants' }}</h2>
                <p class="text-3xl font-bold">{{ $this->totalTenants }}</p>
            </x-filament::card>
        </div>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'آخر نشاط لمالك العقار' : 'Landlord Recent Activity' }}</h2>
        <!-- Add landlord-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>
