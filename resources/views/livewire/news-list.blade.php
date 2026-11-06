@php
    $covers = [
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
        'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=80',
    ];
@endphp

<div class="relative w-full overflow-hidden">
    <!-- Hero Header Section -->
    <div class="relative bg-[#1C1816] text-white py-20 lg:py-24 overflow-hidden border-b border-white/10">
        <!-- Background Architectural Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1920&q=80" 
                 alt="Real Estate News Background" 
                 class="w-full h-full object-cover opacity-20 filter blur-[1px]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#1C1816]/95 via-[#1C1816]/85 to-[#1C1816]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_center,rgba(140,120,105,0.25),transparent_70%)]"></div>
        </div>

        <div class="relative z-10 container mx-auto px-4 lg:px-8 text-center max-w-4xl">
            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl font-black text-white leading-tight tracking-tight mb-5">
                {{ app()->getLocale() === 'ar' ? 'أخبار وتقارير السوق العقاري' : 'Real Estate News & Market Reports' }}
            </h1>

            <!-- Subtitle Paragraph -->
            <p class="text-base sm:text-lg text-stone-300 font-light leading-relaxed max-w-3xl mx-auto">
                {{ app()->getLocale() === 'ar' ? 'تابع آخر المستجدات والتحليلات والاتجاهات الاستثمارية في السوق العقاري المحلي والدولي.' : 'Stay updated with the latest real estate trends, investment insights, and market reports.' }}
            </p>
        </div>
    </div>

    <!-- Search & Filter Bar (Floating Container) -->
    <div class="relative z-20 container mx-auto px-4 lg:px-8 -mt-8 mb-12">
        <div class="max-w-5xl mx-auto bg-white dark:bg-[#1C1816] rounded-2xl p-4 sm:p-5 shadow-xl border border-stone-200/80 dark:border-stone-800">
            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                <!-- Search Input with Icon -->
                <div class="flex-1 w-full relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none text-[#8C7869]">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           class="w-full ps-11 pe-4 py-3 bg-stone-50 dark:bg-stone-800/60 border border-stone-200 dark:border-stone-700 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-stone-900 dark:text-white transition-all placeholder:text-stone-400"
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث في الأخبار والمقالات...' : 'Search articles and news...' }}">
                </div>

                <!-- Featured Toggle Button -->
                <div class="shrink-0 flex items-center">
                    <label class="inline-flex items-center gap-2.5 px-4 py-3 rounded-xl bg-stone-50 dark:bg-stone-800/60 border border-stone-200 dark:border-stone-700 cursor-pointer hover:border-[#8C7869] transition-all select-none">
                        <input type="checkbox" wire:model.live="featuredOnly" class="rounded text-[#8C7869] focus:ring-[#8C7869] w-4 h-4 cursor-pointer">
                        <span class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-stone-700 dark:text-stone-300">
                            <svg class="w-4 h-4 text-amber-500 fill-amber-500" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span>{{ app()->getLocale() === 'ar' ? 'المميزة فقط' : 'Featured Only' }}</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container mx-auto px-4 lg:px-8 pb-20">
        <!-- Featured News Section (When no active search/filters) -->
        @if($featuredNews->count() > 0 && !$search && !$featuredOnly)
            <div class="max-w-6xl mx-auto mb-16">
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </span>
                        <h2 class="text-2xl font-black text-stone-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'المقالات المميزة' : 'Featured Articles' }}
                        </h2>
                    </div>
                </div>

                <!-- Featured Grid (Hero Card + Supporting Cards) -->
                @php
                    $leadArticle = $featuredNews->first();
                    $otherFeatured = $featuredNews->slice(1);
                @endphp

                <!-- Lead Magazine Hero Card -->
                @if($leadArticle)
                    <div class="bg-white dark:bg-[#1C1816] rounded-3xl overflow-hidden shadow-lg border border-stone-200/80 dark:border-stone-800 mb-8 group hover:border-[#8C7869]/50 transition-all">
                        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">
                            <!-- Image Half (6 cols) -->
                            <div class="lg:col-span-6 relative overflow-hidden min-h-[280px] lg:min-h-[380px]">
                                <img src="{{ $leadArticle->image_src ?: $covers[0] }}"
                                     alt="{{ $leadArticle->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent lg:hidden"></div>
                                <span class="absolute top-4 start-4 px-3.5 py-1 rounded-full bg-amber-500 text-white text-xs font-black shadow-md flex items-center gap-1.5">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span>{{ __('Featured') }}</span>
                                </span>
                            </div>

                            <!-- Text Half (6 cols) -->
                            <div class="lg:col-span-6 p-8 lg:p-12 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-3 text-xs text-stone-400 mb-4">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-[#8C7869]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $leadArticle->published_at ? $leadArticle->published_at->translatedFormat('d M, Y') : now()->translatedFormat('d M, Y') }}
                                        </span>
                                        @if($leadArticle->author)
                                            <span>•</span>
                                            <span>{{ $leadArticle->author->name }}</span>
                                        @endif
                                    </div>

                                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-stone-900 dark:text-white leading-snug mb-4 group-hover:text-[#8C7869] transition-colors">
                                        <a href="{{ route('news.detail', $leadArticle->slug) }}">
                                            {{ $leadArticle->title }}
                                        </a>
                                    </h3>

                                    @if($leadArticle->excerpt)
                                        <p class="text-stone-600 dark:text-stone-300 text-sm sm:text-base leading-relaxed line-clamp-3 mb-6 font-light">
                                            {{ $leadArticle->excerpt }}
                                        </p>
                                    @endif
                                </div>

                                <div class="pt-6 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                                    <a href="{{ route('news.detail', $leadArticle->slug) }}" 
                                       class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-xs sm:text-sm font-bold shadow-md transition-all">
                                        <span>{{ __('Read More') }}</span>
                                        <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Supporting Featured Grid -->
                @if($otherFeatured->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($otherFeatured as $featured)
                            @php
                                $imgIndex = ($featured->id) % count($covers);
                            @endphp
                            <div class="bg-white dark:bg-[#1C1816] rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1">
                                <div>
                                    <div class="relative aspect-video overflow-hidden">
                                        <img src="{{ $featured->image_src ?: $covers[$imgIndex] }}"
                                             alt="{{ $featured->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                        <span class="absolute top-3 start-3 px-2.5 py-1 text-xs font-bold text-white bg-amber-500 rounded-lg shadow-sm">
                                            {{ __('Featured') }}
                                        </span>
                                    </div>
                                    <div class="p-6">
                                        <div class="text-xs text-stone-400 mb-2.5">
                                            {{ $featured->published_at ? $featured->published_at->translatedFormat('M d, Y') : now()->translatedFormat('M d, Y') }}
                                        </div>
                                        <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-3 line-clamp-2 group-hover:text-[#8C7869] transition-colors">
                                            <a href="{{ route('news.detail', $featured->slug) }}">
                                                {{ $featured->title }}
                                            </a>
                                        </h3>
                                        @if($featured->excerpt)
                                            <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm line-clamp-2 leading-relaxed">
                                                {{ $featured->excerpt }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-6 pt-0 flex items-center justify-between text-xs font-bold text-[#8C7869]">
                                    <a href="{{ route('news.detail', $featured->slug) }}" class="inline-flex items-center gap-1.5 hover:underline">
                                        <span>{{ __('Read More') }}</span>
                                        <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- All Articles Section -->
        <div class="max-w-6xl mx-auto">
            <!-- Section Title -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-stone-200/80 dark:border-stone-800">
                <h2 class="text-xl sm:text-2xl font-black text-stone-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span>{{ app()->getLocale() === 'ar' ? 'أحدث التقارير والمقالات' : 'Latest Articles & Reports' }}</span>
                    @if($news->total() > 0)
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#8C7869]/15 text-[#8C7869]">
                            {{ $news->total() }}
                        </span>
                    @endif
                </h2>
            </div>

            <!-- Articles 3-Column Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                @forelse($news as $article)
                    @php
                        $artImg = $article->image_src ?: $covers[($article->id) % count($covers)];
                    @endphp
                    <div class="bg-white dark:bg-[#1C1816] rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-200/80 dark:border-stone-800 flex flex-col justify-between group hover:-translate-y-1">
                        <div>
                            <!-- Card Image -->
                            <div class="relative aspect-[16/10] overflow-hidden">
                                <img src="{{ $artImg }}" 
                                     alt="{{ $article->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-80"></div>
                                
                                @if($article->is_featured)
                                    <span class="absolute top-3.5 start-3.5 px-2.5 py-1 text-[11px] font-bold text-white bg-amber-500 rounded-lg shadow-sm">
                                        {{ __('Featured') }}
                                    </span>
                                @endif

                                <div class="absolute bottom-3 start-3 text-[11px] text-white/90 font-medium">
                                    {{ $article->published_at ? $article->published_at->translatedFormat('d M, Y') : now()->translatedFormat('d M, Y') }}
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-stone-900 dark:text-white mb-3 line-clamp-2 group-hover:text-[#8C7869] transition-colors leading-snug">
                                    <a href="{{ route('news.detail', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-stone-500 dark:text-stone-400 text-xs sm:text-sm line-clamp-3 leading-relaxed font-light">
                                    {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-6 pt-0 flex items-center justify-between border-t border-stone-100 dark:border-stone-800 text-xs">
                            <span class="text-stone-400">
                                {{ $article->author ? $article->author->name : (app()->getLocale() === 'ar' ? 'فريق التحرير' : 'Editorial Team') }}
                            </span>
                            <a href="{{ route('news.detail', $article->slug) }}" class="inline-flex items-center gap-1.5 font-bold text-[#8C7869] hover:underline">
                                <span>{{ __('Read More') }}</span>
                                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-16 bg-white dark:bg-[#1C1816] rounded-3xl border border-dashed border-stone-200 dark:border-stone-800 p-8">
                        <div class="w-16 h-16 rounded-2xl bg-stone-100 dark:bg-stone-800 text-stone-400 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <p class="text-stone-500 dark:text-stone-400 text-base font-medium mb-4">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد مقالات منشورة حالياً.' : 'No news articles found at the moment.' }}
                        </p>
                        @if($search || $featuredOnly)
                            <button wire:click="$set('search', ''); $set('featuredOnly', false);" 
                                    class="px-5 py-2.5 rounded-xl bg-[#8C7869] hover:bg-[#7A6759] text-white text-xs font-bold transition-all">
                                {{ app()->getLocale() === 'ar' ? 'إعادة ضبط خيارات البحث' : 'Reset Search' }}
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination Bar -->
            <div class="mt-8">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</div>
