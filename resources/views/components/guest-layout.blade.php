{{--
    Same gap as components/app-layout.blade.php, for the guest/interstitial
    auth pages that use <x-guest-layout> instead: verify-email,
    reset-password, two-factor-challenge, confirm-password all referenced a
    component that was never defined anywhere in this codebase, so every one
    of them fatally errored with "Unable to locate a class or view for
    component [guest-layout]" the moment they rendered. Bridges onto the
    same layouts.app shell login/register/forgot-password already use
    directly — <x-authentication-card> (used inside all four pages' slots)
    already provides its own centered-card chrome, so this only needs to
    supply the outer page shell.
--}}
@extends('layouts.app')

@section('content')
    {{ $slot }}
@endsection
