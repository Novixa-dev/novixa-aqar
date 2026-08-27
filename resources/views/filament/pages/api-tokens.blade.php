<x-filament-panels::page>
    @if (\Laravel\Jetstream\Features::hasApiFeatures())
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            @livewire(\Laravel\Jetstream\Http\Livewire\ApiTokenManager::class)
        </div>
    @else
        <div class="fi-section rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ app()->getLocale() === 'ar'
                    ? 'رموز الوصول عبر واجهة برمجة التطبيقات غير مفعّلة لهذا التثبيت.'
                    : 'API access tokens are not enabled for this installation.' }}
            </p>
        </div>
    @endif
</x-filament-panels::page>
