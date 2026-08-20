<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Payment Portal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium mb-4">{{ __('Make a Payment') }}</h3>
                <livewire:tenant.make-payment />

                <h3 class="text-lg font-medium mt-8 mb-4">{{ __('Payment History') }}</h3>
                <livewire:tenant.payment-history />
            </div>
        </div>
    </div>
</x-app-layout>