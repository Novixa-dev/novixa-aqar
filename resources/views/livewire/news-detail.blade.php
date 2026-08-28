<div>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('news.list') }}"
                    class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 hover:underline font-bold text-sm">
                    <span class="{{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}">&larr;</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'العودة لقائمة الأخبار' : 'Back to News' }}</span>
                </a>
            </div>

            <!-- Article Content -->
            <article class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-6 sm:p-10 mb-8 border border-gray-100 dark:border-gray-700">
                <!-- Featured Badge -->
                @if($news->is_featured)
                    <span class="inline-block px-3 py-1 text-xs font-bold text-gray-900 bg-amber-400 rounded-lg mb-4 shadow-xs">
                        {{ app()->getLocale() === 'ar' ? 'تقرير مميز' : 'Featured Article' }}
                    </span>
                @endif

                <!-- Title -->
                <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4 leading-snug">
                    {{ $news->title }}
                </h1>

                <!-- Meta Information -->
                <div class="flex items-center text-gray-500 dark:text-gray-400 text-xs sm:text-sm mb-6 pb-6 border-b border-gray-200 dark:border-gray-700 flex-wrap gap-4">
                    <div class="flex items-center gap-1.5">
                        <span><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg></span>
                        <span>{{ $news->published_at ? $news->published_at->translatedFormat('M d, Y') : '' }}</span>
                    </div>
                    @if($news->author)
                        <div class="flex items-center gap-1.5">
                            <span><svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg></span>
                            <span>{{ $news->author->name }}</span>
                        </div>
                    @endif
                </div>

                <!-- Excerpt -->
                @if($news->excerpt)
                    <div class="text-base sm:text-lg text-gray-600 dark:text-gray-300 font-medium mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border-s-4 border-blue-600 leading-relaxed">
                        {{ $news->excerpt }}
                    </div>
                @endif

                <!-- Content -->
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 leading-loose">
                    {!! $news->sanitized_content !!}
                </div>
            </article>

            <!-- Related News -->
            @if($relatedNews->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ app()->getLocale() === 'ar' ? 'مقالات ذات صلة' : 'Related Articles' }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedNews as $related)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex flex-col justify-between hover:shadow-lg transition-all">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                        <a href="{{ route('news.detail', $related->slug) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $related->title }}
                                        </a>
                                    </h3>
                                    @if($related->excerpt)
                                        <p class="text-gray-500 dark:text-gray-400 text-xs line-clamp-2 mb-3">
                                            {{ $related->excerpt }}
                                        </p>
                                    @endif
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    {{ $related->published_at ? $related->published_at->translatedFormat('M d, Y') : '' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
