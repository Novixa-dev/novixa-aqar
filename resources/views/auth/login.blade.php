@extends('layouts.app')

@section('content')
    <div class="min-h-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mb-4 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Log in' }}</h1>


            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="email">
                        {{ __('Email') }}
                    </label>
                    <input class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>

                <div class="mt-4">
                    <label class="block font-medium text-sm text-gray-700 dark:text-gray-300" for="password">
                        {{ __('Password') }}
                    </label>
                    <input class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" id="password" type="password" name="password" required autocomplete="current-password">
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-800 focus:ring-opacity-50" id="remember_me" name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-800 dark:bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 dark:hover:bg-green-600 active:bg-green-900 dark:active:bg-green-800 focus:outline-none focus:border-green-900 focus:ring ring-green-300 dark:ring-green-800 disabled:opacity-25 transition ease-in-out duration-150 ms-4">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>

            @if (config('demo.quick_login'))
                {{--
                    Public demo only (DEMO_QUICK_LOGIN). Each button fills the
                    normal form and submits it, so quick login goes through the
                    same Fortify route, throttling and role redirect as typing
                    the credentials by hand.
                --}}
                <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-5">
                    <button type="button" id="demo-accounts-toggle" aria-expanded="false" aria-controls="demo-accounts"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-md border border-green-700 text-green-800 dark:text-green-300 dark:border-green-500 font-semibold text-sm hover:bg-green-50 dark:hover:bg-gray-700 focus:outline-none focus:ring ring-green-300 dark:ring-green-800 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        {{ __('Sign in with a demo account') }}
                    </button>

                    <div id="demo-accounts" hidden class="mt-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ __('Choose a role to explore its dashboard. All data is fictional.') }}</p>
                        <ul class="grid gap-2">
                            @foreach (\App\Support\DemoAccounts::all() as $role => $account)
                                <li>
                                    <button type="button" data-demo-email="{{ $account['email'] }}"
                                        class="demo-login w-full flex items-center justify-between gap-3 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-start hover:border-green-600 hover:bg-green-50 dark:hover:bg-gray-700 focus:outline-none focus:ring ring-green-300 dark:ring-green-800 transition">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">{{ app()->getLocale() === 'ar' ? $account['label_ar'] : $account['label_en'] }}</span>
                                            <span class="block text-xs text-gray-600 dark:text-gray-400 truncate" dir="ltr">{{ $account['email'] }}</span>
                                        </span>
                                        <span class="shrink-0 text-xs font-semibold text-green-800 dark:text-green-300">{{ __('Log in') }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <script>
                    (function () {
                        var toggle = document.getElementById('demo-accounts-toggle');
                        var list = document.getElementById('demo-accounts');
                        var form = document.querySelector('form[action="{{ route('login') }}"]');
                        if (!toggle || !list || !form) return;
                        toggle.addEventListener('click', function () {
                            var open = list.hidden;
                            list.hidden = !open;
                            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                        });
                        list.querySelectorAll('.demo-login').forEach(function (button) {
                            button.addEventListener('click', function () {
                                form.querySelector('#email').value = button.dataset.demoEmail;
                                form.querySelector('#password').value = @json(\App\Support\DemoAccounts::PASSWORD);
                                list.querySelectorAll('.demo-login').forEach(function (b) { b.disabled = true; });
                                form.submit();
                            });
                        });
                    })();
                </script>
            @endif

            {{--
                Socialstream::show() only reflects whether the package feature
                is enabled, not whether any OAuth provider is actually
                configured (config('socialstream.providers') is empty in this
                deployment). Gating on show() alone rendered an "Or Login Via"
                divider with nothing underneath it — <x-socialstream />'s own
                internal !empty(providers()) check correctly renders no
                buttons, leaving a dangling, untranslated-into-Arabic label
                pointing at nothing. Require both.
            --}}
            @if (\JoelButcher\Socialstream\Socialstream::show() && ! empty(\JoelButcher\Socialstream\Socialstream::providers()))
                <div class="mt-4">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'أو سجّل الدخول عبر' : config('socialstream.prompt', 'Or Login Via') }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-socialstream />
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
