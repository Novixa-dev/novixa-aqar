<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('Recent Bookings') }}
        </x-slot>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($this->getRecentBookings() as $booking)
                <div class="py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $booking->property?->title ?? $booking->name ?? (app()->getLocale() === 'ar' ? 'حجز رقم ' : 'Booking #') . $booking->id }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $booking->date }} &bull; {{ $booking->status ?? (app()->getLocale() === 'ar' ? 'قيد الانتظار' : 'pending') }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $booking->created_at ? $booking->created_at->diffForHumans() : '' }}
                    </span>
                </div>
            @empty
                <p class="py-4 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('No recent bookings recorded yet.') }}
                </p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>