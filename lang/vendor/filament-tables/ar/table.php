<?php

// Arabic for keys Filament's own Arabic pack does not translate yet.
// Laravel merges these over the package translations, so only the
// missing keys live here; drop any that a Filament upgrade adds upstream.

return [
    'column_manager' => [
        'actions' => [
            'reorder' => [
                'label' => 'إعادة ترتيب العمود',
            ],
        ],
    ],
    'columns' => [
        'icon' => [
            'boolean' => [
                'true' => 'نعم',
                'false' => 'لا',
            ],
        ],
    ],
    'actions' => [
        'reorder_record' => [
            'label' => 'إعادة ترتيب العنصر :key',
        ],
        'toggle_record_content' => [
            'label' => 'توسيع/طي العنصر :key',
        ],
    ],
    'loading' => 'جارٍ التحميل...',
    'result_count' => '{0} لا توجد نتائج|{1} نتيجة واحدة|[2,10] :count نتائج|[11,*] :count نتيجة',
];
