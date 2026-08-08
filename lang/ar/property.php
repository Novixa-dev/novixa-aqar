<?php

return [
    'types' => [
        'apartment' => 'شقة',
        'villa' => 'فيلا',
        'land' => 'أرض',
        'building' => 'عمارة',
        'commercial_shop' => 'محل تجاري',
        'office' => 'مكتب',
        'rest_house' => 'استراحة',
        'duplex' => 'دوبلكس',
        'penthouse' => 'بنتهاوس',
        'warehouse' => 'مستودع',
    ],

    'listing_types' => [
        'sale' => 'للبيع',
        'rent' => 'للإيجار',
        'annual_rent' => 'إيجار سنوي',
        'monthly_rent' => 'إيجار شهري',
    ],

    'statuses' => [
        'available' => 'متاح',
        'pending' => 'قيد المراجعة',
        'sold' => 'تم البيع',
        'rented' => 'تم التأجير',
        'reserved' => 'محجوز',
        'draft' => 'مسودة',
        'rejected' => 'مرفوض',
    ],

    'attributes' => [
        'title' => 'عنوان العقار',
        'description' => 'وصف العقار',
        'price' => 'السعر',
        'area' => 'المساحة (م²)',
        'bedrooms' => 'غرف النوم',
        'bathrooms' => 'دورات المياه',
        'floors' => 'عدد الأدوار',
        'parking' => 'مواقف السيارات',
        'furnished' => 'حالة التأثيث',
        'city' => 'المدينة',
        'district' => 'الحي / المنطقة',
        'address' => 'العنوان التفصيلي',
        'location' => 'الموقع على الخريطة',
    ],

    'furnished_options' => [
        'yes' => 'مفروش بالكامل',
        'semi' => 'شبه مفروش',
        'no' => 'غير مفروش',
    ],

    'actions' => [
        'submit_property' => 'أضف عقارك الآن',
        'request_viewing' => 'طلب معاينة العقار',
        'contact_agent' => 'التواصل مع الوكيل',
        'whatsapp_inquiry' => 'استفسار عبر الواتساب',
        'share' => 'مشاركة العقار',
        'copy_link' => 'نسخ رابط العقار',
    ],
];
