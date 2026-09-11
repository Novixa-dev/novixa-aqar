<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1')
    ],
    /*
     * Text generation used by the AI property-description writer and the AI
     * assistant.
     *
     * Both services already read config('services.openai.api_key') -- this
     * key simply never existed, so they received null, sent an empty bearer
     * token, and threw on the 401 that came back. Defining it is what makes
     * them configurable at all.
     *
     * Leaving api_key empty is a supported state, not a broken one: both
     * services fall back to local generation when no key is present, so the
     * demo works without an AI subscription.
     */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_uri' => env('OPENAI_BASE_URI', 'https://api.openai.com/v1'),
        'chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
        // Kept short on purpose: this runs inside a request on a single
        // Octane worker, so a slow provider must not hold the worker open.
        'timeout' => (int) env('OPENAI_TIMEOUT', 20),
    ],

    'rightmove' => [
        'base_uri' => env('RIGHTMOVE_BASE_URI'),
        'api_key' => env('RIGHTMOVE_API_KEY'),
    ],

    'onthemarket' => [
        'base_uri' => env('ONTHEMARKET_BASE_URI'),
        'api_key' => env('ONTHEMARKET_API_KEY'),
        'sync_frequency' => env('ONTHEMARKET_SYNC_FREQUENCY', 'hourly'),
    ],


    'stripe' => [
        'secret_key' => config('stripe.secret_key'),
        'publishable_key' => config('stripe.publishable_key'),
    ],

    'digital_signature' => [
        'api_key' => env('DIGITAL_SIGNATURE_API_KEY'),
        'endpoint' => env('DIGITAL_SIGNATURE_ENDPOINT'),
        // Add any other necessary configuration options here
    ],

    'docusign' => [
        'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
        'integration_key' => env('DOCUSIGN_INTEGRATION_KEY'),
        'secret_key' => env('DOCUSIGN_SECRET_KEY'),
        'base_path' => env('DOCUSIGN_BASE_PATH', 'https://demo.docusign.net/restapi'),
        'user_id' => env('DOCUSIGN_USER_ID'),
        'private_key_path' => env('DOCUSIGN_PRIVATE_KEY_PATH'),
    ],

    'crm' => [
        'api_key' => env('CRM_API_KEY'),
        'endpoint' => env('CRM_ENDPOINT'),
        'sync_interval' => env('CRM_SYNC_INTERVAL', 15), // in minutes
    ],

    'jupix' => [
        'api_key' => env('JUPIX_API_KEY'),
        'base_url' => env('JUPIX_BASE_URL'),
    ],

    'lets_safe' => [
        'api_key' => env('LETS_SAFE_API_KEY'),
        'api_url' => env('LETS_SAFE_API_URL', 'https://api.letssafe.com/v1'),
    ],

    'sage_online' => [
        'api_key' => env('SAGE_ONLINE_API_KEY'),
        'endpoint' => env('SAGE_ONLINE_ENDPOINT'),
    ],

    'xero' => [
        'api_key' => env('XERO_API_KEY'),
        'endpoint' => env('XERO_ENDPOINT'),
    ],

    'walkscore' => [
        'api_key' => env('WALKSCORE_API_KEY'),
        'base_uri' => env('WALKSCORE_BASE_URI', 'https://api.walkscore.com'),
    ],

    'neighborhood_data' => [
        'base_uri' => env('NEIGHBORHOOD_DATA_BASE_URI', 'https://api.example.com'),
        'api_key' => env('NEIGHBORHOOD_DATA_API_KEY'),
    ],

    'zoopla' => [
        'base_uri' => env('ZOOPLA_BASE_URI'),
        'api_key' => env('ZOOPLA_API_KEY'),
        'sync_frequency' => env('ZOOPLA_SYNC_FREQUENCY', 'daily'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrations that had no configuration entry at all
    |--------------------------------------------------------------------------
    |
    | Every key below is read somewhere in app/ but was never defined here,
    | so config() returned null and each service quietly started up
    | misconfigured -- then failed at its first real call, far from the cause.
    |
    | Declaring them changes no behaviour on its own: they are still empty
    | until someone supplies credentials. What it buys is that the absence is
    | now visible in one place, `php artisan config:show services` tells the
    | truth, and a service can check whether it is configured instead of
    | discovering it from a 401.
    |
    | Most of these are United Kingdom market services (Plaid, QuickBooks,
    | Locrating, Royal Mail postcodes, AllAgents) carried over from the
    | upstream codebase. They are not part of the Yemeni product and are
    | expected to stay empty here.
    |
    */

    'background_check' => [
        'api_key' => env('BACKGROUND_CHECK_API_KEY'),
        'api_url' => env('BACKGROUND_CHECK_API_URL'),
    ],

    'credit_report' => [
        'api_key' => env('CREDIT_REPORT_API_KEY'),
        'api_url' => env('CREDIT_REPORT_API_URL'),
    ],

    'insurance' => [
        'api_key' => env('INSURANCE_API_KEY'),
        'endpoint' => env('INSURANCE_ENDPOINT'),
    ],

    'postcodes' => [
        'api_key' => env('POSTCODES_API_KEY'),
        'endpoint' => env('POSTCODES_ENDPOINT'),
    ],

    'locrating' => [
        'api_key' => env('LOCRATING_API_KEY'),
    ],

    'allagents' => [
        'api_key' => env('ALLAGENTS_API_KEY'),
    ],

    'plaid' => [
        'client_id' => env('PLAID_CLIENT_ID'),
        'secret' => env('PLAID_SECRET'),
        'env' => env('PLAID_ENV', 'sandbox'),
    ],

    'quickbooks' => [
        'api_key' => env('QUICKBOOKS_API_KEY'),
        'endpoint' => env('QUICKBOOKS_ENDPOINT'),
    ],

    'accounting' => [
        // Which accounting backend AccountingIntegrationService should use.
        // Null means none is wired, which is the correct state here.
        'system' => env('ACCOUNTING_SYSTEM'),
    ],

    'contact_inbox' => env('CONTACT_INBOX_EMAIL', env('MAIL_FROM_ADDRESS', 'info@darhadhramaut.ye')),
];