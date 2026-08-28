@extends('layouts.app')

@section('content')
    <div class="min-h-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div class="w-full sm:max-w-md mb-4 mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">

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
