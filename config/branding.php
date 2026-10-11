<?php

/*
| The public brand of this deployment: everything a visitor sees as "who we
| are" — names, tagline, SEO description, contact details, social image.
|
| One place on purpose: the layout, navbar, footer, page titles, Open Graph
| tags, legal pages and seeders all read from here (via App\Support\Brand,
| shared to every view as $brand), so rebranding a deployment is an .env
| change, not a hunt through templates.
*/

return [
    'name' => [
        'ar' => env('BRAND_NAME_AR', 'نوفيكسا عقار'),
        'en' => env('BRAND_NAME_EN', 'Novixa Aqar'),
    ],

    'tagline' => [
        'ar' => env('BRAND_TAGLINE_AR', 'منصة عقارية متكاملة'),
        'en' => env('BRAND_TAGLINE_EN', 'The complete real-estate platform'),
    ],

    // Default meta / Open Graph description when a page sets none.
    'description' => [
        'ar' => env('BRAND_DESCRIPTION_AR', 'نوفيكسا عقار: منصة عقارية متكاملة للبيع والإيجار في اليمن — بحث متقدم، معاينات وحجوزات، إدارة العقود والإيجارات والصيانة، ولوحة خاصة لكل دور.'),
        'en' => env('BRAND_DESCRIPTION_EN', 'Novixa Aqar: a complete real-estate platform for sales and rentals in Yemen — advanced search, viewings and bookings, leases, rent and maintenance, with a dedicated workspace for every role.'),
    ],

    // The company behind the product.
    'company' => [
        'name' => env('BRAND_COMPANY', 'Novixa'),
        'url' => env('BRAND_COMPANY_URL', 'https://novixa.dev'),
    ],

    'address' => [
        'ar' => env('BRAND_ADDRESS_AR', 'المكلا، حضرموت، اليمن'),
        'en' => env('BRAND_ADDRESS_EN', 'Mukalla, Hadhramaut, Yemen'),
    ],

    'email' => env('BRAND_EMAIL', 'info@novixa.dev'),
    'phone' => env('BRAND_PHONE', '+967 776 248 526'),

    // Absolute URL or a path under public/. Used for og:image / twitter:image
    // when a page provides none (1200×630 recommended).
    'og_image' => env('BRAND_OG_IMAGE', 'images/og-default.jpg'),

    // Search engines may index this deployment. Off by default outside
    // production so demo and staging data never ends up in search results.
    'indexable' => (bool) env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
];
