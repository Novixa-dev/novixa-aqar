<div class="mt-8 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">
    <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'شارك تجربتك' : 'Share Your Experience' }}</h3>
    <p class="text-gray-600 dark:text-gray-300 mb-4">{{ app()->getLocale() === 'ar' ? 'رأيك الصادق يساعد الآخرين على اتخاذ قرار مستنير. يرجى مشاركة تجربتك مع هذا العقار.' : 'Your honest review helps others make informed decisions. Please share your experience with this property.' }}</p>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    @error('general')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
    @enderror

    <form wire:submit.prevent="submitReview">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'عنوان التقييم' : 'Review Title' }}</label>
            <input wire:model="title" type="text" id="title" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'لخّص تجربتك' : 'Summarize your experience' }}">
            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'التقييم العام' : 'Overall Rating' }}</label>
            <div class="flex items-center mt-1">
                @for ($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="$set('rating', {{ $i }})"
                        aria-label="{{ app()->getLocale() === 'ar' ? $i . ' من 5 نجوم' : $i . ' out of 5 stars' }}"
                        class="focus:outline-none {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"><svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" /></svg></button>
                @endfor
            </div>
            @error('rating') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'تقييمك' : 'Your Review' }}</label>
            <textarea wire:model="comment" id="comment" rows="4" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'أخبر الآخرين عن تجربتك مع هذا العقار' : 'Tell others about your experience with this property' }}"></textarea>
            @error('comment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'سيتم مراجعة تقييمك قبل نشره.' : 'Your review will be moderated before it appears publicly.' }}</p>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ app()->getLocale() === 'ar' ? 'إرسال التقييم' : 'Submit Review' }}
            </button>
        </div>
    </form>
</div>
