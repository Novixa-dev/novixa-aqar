<?php

return [

    'accepted' => 'يجب قبول :attribute.',

    'accepted_if' => 'يجب قبول :attribute عندما يكون :other هو :value.',

    'active_url' => ':attribute ليس رابطاً صالحاً.',

    'after' => 'يجب أن يكون :attribute تاريخاً بعد :date.',

    'after_or_equal' => 'يجب أن يكون :attribute تاريخاً بعد أو يساوي :date.',

    'alpha' => 'يجب أن يحتوي :attribute على أحرف فقط.',

    'alpha_dash' => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',

    'alpha_num' => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط.',

    'array' => 'يجب أن يكون :attribute مصفوفة.',

    'before' => 'يجب أن يكون :attribute تاريخاً قبل :date.',

    'before_or_equal' => 'يجب أن يكون :attribute تاريخاً قبل أو يساوي :date.',

    'between' => ['numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.', 'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على عدد أحرف بين :min و :max.', 'array' => 'يجب أن يحتوي :attribute على عدد عناصر بين :min و :max.'],

    'boolean' => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',

    'confirmed' => 'تأكيد :attribute غير متطابق.',

    'current_password' => 'كلمة المرور الحالية غير صحيحة.',

    'date' => ':attribute ليس تاريخاً صالحاً.',

    'date_equals' => 'يجب أن يكون :attribute تاريخاً مساوياً لـ :date.',

    'date_format' => ':attribute لا يطابق الصيغة :format.',

    'decimal' => 'يجب أن يحتوي :attribute على :decimal خانات عشرية.',

    'declined' => 'يجب رفض :attribute.',

    'declined_if' => 'يجب رفض :attribute عندما يكون :other هو :value.',

    'different' => 'يجب أن يكون :attribute و :other مختلفين.',

    'digits' => 'يجب أن يحتوي :attribute على :digits أرقام.',

    'digits_between' => 'يجب أن يحتوي :attribute على عدد أرقام بين :min و :max.',

    'dimensions' => 'أبعاد الصورة :attribute غير صالحة.',

    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',

    'doesnt_end_with' => 'يجب ألا ينتهي :attribute بأحد القيم التالية: :values.',

    'doesnt_start_with' => 'يجب ألا يبدأ :attribute بأحد القيم التالية: :values.',

    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالح.',

    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',

    'enum' => 'القيمة المحددة لـ :attribute غير صالحة.',

    'exists' => 'القيمة المحددة لـ :attribute غير موجودة.',

    'file' => 'يجب أن يكون :attribute ملفاً.',

    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',

    'gt' => ['numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.', 'file' => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على أكثر من :value أحرف.', 'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر.'],

    'gte' => ['numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.', 'file' => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على :value أحرف أو أكثر.', 'array' => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.'],

    'image' => 'يجب أن يكون :attribute صورة.',

    'in' => 'القيمة المحددة لـ :attribute غير صالحة.',

    'in_array' => 'حقل :attribute غير موجود في :other.',

    'integer' => 'يجب أن يكون :attribute عدداً صحيحاً.',

    'ip' => 'يجب أن يكون :attribute عنوان IP صالحاً.',

    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صالحاً.',

    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صالحاً.',

    'json' => 'يجب أن يكون :attribute نص JSON صالحاً.',

    'lowercase' => 'يجب أن يكون :attribute بأحرف صغيرة.',

    'lt' => ['numeric' => 'يجب أن تكون قيمة :attribute أقل من :value.', 'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على أقل من :value أحرف.', 'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر.'],

    'lte' => ['numeric' => 'يجب أن تكون قيمة :attribute أقل من أو تساوي :value.', 'file' => 'يجب أن يكون حجم :attribute أقل من أو يساوي :value كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على :value أحرف أو أقل.', 'array' => 'يجب ألا يحتوي :attribute على أكثر من :value عناصر.'],

    'max' => ['numeric' => 'يجب ألا تكون قيمة :attribute أكبر من :max.', 'file' => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت.', 'string' => 'يجب ألا يتجاوز عدد أحرف :attribute :max أحرف.', 'array' => 'يجب ألا يحتوي :attribute على أكثر من :max عناصر.'],

    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',

    'mimetypes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',

    'min' => ['numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.', 'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على :min أحرف على الأقل.', 'array' => 'يجب أن يحتوي :attribute على الأقل على :min عناصر.'],

    'multiple_of' => 'يجب أن تكون قيمة :attribute مضاعفاً لـ :value.',

    'not_in' => 'القيمة المحددة لـ :attribute غير صالحة.',

    'not_regex' => 'صيغة :attribute غير صالحة.',

    'numeric' => 'يجب أن يكون :attribute رقماً.',

    'password' => ['letters' => 'يجب أن تحتوي :attribute على حرف واحد على الأقل.', 'mixed' => 'يجب أن تحتوي :attribute على حرف كبير وحرف صغير على الأقل.', 'numbers' => 'يجب أن تحتوي :attribute على رقم واحد على الأقل.', 'symbols' => 'يجب أن تحتوي :attribute على رمز واحد على الأقل.', 'uncompromised' => 'القيمة المحددة لـ :attribute ظهرت في تسريب بيانات. يرجى اختيار قيمة أخرى.'],

    'present' => 'يجب تقديم حقل :attribute.',

    'prohibited' => 'حقل :attribute محظور.',

    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',

    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other في :values.',

    'prohibits' => 'حقل :attribute يمنع وجود :other.',

    'regex' => 'صيغة :attribute غير صالحة.',

    'required' => 'حقل :attribute مطلوب.',

    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',

    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',

    'required_if_accepted' => 'حقل :attribute مطلوب عند قبول :other.',

    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other في :values.',

    'required_with' => 'حقل :attribute مطلوب عند وجود :values.',

    'required_with_all' => 'حقل :attribute مطلوب عند وجود :values.',

    'required_without' => 'حقل :attribute مطلوب عند عدم وجود :values.',

    'required_without_all' => 'حقل :attribute مطلوب عند عدم وجود أي من :values.',

    'same' => 'يجب أن يتطابق :attribute مع :other.',

    'size' => ['numeric' => 'يجب أن تكون قيمة :attribute :size.', 'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.', 'string' => 'يجب أن يحتوي :attribute على :size أحرف.', 'array' => 'يجب أن يحتوي :attribute على :size عناصر.'],

    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',

    'string' => 'يجب أن يكون :attribute نصاً.',

    'timezone' => 'يجب أن يكون :attribute نطاقاً زمنياً صالحاً.',

    'unique' => 'قيمة :attribute مستخدمة من قبل.',

    'uploaded' => 'فشل في تحميل :attribute.',

    'uppercase' => 'يجب أن يكون :attribute بأحرف كبيرة.',

    'url' => 'يجب أن يكون :attribute رابطاً صالحاً.',

    'ulid' => 'يجب أن يكون :attribute ULID صالحاً.',

    'uuid' => 'يجب أن يكون :attribute UUID صالحاً.',

    'attributes' => [
        // Livewire component properties (camelCase names reach the validator as-is).
        'annualExpenses' => 'المصاريف السنوية',
        'annualRentalIncome' => 'الدخل الإيجاري السنوي',
        'areaSqft' => 'المساحة',
        'buyerType' => 'نوع المشتري',
        'customDescription' => 'الوصف المخصص',
        'descriptionTone' => 'أسلوب الوصف',
        'endDate' => 'تاريخ الانتهاء',
        'input' => 'الرسالة',
        'interestRate' => 'نسبة الفائدة',
        'invoiceId' => 'الفاتورة',
        'isFirstTimeBuyer' => 'مشترٍ لأول مرة',
        'loanAmount' => 'مبلغ التمويل',
        'loanTerm' => 'مدة التمويل',
        'max_price' => 'أعلى سعر',
        'min_price' => 'أدنى سعر',
        'movingDistance' => 'مسافة الانتقال',
        'neighborhood_id' => 'الحي',
        'newDate' => 'التاريخ الجديد',
        'newTime' => 'الوقت الجديد',
        'notes' => 'الملاحظات',
        'owner_phone' => 'هاتف المالك',
        'paymentMethod' => 'طريقة الدفع',
        'propertyAddress' => 'عنوان العقار',
        'propertyPrice' => 'سعر العقار',
        'propertyType' => 'نوع العقار',
        'propertyValue' => 'قيمة العقار',
        'purchasePrice' => 'سعر الشراء',
        'rentalPropertyValue' => 'قيمة العقار المؤجر',
        'reportType' => 'نوع التقرير',
        'scenarios.*.annualExpenses' => 'المصاريف السنوية',
        'scenarios.*.annualRentalIncome' => 'الدخل الإيجاري السنوي',
        'scenarios.*.appreciationRate' => 'نسبة ارتفاع القيمة',
        'scenarios.*.holdingPeriod' => 'مدة الاحتفاظ',
        'scenarios.*.purchasePrice' => 'سعر الشراء',
        'selectedAgent' => 'الوكيل',
        'selectedAppointmentType' => 'نوع الموعد',
        'selectedDate' => 'التاريخ',
        'selectedMaintenanceRequest' => 'طلب الصيانة',
        'selectedProperties' => 'العقارات',
        'selectedProperty' => 'العقار',
        'selectedTenant' => 'المستأجر',
        'selectedTenants' => 'المستأجرون',
        'selectedTime' => 'الوقت',
        'selectedTimeSlot' => 'الموعد',
        'startDate' => 'تاريخ البدء',
        'tourDate' => 'تاريخ الجولة',
        'tourNotes' => 'ملاحظات الجولة',
        'tourTime' => 'وقت الجولة',
        'userContact' => 'رقم التواصل',
        'userEmail' => 'البريد الإلكتروني',
        'userName' => 'الاسم',
        'valuationBasePrice' => 'السعر الأساسي',
        'valuationBathrooms' => 'دورات المياه',
        'valuationBedrooms' => 'غرف النوم',
        'valuationCondition' => 'حالة العقار',
        'valuationLocation' => 'الموقع',
        'valuationPropertySize' => 'مساحة العقار',
        'valuationPropertyType' => 'نوع العقار',
        'valuationYearBuilt' => 'سنة البناء',
        'video' => 'الفيديو',
        'year_built' => 'سنة البناء',
        'name' => 'الاسم',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'city' => 'المدينة',
        'country' => 'الدولة',
        'address' => 'العنوان',
        'phone' => 'رقم الهاتف',
        'mobile' => 'الجوال',
        'age' => 'العمر',
        'sex' => 'الجنس',
        'gender' => 'النوع',
        'day' => 'اليوم',
        'month' => 'الشهر',
        'year' => 'السنة',
        'hour' => 'ساعة',
        'minute' => 'دقيقة',
        'second' => 'ثانية',
        'title' => 'العنوان',
        'content' => 'المحتوى',
        'description' => 'الوصف',
        'excerpt' => 'الملخص',
        'date' => 'التاريخ',
        'time' => 'الوقت',
        'available' => 'متاح',
        'size' => 'الحجم',
        'price' => 'السعر',
        'property_id' => 'العقار',
        'bedrooms' => 'غرف النوم',
        'bathrooms' => 'دورات المياه',
        'area' => 'المساحة',
        'message' => 'الرسالة',
        'subject' => 'الموضوع',
        'accounting_integration_id' => 'تكامل المحاسبة',
        'agency_name' => 'اسم الوكالة',
        'amount' => 'المبلغ',
        'annual_income' => 'الدخل السنوي',
        'area_sqft' => 'المساحة (قدم²)',
        'attendees' => 'الحضور',
        'body' => 'النص',
        'channels' => 'القنوات',
        'charge_date' => 'تاريخ الخصم',
        'comment' => 'تعليق',
        'completed' => 'مكتمل',
        'current_password' => 'كلمة المرور الحالية',
        'decision' => 'القرار',
        'document_type' => 'نوع المستند',
        'employment_status' => 'حالة التوظيف',
        'end_date' => 'تاريخ الانتهاء',
        'file' => 'الملف',
        'invoice_id' => 'رقم الفاتورة',
        'issued_at' => 'تاريخ الإصدار',
        'lease_end_date' => 'تاريخ انتهاء الإيجار',
        'lease_start_date' => 'تاريخ بداية الإيجار',
        'link_type' => 'نوع الربط',
        'linkable_id' => 'معرّف العنصر المرتبط',
        'linkable_type' => 'نوع العنصر المرتبط',
        'location' => 'الموقع',
        'media' => 'الوسائط',
        'monthly_rent' => 'الإيجار الشهري',
        'notice_expires_at' => 'انتهاء مهلة الإشعار',
        'notice_served_at' => 'تاريخ تقديم الإشعار',
        'notice_type' => 'نوع الإشعار',
        'operating_countries' => 'دول التشغيل',
        'primary_country' => 'الدولة الرئيسية',
        'property_type' => 'نوع العقار',
        'rating' => 'التقييم',
        'recipient_id' => 'المستلم',
        'recipients' => 'المستلمون',
        'recurrence.frequency' => 'تكرار الدورية',
        'related_contact_id' => 'جهة الاتصال ذات الصلة',
        'relationship' => 'صلة القرابة/العلاقة',
        'report_type' => 'نوع التقرير',
        'role' => 'الدور',
        'scheduled_at' => 'الموعد المحدد',
        'search' => 'البحث',
        'session_id' => 'معرّف الجلسة',
        'signature_data' => 'بيانات التوقيع',
        'staff_id' => 'موظف العمليات',
        'stage' => 'المرحلة',
        'start_date' => 'تاريخ البدء',
        'status' => 'الحالة',
        'structured_address.city' => 'المدينة',
        'structured_address.line_1' => 'العنوان (سطر 1)',
        'structured_address.postal_code' => 'الرمز البريدي',
        'team_id' => 'الفريق',
        'tenant_id' => 'المستأجر',
        'token' => 'الرمز',
        'type' => 'النوع',
        'update_type' => 'نوع التحديث',
        'verified' => 'موثق',
        'address.city' => 'المدينة',
        'address.line_1' => 'العنوان (سطر 1)',
        'address.postal_code' => 'الرمز البريدي',
        'employer_reference.status' => 'حالة مرجع جهة العمل',
        'landlord_reference.status' => 'حالة مرجع المالك السابق',
    ],

];
