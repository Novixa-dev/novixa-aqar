@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الصفحة غير موجودة' : 'Page Not Found')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4 py-16 text-center">
        <p class="text-7xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ app()->getLocale() === 'ar' ? '٤٠٤' : '404' }}</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
            {{ app()->getLocale() === 'ar' ? 'الصفحة غير موجودة' : 'Page Not Found' }}
        </h1>
        <p class="mt-3 max-w-md text-gray-600 dark:text-gray-400">
            {{ app()->getLocale() === 'ar'
                ? 'عذرًا، الصفحة التي تبحث عنها غير موجودة أو تم نقلها.'
                : 'Sorry, the page you are looking for does not exist or has been moved.' }}
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                {{ app()->getLocale() === 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
            </a>
            <a href="{{ url('/properties') }}" class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-600 px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                {{ app()->getLocale() === 'ar' ? 'تصفح العقارات' : 'Browse Properties' }}
            </a>
        </div>
    </div>
@endsection
