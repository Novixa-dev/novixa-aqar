<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'المهام المفتوحة' : 'Open Jobs' }}</h2>
                <p class="text-3xl font-bold">{{ $this->openJobs }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'المهام المكتملة' : 'Completed Jobs' }}</h2>
                <p class="text-3xl font-bold">{{ $this->completedJobs }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">{{ app()->getLocale() === 'ar' ? 'المدفوعات المعلقة' : 'Pending Payments' }}</h2>
                <p class="text-3xl font-bold">{{ $this->pendingPayments }}</p>
            </x-filament::card>
        </div>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ app()->getLocale() === 'ar' ? 'آخر نشاط للمقاول' : 'Contractor Recent Activity' }}</h2>
        <!-- Add contractor-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>
