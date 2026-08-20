<div class="mt-8">
    <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إضافة تقييم للمستأجر' : 'Leave a Review for Tenant' }}</h3>
    <form wire:submit.prevent="submitReview">
        <div class="mb-4">
            <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'التقييم' : 'Rating' }}</label>
            <select wire:model="rating" id="rating" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="5">{{ app()->getLocale() === 'ar' ? '5 نجوم' : '5 Stars' }}</option>
                <option value="4">{{ app()->getLocale() === 'ar' ? '4 نجوم' : '4 Stars' }}</option>
                <option value="3">{{ app()->getLocale() === 'ar' ? '3 نجوم' : '3 Stars' }}</option>
                <option value="2">{{ app()->getLocale() === 'ar' ? 'نجمتان' : '2 Stars' }}</option>
                <option value="1">{{ app()->getLocale() === 'ar' ? 'نجمة واحدة' : '1 Star' }}</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ app()->getLocale() === 'ar' ? 'تعليق' : 'Comment' }}</label>
            <textarea wire:model="comment" id="comment" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
        </div>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ app()->getLocale() === 'ar' ? 'إرسال التقييم' : 'Submit Review' }}
        </button>
    </form>
</div>