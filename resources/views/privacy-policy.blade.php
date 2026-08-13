@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('Privacy Policy') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية وحماية البيانات' : 'Privacy & Data Protection Policy' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'نلتزم في منصة نوفيكسا عقار بحماية خصوصية مستخدمينا وتأمين بياناتهم الشخصية وفق أعلى المعايير الأمنية.' : 'At Novixa Aqar, we are strictly committed to safeguarding our users’ personal information with industry-leading security.' }}
        </p>
    </div>

    <!-- Content Card -->
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 lg:p-12 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">
        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">1</span>
                <span>{{ app()->getLocale() === 'ar' ? 'البيانات التي نجمعها' : 'Information We Collect' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'نقوم بجمع البيانات الضرورية لتقديم الخدمات مثل الاسم، رقم الهاتف، البريد الإلكتروني، وتفضيلات البحث العقاري ومواعيد المعاينة لتسهيل تواصل الوكلاء والملاك معك.' : 'We collect necessary information such as name, phone number, email, and property preferences to facilitate inquiries and bookings.' }}
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">2</span>
                <span>{{ app()->getLocale() === 'ar' ? 'كيفية استخدام البيانات' : 'How We Use Your Information' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'تُستخدم بياناتك لإدارة الحسابات، تحسين نتائج البحث، إتمام طلبات المعاينة، وتوثيق عقود الإيجار الرقمية، ولن يتم بيع أو تأجير بياناتك لأي طرف ثالث مطلقاً.' : 'Your data is used solely to manage accounts, process booking requests, power AI recommendations, and execute digital contracts. We never sell your personal information.' }}
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">3</span>
                <span>{{ app()->getLocale() === 'ar' ? 'الأمان والتشفير' : 'Security & Encryption' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'نطبق بروتوكولات تشفير متقدمة لحماية قواعد البيانات والاتصال بين متصفحك وخوادم المنصة لمنع أي وصول غير مصرح به.' : 'We implement advanced encryption protocols and security best practices to protect your data against unauthorized access.' }}
            </p>
        </section>
    </div>
</div>
@endsection
