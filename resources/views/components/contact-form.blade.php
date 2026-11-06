<div>
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-2xl bg-green-50 dark:bg-gray-700 dark:text-green-400 border border-green-200 dark:border-green-600 flex items-center gap-3" role="alert">
            <span class="text-emerald-500"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></span>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6" style="--brand-color: {{ $brand->themeColor() }}">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">{{ __('Name') }}:</label>
                <input type="text" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:outline-none [--tw-ring-color:var(--brand-color)] text-sm text-gray-900 dark:text-white" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }}:</label>
                <input type="email" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:outline-none [--tw-ring-color:var(--brand-color)] text-sm text-gray-900 dark:text-white" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">{{ __('Phone Number') }}:</label>
                <input type="tel" class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:outline-none [--tw-ring-color:var(--brand-color)] text-sm text-gray-900 dark:text-white" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="interest" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">{{ app()->getLocale() === 'ar' ? 'نوع الاهتمام' : 'Interest' }}:</label>
                <div class="relative">
                    <select class="w-full ps-4 pe-10 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:outline-none [--tw-ring-color:var(--brand-color)] text-sm text-gray-900 dark:text-white appearance-none cursor-pointer" id="interest" name="interest">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- حدد طبيعة الاستفسار --' : '-- Select your interest --' }}</option>
                        <option value="buying" {{ old('interest') == 'buying' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'شراء عقار' : 'Buying a property' }}</option>
                        <option value="selling" {{ old('interest') == 'selling' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'بيع عقار' : 'Selling a property' }}</option>
                        <option value="renting" {{ old('interest') == 'renting' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'استئجار عقار' : 'Renting a property' }}</option>
                        <option value="other" {{ old('interest') == 'other' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'أخرى / استشارات' : 'Other inquiries' }}</option>
                    </select>
                    <div class="absolute end-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                @error('interest')
                    <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">{{ __('Message') }}:</label>
            <textarea class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:outline-none [--tw-ring-color:var(--brand-color)] text-sm text-gray-900 dark:text-white" id="message" name="message" rows="5" required placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب تفاصيل طلبك أو استفسارك هنا...' : 'Type your detailed inquiry here...' }}">{{ old('message') }}</textarea>
            @error('message')
                <div class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="px-8 py-3.5 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:brightness-90 transition-all text-sm" style="background-color: var(--brand-color)">
            {{ __('Send Message') }}
        </button>
    </form>
</div>
