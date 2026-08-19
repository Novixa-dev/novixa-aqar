<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The currency assumed when a record has none set, and the currency
    | pre-selected in forms (property listings, offers, etc).
    |
    */

    'default' => 'YER',

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    |
    | Every currency a user can actually select in the application. Each
    | entry defines how the currency is displayed — its symbol, name in
    | both site languages, decimal precision, and symbol position — so
    | formatting logic never needs to hardcode a currency's presentation.
    |
    | This is a fictional real-estate demo: no live exchange-rate feed is
    | wired up, and none should be assumed. Prices are stored per-record
    | in whichever of these currencies the listing was created in; the
    | application does not convert between them.
    |
    */

    'supported' => [
        'YER' => [
            'symbol' => 'ر.ي',
            'name' => 'Yemeni Rial',
            'name_ar' => 'ريال يمني',
            'decimals' => 0,
            'symbol_position' => 'after',
            'thousands_separator' => ',',
        ],
        'SAR' => [
            'symbol' => 'ر.س',
            'name' => 'Saudi Riyal',
            'name_ar' => 'ريال سعودي',
            'decimals' => 2,
            'symbol_position' => 'after',
            'thousands_separator' => ',',
        ],
        'USD' => [
            'symbol' => '$',
            'name' => 'US Dollar',
            'name_ar' => 'دولار أمريكي',
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_separator' => ',',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Legacy Currencies
    |--------------------------------------------------------------------------
    |
    | Currencies that may still exist on old records (e.g. columns that
    | defaulted to GBP before this configuration existed) but are not
    | offered as a choice anywhere in the UI and are not considered
    | "supported" for validation purposes. Kept only so historical data
    | can still be displayed correctly rather than mis-rendered.
    |
    */

    'legacy' => [
        'GBP' => [
            'symbol' => '£',
            'name' => 'British Pound',
            'name_ar' => 'جنيه إسترليني',
            'decimals' => 2,
            'symbol_position' => 'before',
            'thousands_separator' => ',',
        ],
    ],

];
