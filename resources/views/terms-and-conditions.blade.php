@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-16">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('Terms & Conditions') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms and Conditions' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'يرجى قراءة الشروط والأحكام بعناية قبل استخدام منصة نوفيكسا عقار (Novixa Aqar).' : 'Please read these terms and conditions carefully before using Novixa Aqar platform.' }}
        </p>
    </div>

    <!-- Content Card -->
    <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 lg:p-12 space-y-8 text-gray-700 dark:text-gray-300 leading-relaxed">
        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">1</span>
                <span>{{ app()->getLocale() === 'ar' ? 'الموافقة على الشروط' : 'Acceptance of Terms' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'باستخدامك لمنصة نوفيكسا عقار أو أي من خدماتها، فإنك تقر وتوافق على الالتزام بكافة الشروط والسياسات المذكورة هنا وجميع القوانين المعمول بها.' : 'By accessing or using Novixa Aqar platform, you agree to be bound by these Terms and Conditions and all applicable laws and regulations.' }}
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">2</span>
                <span>{{ app()->getLocale() === 'ar' ? 'استخدام الخدمات وقوائم العقارات' : 'Use of Services & Listings' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'تتيح المنصة استعراض العقارات، طلب المعاينات، وتقديم عروض الشراء والإيجار. يتحمل المستخدم مسؤولية التأكد من صحة بياناته واستخدام المنصة لأغراض قانونية ومشروعة فقط.' : 'Our platform provides real estate listings and related services. You agree to use these services only for lawful purposes and verified business transactions.' }}
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">3</span>
                <span>{{ app()->getLocale() === 'ar' ? 'الحسابات وسرية البيانات' : 'User Accounts & Security' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'يلتزم المستخدم بالحفاظ على سرية بيانات حسابه وكلمة المرور، ويتحمل المسؤولية الكاملة عن كافة الأنشطة التي تتم من خلال حسابه.' : 'Users are responsible for maintaining the confidentiality of their credentials and for all activities carried out under their accounts.' }}
            </p>
        </section>

        <section>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm font-bold">4</span>
                <span>{{ app()->getLocale() === 'ar' ? 'حقوق الملكية الفكرية' : 'Intellectual Property' }}</span>
            </h2>
            <p class="text-sm sm:text-base">
                {{ app()->getLocale() === 'ar' ? 'جميع حقوق الملكية الفكرية والعلامات التجارية لـ Novixa Aqar والتصاميم والأنظمة البرمجية مملوكة بالكامل لشركة Novixa والمالك AK01REDWAN.' : 'All intellectual property, software architecture, branding, and assets are the exclusive property of Novixa & AK01REDWAN.' }}
            </p>
        </section>
    </div>
</div>
@endsection
