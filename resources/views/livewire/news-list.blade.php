<div>
    <div class="bg-gradient-to-b from-blue-50/50 to-white dark:from-gray-900 dark:to-gray-800 py-12 border-b border-gray-100 dark:border-gray-800">
        <div class="container mx-auto px-4 lg:px-8 text-center max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
                {{ __('News') }}
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
                {{ app()->getLocale() === 'ar' ? 'أخبار وتقارير السوق العقاري' : 'Real Estate News & Market Reports' }}
            </h1>
            <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
                {{ app()->getLocale() === 'ar' ? 'تابع آخر المستجدات والتحليلات والاتجاهات الاستثمارية في السوق العقاري المحلي والدولي.' : 'Stay updated with the latest real estate trends, investment insights, and market reports.' }}
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 lg:px-8 py-10">
        <!-- Featured News Section -->
        @if($featuredNews->count() > 0 && !$search && !$featuredOnly)
            <div class="mb-12">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="text-amber-500"><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg></span>
                    <span>{{ app()->getLocale() === 'ar' ? 'المقالات المميزة' : 'Featured Articles' }}</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($featuredNews as $featured)
                        <a href="{{ route('news.detail', $featured->slug) }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between group overflow-hidden">
                            <div class="relative h-40 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                                @if($featured->image_src)
                                    <img src="{{ $featured->image_src }}" alt="{{ $featured->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-700">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                    </div>
                                @endif
                                <span class="absolute top-3 start-3 inline-block px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50/95 dark:bg-amber-900/80 dark:text-amber-300 rounded-lg shadow-sm">
                                    {{ __('Featured') }}
                                </span>
                            </div>
                            <div class="p-6 flex flex-col justify-between flex-1">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                        {{ $featured->title }}
                                    </h3>
                                    @if($featured->excerpt)
                                        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-3 leading-relaxed">{{ $featured->excerpt }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <span>{{ $featured->published_at ? $featured->published_at->translatedFormat('M d, Y') : now()->translatedFormat('M d, Y') }}</span>
                                    <span class="text-blue-600 font-semibold">{{ __('Read More') }} <span class="inline-block {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span></span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Search and Filter -->
        <div class="mb-8 flex flex-col sm:flex-row gap-4 items-center justify-between bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
            <div class="flex-1 w-full relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white"
                       placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث في الأخبار والمقالات...' : 'Search articles and news...' }}">
            </div>
            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <input type="checkbox" wire:model.live="featuredOnly" class="rounded text-blue-600 focus:ring-blue-500">
                    <span>{{ app()->getLocale() === 'ar' ? 'المميزة فقط' : 'Featured Only' }}</span>
                </label>
            </div>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($news as $article)
                <a href="{{ route('news.detail', $article->slug) }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 flex flex-col justify-between group overflow-hidden">
                    <div class="relative h-36 w-full overflow-hidden bg-gray-100 dark:bg-gray-900">
                        @if($article->image_src)
                            <img src="{{ $article->image_src }}" alt="{{ $article->title }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300 dark:text-gray-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            </div>
                        @endif
                        @if($article->is_featured)
                            <span class="absolute top-3 start-3 inline-block px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50/95 dark:bg-amber-900/80 dark:text-amber-300 rounded-lg shadow-sm">
                                {{ __('Featured') }}
                            </span>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col justify-between flex-1">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                {{ $article->title }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 line-clamp-3 leading-relaxed">
                                {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <span>{{ $article->published_at ? $article->published_at->translatedFormat('M d, Y') : now()->translatedFormat('M d, Y') }}</span>
                            <span class="text-blue-600 font-bold">
                                {{ __('Read More') }} <span class="inline-block {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد مقالات منشورة حالياً.' : 'No news articles found at the moment.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $news->links() }}
        </div>
    </div>
</div>
