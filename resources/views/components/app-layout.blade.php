{{--
    Bridges the stock Jetstream `<x-app-layout>` convention (still used by
    profile/show, teams/create, teams/show, tenant/applications,
    tenant/payment-portal, custom-reports/index, api/index) onto this
    project's actual shared shell, `layouts.app`. The `app-layout` component
    never existed in this codebase, so every page using it fatally errored
    with "Unable to locate a class or view for component [app-layout]" —
    this is the root cause of the reported public-site profile-page error.
--}}
@extends('layouts.app')

@section('content')
    @isset($header)
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </div>
    @endisset

    <div class="py-8">
        {{ $slot }}
    </div>
@endsection
