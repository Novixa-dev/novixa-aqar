<x-filament-panels::page>
    <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        @if ($plaidClientId && $plaidSecret)
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ app()->getLocale() === 'ar'
                    ? 'بيئة Plaid الحالية: '
                    : 'Current Plaid environment: ' }}
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $plaidEnvironment ?? 'sandbox' }}</span>
            </p>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ app()->getLocale() === 'ar'
                    ? 'لم يتم إعداد تكامل Plaid بعد لهذه البيئة.'
                    : 'Plaid integration has not been configured for this environment yet.' }}
            </p>
        @endif
    </div>
</x-filament-panels::page>
