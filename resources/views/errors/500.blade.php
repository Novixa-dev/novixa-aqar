@extends('layouts.app')

@section('title', app()->getLocale() === 'ar' ? 'خطأ في الخادم' : 'Server Error')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4 py-16 text-center">
        <p class="text-7xl font-bold tracking-tight text-red-600 dark:text-red-400">{{ app()->getLocale() === 'ar' ? '٥٠٠' : '500' }}</p>
        <h1 class="mt-4 text-2xl font-semibold text-gray-900 dark:text-white">
            {{ app()->getLocale() === 'ar' ? 'خطأ في الخادم' : 'Server Error' }}
        </h1>
        <p class="mt-3 max-w-md text-gray-600 dark:text-gray-400">
            {{ app()->getLocale() === 'ar'
                ? 'حدث خطأ غير متوقع على الخادم. نعمل على إصلاحه، يرجى المحاولة لاحقًا.'
                : 'An unexpected error occurred on the server. We are working on it, please try again later.' }}
        </p>
        <div class="mt-8">
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                {{ app()->getLocale() === 'ar' ? 'العودة للرئيسية' : 'Back to Home' }}
            </a>
        </div>
    </div>
@endsection
