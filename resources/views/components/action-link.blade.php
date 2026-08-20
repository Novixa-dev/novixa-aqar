<a {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-800 dark:border-gray-300 rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-700 hover:border-gray-600 active:border-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150'])}}>
    {{ $slot }}
</a>
