@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'الموقع تحت الصيانة' : 'Site Under Maintenance')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4 py-16 text-center">
        <p class="text-7xl font-bold tracking-tight text-yellow-500 dark:text-yellow-400">{{ app()->getLocale() === 'ar' ? '٥٠٣' : '503' }}</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
            {{ app()->getLocale() === 'ar' ? 'الموقع تحت الصيانة' : 'Site Under Maintenance' }}
        </h1>
        <p class="mt-3 max-w-md text-gray-600 dark:text-gray-400">
            {{ app()->getLocale() === 'ar'
                ? 'نجري بعض الأعمال التحسينية وسنعود قريبًا.'
                : 'We are performing some improvements and will be back soon.' }}
        </p>
        <div class="mt-8">
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                {{ app()->getLocale() === 'ar' ? 'إعادة المحاولة' : 'Try Again' }}
            </a>
        </div>
    </div>
@endsection
