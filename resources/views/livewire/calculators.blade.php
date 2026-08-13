<div class="container mx-auto px-4 lg:px-8 py-12" x-data="{ calculatorType: 'mortgage' }">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 text-xs font-bold uppercase tracking-wider mb-4 border border-blue-100 dark:border-blue-800">
            {{ __('Calculators') }}
        </span>
        <h1 class="text-3xl sm:text-5xl font-black text-gray-900 dark:text-white tracking-tight mb-4">
            {{ app()->getLocale() === 'ar' ? 'الحاسبات والحلول المالية العقارية' : 'Real Estate Financial Calculators' }}
        </h1>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed font-light">
            {{ app()->getLocale() === 'ar' ? 'احسب أقساط التمويل العقاري، تكاليف الشراء، والرسوم التقديرية بدقة وسهولة.' : 'Calculate your estimated mortgage payments, moving costs, and purchasing budgets with ease.' }}
        </p>
    </div>

    <!-- Tab Buttons -->
    <div class="flex flex-wrap items-center justify-center gap-2 mb-10 max-w-2xl mx-auto p-1.5 bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
        <button x-on:click="calculatorType = 'mortgage'" 
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all" 
                x-bind:class="calculatorType === 'mortgage' ? 'bg-white dark:bg-gray-700 text-blue-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ __('Mortgage & Financing Calculator') }}
        </button>
        <button x-on:click="calculatorType = 'costOfMoving'" 
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all" 
                x-bind:class="calculatorType === 'costOfMoving' ? 'bg-white dark:bg-gray-700 text-blue-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ app()->getLocale() === 'ar' ? 'تكاليف الانتقال' : 'Cost of Moving' }}
        </button>
        <button x-on:click="calculatorType = 'stampDuty'" 
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all" 
                x-bind:class="calculatorType === 'stampDuty' ? 'bg-white dark:bg-gray-700 text-blue-600 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ app()->getLocale() === 'ar' ? 'رسوم التسجيل' : 'Registration Fees' }}
        </button>
    </div>

    <!-- Mortgage Calculator Card -->
    <div x-show="calculatorType === 'mortgage'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span>💰</span>
            <span>{{ __('Mortgage & Financing Calculator') }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="propertyPrice">
                    {{ app()->getLocale() === 'ar' ? 'سعر العقار الإجمالي ($)' : 'Property Price ($)' }}
                </label>
                <input wire:model="propertyPrice" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" id="propertyPrice" type="number" placeholder="300000">
                @error('propertyPrice') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="loanAmount">
                    {{ __('Loan Amount') }} ($)
                </label>
                <input wire:model="loanAmount" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" id="loanAmount" type="number" placeholder="240000">
                @error('loanAmount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="interestRate">
                    {{ __('Interest Rate') }} (%)
                </label>
                <input wire:model="interestRate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" id="interestRate" type="number" step="0.01" placeholder="4.5">
                @error('interestRate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="loanTerm">
                    {{ __('Loan Term (Years)') }}
                </label>
                <input wire:model="loanTerm" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" id="loanTerm" type="number" placeholder="25">
                @error('loanTerm') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateMortgage" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($mortgageResult)
            <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-100 dark:border-blue-800">
                <h3 class="text-sm font-bold uppercase tracking-wider text-blue-900 dark:text-blue-200 mb-4">{{ app()->getLocale() === 'ar' ? 'النتائج التقديرية:' : 'Estimated Results:' }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Monthly Payment') }}</span>
                        <span class="text-xl font-black text-blue-600 dark:text-blue-400">${{ number_format($mortgageResult['monthly_payment'] ?? 0, 2) }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Total Payment') }}</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">${{ number_format($mortgageResult['total_payment'] ?? 0, 2) }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Total Interest') }}</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">${{ number_format($mortgageResult['total_interest'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Cost of Moving Card -->
    <div x-show="calculatorType === 'costOfMoving'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8" style="display: none;">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span>🚚</span>
            <span>{{ app()->getLocale() === 'ar' ? 'حاسبة تكاليف الانتقال والخدمات' : 'Cost of Moving Calculator' }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'قيمة العقار ($)' : 'Property Value ($)' }}
                </label>
                <input wire:model="propertyValue" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="250000">
                @error('propertyValue') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'مسافة النقل (كم)' : 'Moving Distance (km)' }}
                </label>
                <input wire:model="movingDistance" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="15">
                @error('movingDistance') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'مشتري لأول مرة؟' : 'First Time Buyer?' }}
                </label>
                <select wire:model="isFirstTimeBuyer" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الإجابة' : 'Select option' }}</option>
                    <option value="1">{{ app()->getLocale() === 'ar' ? 'نعم' : 'Yes' }}</option>
                    <option value="0">{{ app()->getLocale() === 'ar' ? 'لا' : 'No' }}</option>
                </select>
                @error('isFirstTimeBuyer') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateCostOfMoving" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($costOfMovingResult)
            <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-100 dark:border-blue-800">
                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'إجمالي تكاليف الانتقال التقديرية:' : 'Total Estimated Moving Cost:' }}</span>
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400">${{ number_format($costOfMovingResult['total_cost'] ?? 0, 2) }}</span>
            </div>
        @endif
    </div>

    <!-- Stamp Duty Card -->
    <div x-show="calculatorType === 'stampDuty'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8" style="display: none;">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span>📑</span>
            <span>{{ app()->getLocale() === 'ar' ? 'حاسبة رسوم التسجيل ونقل الملكية' : 'Registration & Stamp Duty Calculator' }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'سعر شراء العقار ($)' : 'Purchase Price ($)' }}
                </label>
                <input wire:model="purchasePrice" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="250000">
                @error('purchasePrice') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'نوع المشتري' : 'Buyer Type' }}
                </label>
                <select wire:model="buyerType" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-600 focus:outline-none text-sm text-gray-900 dark:text-white">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر نوع المشتري' : 'Select buyer type' }}</option>
                    <option value="first_time_buyer">{{ app()->getLocale() === 'ar' ? 'مشتري للمرة الأولى' : 'First Time Buyer' }}</option>
                    <option value="home_mover">{{ app()->getLocale() === 'ar' ? 'ناقل سكن (عقار رئيسي)' : 'Home Mover' }}</option>
                    <option value="additional_property">{{ app()->getLocale() === 'ar' ? 'عقار إضافي / استثمار' : 'Additional Property / Investment' }}</option>
                </select>
                @error('buyerType') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateStampDuty" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($stampDutyResult)
            <div class="mt-8 p-6 bg-blue-50 dark:bg-blue-900/30 rounded-2xl border border-blue-100 dark:border-blue-800">
                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'رسوم التسجيل التقديرية:' : 'Estimated Registration / Stamp Duty:' }}</span>
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400">${{ number_format($stampDutyResult['tax_amount'] ?? 0, 2) }}</span>
            </div>
        @endif
    </div>
</div>