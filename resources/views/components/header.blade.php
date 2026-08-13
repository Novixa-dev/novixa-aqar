<div class="bg-gray-800">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Top">
        <div class="w-full py-6 flex items-center justify-between border-b border-gray-500 lg:border-none">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2 rtl:space-x-reverse">
                    <img class="h-10 w-auto rounded" src="{{ asset('images/logo.png') }}" alt="Novixa Aqar | نوفيكسا عقار">
                    <span class="text-xl font-bold text-white tracking-wide">نوفيكسا عقار <span class="text-sm font-normal text-gray-300">| Novixa Aqar</span></span>
                </a>
                <div class="hidden ml-10 space-x-8 lg:block">
                    <a href="/properties" class="text-base font-medium text-white hover:text-gray-300">العقارات</a>
                    <a href="/calculators" class="text-base font-medium text-white hover:text-gray-300">الحاسبات</a>
                    <a href="/about" class="text-base font-medium text-white hover:text-gray-300">عن الشركة</a>
                    <a href="/services" class="text-base font-medium text-white hover:text-gray-300">الخدمات</a>
                    <a href="/contact" class="text-base font-medium text-white hover:text-gray-300">اتصل بنا</a>
                </div>
            </div>
            <div class="ml-10 space-x-4 flex items-center">
                @auth
                    <a href="/admin" class="inline-block bg-indigo-600 py-2 px-4 border border-transparent rounded-md text-base font-medium text-white hover:bg-indigo-700">لوحة التحكم</a>
                @else
                    <a href="/login" class="inline-block bg-gray-600 py-2 px-4 border border-transparent rounded-md text-base font-medium text-white hover:bg-gray-700">تسجيل الدخول</a>
                    <a href="/register" class="inline-block bg-blue-600 py-2 px-4 border border-transparent rounded-md text-base font-medium text-white hover:bg-blue-700">حساب جديد</a>
                @endauth
            </div>
        </div>
        <div class="py-4 flex flex-wrap justify-center space-x-6 lg:hidden">
            <a href="/properties" class="text-base font-medium text-white hover:text-gray-300">العقارات</a>
            <a href="/calculators" class="text-base font-medium text-white hover:text-gray-300">الحاسبات</a>
            <a href="/about" class="text-base font-medium text-white hover:text-gray-300">عن الشركة</a>
            <a href="/services" class="text-base font-medium text-white hover:text-gray-300">الخدمات</a>
            <a href="/contact" class="text-base font-medium text-white hover:text-gray-300">اتصل بنا</a>
        </div>
    </nav>
</div>
