<div class="container mx-auto px-4 lg:px-8 py-12" x-data="{ calculatorType: 'mortgage' }">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">

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
                x-bind:class="calculatorType === 'mortgage' ? 'bg-white dark:bg-gray-700 text-[#8C7869] dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ __('Mortgage & Financing Calculator') }}
        </button>
        <button x-on:click="calculatorType = 'costOfMoving'" 
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all" 
                x-bind:class="calculatorType === 'costOfMoving' ? 'bg-white dark:bg-gray-700 text-[#8C7869] dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ app()->getLocale() === 'ar' ? 'تكاليف الانتقال' : 'Cost of Moving' }}
        </button>
        <button x-on:click="calculatorType = 'stampDuty'" 
                class="flex-1 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all" 
                x-bind:class="calculatorType === 'stampDuty' ? 'bg-white dark:bg-gray-700 text-[#8C7869] dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900'">
            {{ app()->getLocale() === 'ar' ? 'رسوم التسجيل' : 'Registration Fees' }}
        </button>
    </div>

    <!-- Mortgage Calculator Card -->
    <div x-show="calculatorType === 'mortgage'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span class="text-emerald-600 dark:text-emerald-400"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg></span>
            <span>{{ __('Mortgage & Financing Calculator') }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="propertyPrice">
                    {{ app()->getLocale() === 'ar' ? 'سعر العقار الإجمالي (ر.ي)' : 'Property Price (YER)' }}
                </label>
                <input wire:model="propertyPrice" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" id="propertyPrice" type="number" placeholder="300000">
                @error('propertyPrice') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="loanAmount">
                    {{ __('Loan Amount') }} ({{ app()->getLocale() === 'ar' ? 'ر.ي' : 'YER' }})
                </label>
                <input wire:model="loanAmount" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" id="loanAmount" type="number" placeholder="240000">
                @error('loanAmount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="interestRate">
                    {{ __('Interest Rate') }} (%)
                </label>
                <input wire:model="interestRate" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" id="interestRate" type="number" step="0.01" placeholder="4.5">
                @error('interestRate') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2" for="loanTerm">
                    {{ __('Loan Term (Years)') }}
                </label>
                <input wire:model="loanTerm" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" id="loanTerm" type="number" placeholder="25">
                @error('loanTerm') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateMortgage" class="px-8 py-3 bg-[#8C7869] hover:bg-[#7A6759] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($mortgageResult)
            <div class="mt-8 p-6 bg-[#8C7869]/10 rounded-2xl border border-[#8C7869]/20">
                <h3 class="text-sm font-bold uppercase tracking-wider text-[#8C7869] dark:text-[#8C7869] mb-4">{{ app()->getLocale() === 'ar' ? 'النتائج التقديرية:' : 'Estimated Results:' }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Monthly Payment') }}</span>
                        <span class="text-xl font-black text-[#8C7869]">{{ app(\App\Services\CurrencyService::class)->format($mortgageResult['monthly_payment'] ?? 0) }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Total Payment') }}</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">{{ app(\App\Services\CurrencyService::class)->format($mortgageResult['total_payment'] ?? 0) }}</span>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-xs">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ __('Total Interest') }}</span>
                        <span class="text-xl font-black text-gray-900 dark:text-white">{{ app(\App\Services\CurrencyService::class)->format($mortgageResult['total_interest'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Cost of Moving Card -->
    <div x-show="calculatorType === 'costOfMoving'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8" style="display: none;">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span class="text-[#8C7869]"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg></span>
            <span>{{ app()->getLocale() === 'ar' ? 'حاسبة تكاليف الانتقال والخدمات' : 'Cost of Moving Calculator' }}</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'قيمة العقار (ر.ي)' : 'Property Value (YER)' }}
                </label>
                <input wire:model="propertyValue" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="250000">
                @error('propertyValue') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'مسافة النقل (كم)' : 'Moving Distance (km)' }}
                </label>
                <input wire:model="movingDistance" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="15">
                @error('movingDistance') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'مشتري لأول مرة؟' : 'First Time Buyer?' }}
                </label>
                <select wire:model="isFirstTimeBuyer" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الإجابة' : 'Select option' }}</option>
                    <option value="1">{{ app()->getLocale() === 'ar' ? 'نعم' : 'Yes' }}</option>
                    <option value="0">{{ app()->getLocale() === 'ar' ? 'لا' : 'No' }}</option>
                </select>
                @error('isFirstTimeBuyer') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateCostOfMoving" class="px-8 py-3 bg-[#8C7869] hover:bg-[#7A6759] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($costOfMovingResult)
            <div class="mt-8 p-6 bg-[#8C7869]/10 rounded-2xl border border-[#8C7869]/20">
                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'إجمالي تكاليف الانتقال التقديرية:' : 'Total Estimated Moving Cost:' }}</span>
                <span class="text-3xl font-black text-[#8C7869]">{{ app(\App\Services\CurrencyService::class)->format($costOfMovingResult['total_cost'] ?? 0, 2) }}</span>
            </div>
        @endif
    </div>

    <!-- Stamp Duty Card -->
    <div x-show="calculatorType === 'stampDuty'" class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 rounded-3xl p-6 sm:p-10 mb-8" style="display: none;">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <span class="text-[#8C7869]"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg></span>
            <span>{{ app()->getLocale() === 'ar' ? 'حاسبة رسوم التسجيل ونقل الملكية' : 'Registration & Transfer Fee Calculator' }}</span>
        </h2>
        <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                    {{ app()->getLocale() === 'ar' ? 'سعر شراء العقار' : 'Purchase Price' }}
                </label>
                <input wire:model="purchasePrice" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#8C7869] focus:outline-none text-sm text-gray-900 dark:text-white" type="number" placeholder="25000000">
                @error('purchasePrice') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <button wire:click="calculateStampDuty" class="px-8 py-3 bg-[#8C7869] hover:bg-[#7A6759] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all text-sm" type="button">
                {{ __('Calculate') }}
            </button>
        </div>

        @if($stampDutyResult)
            <div class="mt-8 p-6 bg-[#8C7869]/10 rounded-2xl border border-[#8C7869]/20">
                <span class="text-xs text-gray-500 dark:text-gray-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'إجمالي الرسوم التقديرية:' : 'Estimated Total Fees:' }}</span>
                <span class="text-3xl font-black text-[#8C7869]">{{ app(\App\Services\CurrencyService::class)->format($stampDutyResult['total_tax'] ?? 0, 'YER') }}</span>
            </div>
        @endif
    </div>
</div>