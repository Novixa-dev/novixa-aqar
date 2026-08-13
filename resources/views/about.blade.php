@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('About Us') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'عن منصة نوفيكسا عقار' : 'About Novixa Aqar' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'رؤية رقمية حديثة لإعادة ابتكار السوق العقاري وتسهيل عمليات الشراء والإيجار والاستثمار بأعلى درجات الشفافية والأمان.' : 'A modern digital vision reimagining real estate, empowering property discovery, leases, and smart investments with utmost security.' }}
        </p>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 lg:p-12 mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mb-6">
                    {{ app()->getLocale() === 'ar' ? 'رسالتنا وأهدافنا' : 'Our Mission & Vision' }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                    {{ app()->getLocale() === 'ar' ? 'تسعى نوفيكسا عقار (Novixa Aqar) لربط البائعين والمشترين، الملاك والمستأجرين، من خلال بيئة تقنية متكاملة توفر حلول التثمين الذكي بالذكاء الاصطناعي، الجولات الافتراضية ثلاثية الأبعاد، وتوثيق العقود إلكترونياً.' : 'Novixa Aqar is dedicated to connecting buyers, sellers, landlords, and tenants through an integrated intelligent ecosystem featuring AI property valuations, 3D holographic tours, and digital smart contracts.' }}
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl">
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mb-1">100%</div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'عقارات موثقة ومفحوصة' : 'Verified Listings' }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl">
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mb-1">24/7</div>
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'دعم فني وحجز مواعيد فوري' : 'Automated Booking & Support' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 p-8 rounded-3xl border border-blue-100 dark:border-gray-600">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ app()->getLocale() === 'ar' ? 'قيمنا الجوهرية' : 'Core Values' }}
                </h3>
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg">✓</span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'الشفافية والمصداقية:' : 'Transparency & Trust:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'بيانات دقيقة وتفاصيل واضحة لجميع العقارات والأسعار.' : 'Accurate real-time data and complete pricing clarity.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg">✓</span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'الابتكار التقني:' : 'Cutting-Edge Innovation:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'استخدام أحدث تقنيات الخرائط التفاعلية والذكاء الاصطناعي.' : 'Leveraging AI valuations and interactive geographic maps.' }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 text-lg">✓</span>
                        <div>
                            <strong>{{ app()->getLocale() === 'ar' ? 'خدمة العملاء المتميزة:' : 'Client-Centric Experience:' }}</strong>
                            <span>{{ app()->getLocale() === 'ar' ? 'متابعة حية وشاملة لاحتياجات العملاء والمستثمرين.' : 'Personalized support for tenants, buyers, and developers.' }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
