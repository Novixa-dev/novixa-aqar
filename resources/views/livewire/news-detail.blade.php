<div class="bg-[#FAF8F5] dark:bg-[#141211] min-h-screen py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('news.list') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-[#1C1816] border border-stone-200 dark:border-stone-800 text-stone-700 dark:text-stone-300 hover:text-[#8C7869] dark:hover:text-[#8C7869] text-xs font-bold transition-all shadow-sm group">
                <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }} group-hover:-translate-x-1 transition-transform">&larr;</span>
                <span>{{ app()->getLocale() === 'ar' ? 'العودة لقائمة الأخبار والتقارير' : 'Back to News & Reports' }}</span>
            </a>
        </div>

        <!-- Article Card -->
        <article class="bg-white dark:bg-[#1C1816] rounded-3xl shadow-xl p-8 sm:p-12 mb-10 border border-stone-200/80 dark:border-stone-800">
            <!-- Featured Badge -->
            @if($news->is_featured)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-white bg-amber-500 rounded-lg mb-6 shadow-sm">
                    <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>{{ app()->getLocale() === 'ar' ? 'تقرير مميز' : 'Featured Article' }}</span>
                </span>
            @endif

            <!-- Title -->
            <h1 class="text-2xl sm:text-4xl font-black text-stone-900 dark:text-white mb-6 leading-snug tracking-tight">
                {{ $news->title }}
            </h1>

            <!-- Meta Information -->
            <div class="flex items-center text-stone-500 dark:text-stone-400 text-xs sm:text-sm mb-8 pb-6 border-b border-stone-200 dark:border-stone-800 flex-wrap gap-4">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#8C7869]" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <span>{{ $news->published_at ? $news->published_at->translatedFormat('d M, Y') : '' }}</span>
                </div>
                @if($news->author)
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#8C7869]" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>{{ $news->author->name }}</span>
                    </div>
                @endif
            </div>

            <!-- Excerpt Callout -->
            @if($news->excerpt)
                <div class="text-base sm:text-lg text-stone-700 dark:text-stone-300 font-medium mb-8 p-6 bg-[#8C7869]/10 rounded-2xl border-s-4 border-[#8C7869] leading-relaxed">
                    {{ $news->excerpt }}
                </div>
            @endif

            <!-- Sanitized Content -->
            <div class="prose prose-lg dark:prose-invert max-w-none text-stone-800 dark:text-stone-200 leading-loose font-light">
                {!! $news->sanitized_content !!}
            </div>
        </article>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
            <div class="mb-10">
                <h2 class="text-xl font-bold text-stone-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#8C7869]"></span>
                    <span>{{ app()->getLocale() === 'ar' ? 'مقالات ذات صلة' : 'Related Articles' }}</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relatedNews as $related)
                        <div class="bg-white dark:bg-[#1C1816] rounded-2xl shadow-sm border border-stone-200/80 dark:border-stone-800 p-6 flex flex-col justify-between hover:shadow-lg transition-all group hover:-translate-y-1">
                            <div>
                                <h3 class="text-base font-bold text-stone-900 dark:text-white mb-2.5 line-clamp-2 group-hover:text-[#8C7869] transition-colors">
                                    <a href="{{ route('news.detail', $related->slug) }}">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                @if($related->excerpt)
                                    <p class="text-stone-500 dark:text-stone-400 text-xs line-clamp-2 mb-4 leading-relaxed font-light">
                                        {{ $related->excerpt }}
                                    </p>
                                @endif
                            </div>
                            <div class="text-[11px] text-stone-400 pt-3 border-t border-stone-100 dark:border-stone-800 flex items-center justify-between">
                                <span>{{ $related->published_at ? $related->published_at->translatedFormat('d M, Y') : '' }}</span>
                                <span class="text-[#8C7869] font-bold">{{ __('Read More') }} &rarr;</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
