<div class="bg-white dark:bg-gray-900 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white mb-2">
                    {{ app()->getLocale() === 'ar' ? 'عمليات البحث المحفوظة' : 'Saved searches' }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ app()->getLocale() === 'ar'
                        ? 'نرسل لك تنبيهاً يومياً عند نشر عقارات جديدة تطابق بحثك.'
                        : 'We send you a daily alert when new listings matching your search are published.' }}
                </p>
            </div>

            @forelse ($searches as $search)
                <div wire:key="saved-search-{{ $search->id }}" class="mb-4 p-5 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $search->summary() }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ app()->getLocale() === 'ar' ? 'عدد العقارات المطابقة الآن:' : 'Matching listings now:' }}
                            <span class="font-bold">{{ number_format($search->match_count) }}</span>
                            · {{ app()->getLocale() === 'ar' ? 'حُفظ' : 'Saved' }} {{ $search->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ $search->url() }}" class="px-3 py-2 rounded-lg text-xs font-bold text-white bg-blue-600 hover:bg-blue-700">
                            {{ app()->getLocale() === 'ar' ? 'عرض النتائج' : 'View results' }}
                        </a>
                        <button type="button" wire:click="toggleAlerts({{ $search->id }})"
                            aria-pressed="{{ $search->alerts_enabled ? 'true' : 'false' }}"
                            class="px-3 py-2 rounded-lg text-xs font-bold border {{ $search->alerts_enabled ? 'border-green-600 text-green-700 dark:text-green-400' : 'border-gray-300 text-gray-500 dark:text-gray-400' }}">
                            {{ $search->alerts_enabled
                                ? (app()->getLocale() === 'ar' ? 'التنبيهات مفعّلة' : 'Alerts on')
                                : (app()->getLocale() === 'ar' ? 'التنبيهات متوقفة' : 'Alerts off') }}
                        </button>
                        <button type="button" wire:click="delete({{ $search->id }})"
                            wire:confirm="{{ app()->getLocale() === 'ar' ? 'حذف هذا البحث المحفوظ؟' : 'Delete this saved search?' }}"
                            class="px-3 py-2 rounded-lg text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/20 hover:bg-red-100">
                            {{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ app()->getLocale() === 'ar'
                            ? 'لا توجد عمليات بحث محفوظة بعد. اختر الفلاتر في صفحة العقارات ثم اضغط "احفظ البحث ونبّهني".'
                            : 'No saved searches yet. Pick filters on the properties page, then press "Save search & alert me".' }}
                    </p>
                    <a href="{{ route('property.list') }}" class="inline-block px-4 py-2 rounded-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-700">
                        {{ app()->getLocale() === 'ar' ? 'تصفح العقارات' : 'Browse properties' }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
