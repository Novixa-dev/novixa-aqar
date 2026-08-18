<x-filament-panels::page>
    {{ $this->getHeaderWidgets() }}

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Current Property</h2>
                <p class="text-3xl font-bold">{{ $this->currentProperty->address ?? 'N/A' }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Next Rent Due</h2>
                <p class="text-3xl font-bold">{{ $this->rentDueDate ? $this->rentDueDate->format('M d, Y') : 'N/A' }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Open Work Orders</h2>
                <p class="text-3xl font-bold">{{ $this->openWorkOrders }}</p>
            </x-filament::card>
        </div>
    </div>

    <div class="mt-6">
        {{ $this->getWidgets()['App\Filament\Tenant\Widgets\RecentMaintenanceRequests'] }}
    </div>
</x-filament-panels::page>