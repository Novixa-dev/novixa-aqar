<?php

// Arabic for keys Filament's own Arabic pack does not translate yet.
// Laravel merges these over the package translations, so only the
// missing keys live here; drop any that a Filament upgrade adds upstream.

return [
    'callout' => [
        'statuses' => [
            'danger' => 'خطأ:',
            'info' => 'ملاحظة:',
            'success' => 'تم بنجاح:',
            'warning' => 'تنبيه:',
        ],
    ],
    'section' => [
        'actions' => [
            'collapse' => [
                'label' => 'طي القسم',
            ],
            'expand' => [
                'label' => 'توسيع القسم',
            ],
        ],
    ],
    'wizard' => [
        'header' => [
            'step' => [
                'statuses' => [
                    'completed' => 'مكتمل',
                    'upcoming' => 'غير مكتمل',
                ],
            ],
        ],
    ],
];
