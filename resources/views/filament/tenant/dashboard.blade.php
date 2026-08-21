<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'العقار الحالي' : 'Current Property' }}</h2>
                <p class="text-3xl font-bold">{{ $this->currentProperty->address ?? 'N/A' }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'موعد استحقاق الإيجار القادم' : 'Next Rent Due' }}</h2>
                <p class="text-3xl font-bold">{{ $this->rentDueDate ? $this->rentDueDate->translatedFormat('M d, Y') : 'N/A' }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'طلبات الصيانة المفتوحة' : 'Open Work Orders' }}</h2>
                <p class="text-3xl font-bold">{{ $this->openWorkOrders }}</p>
            </x-filament::card>
        </div>
    </div>
</x-filament-panels::page>
