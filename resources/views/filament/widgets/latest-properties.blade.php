<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Latest Properties') }}
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($this->getLatestProperties() as $property)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $property->title }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            ${{ number_format($property->price) }} &bull; {{ $property->status === 'For Rent' ? __('For Rent') : __('For Sale') }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $property->created_at ? $property->created_at->diffForHumans() : '' }}
                    </span>
                </div>
            @empty
                <p class="py-4 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('No properties recorded yet.') }}
                </p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>