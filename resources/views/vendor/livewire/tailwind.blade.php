{{--
    Custom replacement for Laravel's default Tailwind pagination view.

    The stock view (vendor/laravel/framework/.../resources/views/tailwind.blade.php)
    only handles RTL with `rtl:flex-row-reverse` on the outer wrapper — the
    individual buttons still use physical `rounded-l-md`/`rounded-r-md` and
    `-ml-px`, and the prev/next chevrons never mirror. Reversing the flex
    order while every button's own rounding/margin stays anchored to the
    physical left produces a visually broken, misaligned control in RTL
    (rounded corners on the wrong end, gaps instead of a joined pill).
    Rebuilt with logical properties (`rounded-s-*`/`rounded-e-*`, `ms-*`)
    throughout, per this project's own RTL convention (CLAUDE.md), so no
    row-reversal hack is needed at all — the browser handles direction.
--}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        {{-- Mobile: simple prev/next --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        {{-- Desktop: full control with page numbers --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            {{-- Laravel's stock lang/{ar,en}/pagination.php only defines
                 previous/next (used below) — Showing/to/of/results were
                 never part of it, so a __() call here would just render
                 the raw English fallback string regardless of locale. This
                 project's own convention for public-site strings is an
                 inline ternary, not a translation file entry (CLAUDE.md). --}}
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ app()->getLocale() === 'ar' ? 'عرض' : 'Showing' }}
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $paginator->firstItem() ?? $paginator->count() }}</span>
                {{ app()->getLocale() === 'ar' ? 'إلى' : 'to' }}
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $paginator->lastItem() ?? $paginator->count() }}</span>
                {{ app()->getLocale() === 'ar' ? 'من' : 'of' }}
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $paginator->total() }}</span>
                {{ app()->getLocale() === 'ar' ? 'نتيجة' : 'results' }}
            </p>

            <div class="inline-flex items-center rounded-lg shadow-sm">
                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{!! __('pagination.previous') !!}" class="inline-flex items-center justify-center w-9 h-9 rounded-s-lg border border-gray-300 bg-white text-gray-300 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{!! __('pagination.previous') !!}" class="inline-flex items-center justify-center w-9 h-9 rounded-s-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 border-t border-b border-gray-300 bg-white text-sm text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center w-9 h-9 border-t border-b border-blue-600 bg-blue-600 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ app()->getLocale() === 'ar' ? 'الذهاب إلى صفحة ' . $page : 'Go to page ' . $page }}" class="inline-flex items-center justify-center w-9 h-9 border-t border-b border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{!! __('pagination.next') !!}" class="inline-flex items-center justify-center w-9 h-9 rounded-e-lg border border-gray-300 bg-white text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{!! __('pagination.next') !!}" class="inline-flex items-center justify-center w-9 h-9 rounded-e-lg border border-gray-300 bg-white text-gray-300 cursor-not-allowed dark:bg-gray-800 dark:border-gray-700 dark:text-gray-600">
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
