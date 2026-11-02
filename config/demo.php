<?php

return [

    /*
    | Public demo mode. When on, the login page lists the seeded personas from
    | App\Support\DemoAccounts so a visitor can sign in as any role with one
    | click, and those accounts' email and password become read-only so one
    | visitor cannot lock the others out. Off by default: only the demo
    | sites' app.env turn it on, never a client installation.
    */
    'quick_login' => (bool) env('DEMO_QUICK_LOGIN', false),

];
