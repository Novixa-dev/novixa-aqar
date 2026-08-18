<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Total Properties</h2>
                <p class="text-3xl font-bold">{{ $this->totalProperties }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Occupied Properties</h2>
                <p class="text-3xl font-bold">{{ $this->occupiedProperties }}</p>
            </x-filament::card>
        </div>

        <div>
            <x-filament::card>
                <h2 class="text-lg font-semibold">Total Tenants</h2>
                <p class="text-3xl font-bold">{{ $this->totalTenants }}</p>
            </x-filament::card>
        </div>
    </div>

    <x-filament::card class="mt-6">
        <h2 class="text-lg font-semibold mb-4">Landlord Recent Activity</h2>
        <!-- Add landlord-specific recent activity content here -->
    </x-filament::card>
</x-filament-panels::page>