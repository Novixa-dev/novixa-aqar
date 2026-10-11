<?php

namespace Database\Seeders;

use App\Support\DemoAccounts;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\LeaseAgreement;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentalApplication;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

/**
 * Seeds the end-to-end demo workflow data (leases, payments, maintenance,
 * applications, viewings) that the role dashboards surface. Demo-only:
 * like UserSeeder, this must never run against a production database.
 */
class DemoWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->warn('DemoWorkflowSeeder skipped: production environment.');

            return;
        }

        $teamId = 1;
        $landlord = User::where('email', DemoAccounts::email('landlord'))->first();
        $agent = User::where('email', DemoAccounts::email('agent'))->first();
        $staff = User::where('email', DemoAccounts::email('staff'))->first();
        $tenantUser = User::where('email', DemoAccounts::email('tenant'))->first();
        $buyerUser = User::where('email', DemoAccounts::email('buyer'))->first();

        // Give the landlord direct ownership of several rentals so their
        // panel shows inventory and ownership-scoped resources have rows.
        if ($landlord) {
            Property::where('status', Property::STATUS_FOR_RENT)
                ->orderBy('id')
                ->take(5)
                ->update(['user_id' => $landlord->id]);
        }

        // Give the seller persona a portfolio of sale listings so their
        // dashboard and ownership-scoped resources are not empty in the demo.
        $seller = User::where('email', DemoAccounts::email('seller'))->first();
        if ($seller) {
            Property::where('status', Property::STATUS_FOR_SALE)
                ->orderBy('id')
                ->take(6)
                ->update(['user_id' => $seller->id]);
        }
        $rentals = Property::where('user_id', $landlord?->id)->orderBy('id')->get();
        $fallbackRentals = $rentals->isEmpty()
            ? Property::where('status', Property::STATUS_FOR_RENT)->orderBy('id')->get()
            : $rentals;

        // A handful of listings that have actually closed, so agent-
        // performance "Properties Sold"/"Properties Rented" stats aren't
        // permanently zero against fresh seed data (previously every
        // seeded property stayed for_sale/for_rent forever). Picked from
        // the agent's own inventory, distinct from the landlord/seller
        // portfolios just assigned above.
        Property::where('status', Property::STATUS_FOR_SALE)
            ->where('user_id', '!=', $seller?->id)
            ->orderBy('id')
            ->take(4)
            ->update(['status' => Property::STATUS_SOLD, 'sold_date' => now()->subDays(10)]);

        Property::where('status', Property::STATUS_FOR_RENT)
            ->where('user_id', '!=', $landlord?->id)
            ->orderBy('id')
            ->take(3)
            ->update(['status' => Property::STATUS_RENTED]);

        // A small, honest moderation queue: most seeded listings are
        // already live/approved, but a couple sit "pending" and one
        // "rejected" so the Staff "Property Moderation" resource shows a
        // real, active workflow rather than a uniform blank column.
        Property::orderBy('id')->take(2)->update(['moderation_status' => 'approved']);
        Property::orderBy('id')->skip(2)->take(2)->update(['moderation_status' => 'pending']);
        Property::orderBy('id')->skip(4)->take(1)->update(['moderation_status' => 'rejected', 'moderation_notes' => 'الصور المرفقة غير واضحة، يرجى إعادة رفع صور أحدث للعقار قبل النشر.']);
        Property::whereNull('moderation_status')->update(['moderation_status' => 'approved', 'moderated_at' => now()->subDays(5)]);

        // --- Transactions, for the just-sold properties above. Without
        // these, `transactions` was permanently empty against fresh seed
        // data — every revenue chart/stat/"Recent Transactions" widget on
        // the Staff dashboard rendered a blank chart or "0.00", which reads
        // as a broken/lifeless demo rather than an active agency. ---
        if ($buyerUser && $seller) {
            Property::where('status', Property::STATUS_SOLD)
                ->orderBy('id')
                ->take(4)
                ->get()
                ->each(function (Property $prop) use ($buyerUser, $seller, $teamId) {
                    Transaction::updateOrCreate(
                        ['property_id' => $prop->id, 'transaction_type' => 'sale'],
                        [
                            'buyer_id' => $buyerUser->id,
                            'seller_id' => $seller->id,
                            'date' => $prop->sold_date ?? now()->subDays(10),
                            'amount' => $prop->price,
                            'status' => Transaction::STATUS_COMPLETED,
                            'completed_at' => $prop->sold_date ?? now()->subDays(10),
                            'commission_amount' => round($prop->price * 0.025, 2),
                            'team_id' => $teamId,
                        ]
                    );
                });
        }

        // --- Vendors (contractors) ---
        $vendorsData = [
            [
                'company_name' => 'شركة البناء الحديث للمقاولات',
                'contact_person' => 'م. سالم باعباد',
                'email' => 'albina.alhadith@novixa.dev',
                'phone' => '+967 5 382 4471',
                'city' => 'المكلا',
                'vendor_type' => 'contractor',
                'specializations' => ['سباكة', 'كهرباء', 'بناء'],
                'rating' => 4.7,
                'preferred_vendor' => true,
                'hourly_rate' => 8500,
            ],
            [
                'company_name' => 'مؤسسة الخليج لصيانة العقارات',
                'contact_person' => 'أ. فهد البحر',
                'email' => 'gulf.maintenance@novixa.dev',
                'phone' => '+967 5 341 9082',
                'city' => 'المكلا',
                'vendor_type' => 'maintenance',
                'specializations' => ['تكييف', 'نجارة', 'دهان'],
                'rating' => 4.3,
                'preferred_vendor' => true,
                'hourly_rate' => 6000,
            ],
            [
                'company_name' => 'ورشة اليمن للتبريد والتكييف',
                'contact_person' => 'م. أنيس الصنعاني',
                'email' => 'yemen.ac@novixa.dev',
                'phone' => '+967 1 244 5560',
                'city' => 'صنعاء',
                'vendor_type' => 'hvac',
                'specializations' => ['تكييف مركزي', 'تبريد'],
                'rating' => 4.1,
                'preferred_vendor' => false,
                'hourly_rate' => 7500,
            ],
        ];
        $vendors = [];
        foreach ($vendorsData as $v) {
            $vendors[] = Vendor::updateOrCreate(
                ['company_name' => $v['company_name']],
                $v + [
                    'country' => 'اليمن',
                    'status' => 'active',
                    'payment_terms' => 'net_30',
                    'team_id' => $teamId,
                    'added_by' => User::where('email', DemoAccounts::email('super_admin'))->value('id') ?? 1,
                ]
            );
        }

        // Link the seeded contractor persona to the first vendor company.
        $contractorUserId = User::where('email', DemoAccounts::email('contractor'))->first();

        // The contractor panel scopes work orders by the contractors-table
        // record carrying the persona's email.
        $contractorProfile = null;
        if ($contractorUserId) {
            $contractorProfile = \App\Models\Contractor::updateOrCreate(
                ['email' => $contractorUserId->email],
                [
                    'name' => 'شركة البناء الحديث للمقاولات',
                    'phone' => '+967 5 382 4471',
                    'specialization' => 'سباكة، كهرباء، بناء',
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Tenants ---
        $tenantSeeds = [
            ['name' => 'أحمد صالح باوزير', 'email' => 'ahmed.bawazir@novixa.dev', 'phone' => '+967 771 234 567', 'user_id' => $tenantUser?->id],
            ['name' => 'منى عبدالله بكري', 'email' => 'mona.bakri@novixa.dev', 'phone' => '+967 773 555 142', 'user_id' => null],
            ['name' => 'خالد عمر السقاف', 'email' => 'khaled.saqqaf@novixa.dev', 'phone' => '+967 775 808 930', 'user_id' => null],
            ['name' => 'فاطمة ناصر العمودي', 'email' => 'fatima.amoudi@novixa.dev', 'phone' => '+967 770 442 918', 'user_id' => null],
            ['name' => 'ياسر محمد باعشن', 'email' => 'yasser.bashan@novixa.dev', 'phone' => '+967 772 663 501', 'user_id' => null],
            ['name' => 'هدى سالم الكثيري', 'email' => 'huda.kathiri@novixa.dev', 'phone' => '+967 774 219 837', 'user_id' => null],
            ['name' => 'عمر عبدالرحمن باجرش', 'email' => 'omar.bajarash@novixa.dev', 'phone' => '+967 776 550 274', 'user_id' => null],
        ];
        $tenants = [];
        foreach ($tenantSeeds as $t) {
            $tenants[] = Tenant::updateOrCreate(
                ['email' => $t['email']],
                $t + ['status' => 'active', 'team_id' => $teamId]
            );
        }

        // --- Leases (tenant_id -> users.id) ---
        $leaseUsers = array_values(array_filter([
            $tenantUser,
            User::where('email', DemoAccounts::email('buyer'))->first(),
        ]));
        $leaseRows = [];
        foreach ($leaseUsers as $i => $u) {
            $prop = $fallbackRentals->get($i);
            if (! $prop || ! $u) {
                continue;
            }
            $start = now()->subMonths(6 - $i * 2)->startOfMonth();
            $leaseRows[] = Lease::updateOrCreate(
                ['property_id' => $prop->id, 'tenant_id' => $u->id],
                [
                    'start_date' => $start->toDateString(),
                    'end_date' => $start->copy()->addYear()->subDay()->toDateString(),
                    'rent_amount' => 120000 + $i * 30000,
                    'security_deposit' => 240000,
                    'status' => $i === 0 ? 'active' : ($i === 1 ? 'active' : 'expired'),
                    'terms' => 'الإيجار سنوي يُدفع على أقساط شهرية، والصيانة الدورية على المالك ما لم يكن العطل بسبب المستأجر.',
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Lease agreements (Staff panel's LeaseAgreementResource; a
        // separate, parallel table from `leases` above — previously never
        // seeded at all, so it always rendered empty next to the populated
        // Leases list in the same panel) ---
        foreach ($leaseRows as $i => $lease) {
            $leaseTenantRecord = Tenant::where('user_id', $lease->tenant_id)->first();
            if (! $leaseTenantRecord) {
                continue;
            }
            LeaseAgreement::updateOrCreate(
                ['property_id' => $lease->property_id, 'tenant_id' => $leaseTenantRecord->id],
                [
                    'landlord_id' => $landlord?->id,
                    'start_date' => $lease->start_date,
                    'end_date' => $lease->end_date,
                    'monthly_rent' => $lease->rent_amount,
                    'rent_amount' => $lease->rent_amount,
                    'security_deposit' => $lease->security_deposit,
                    'status' => $lease->status,
                    'payment_frequency' => 'monthly',
                    'terms_and_conditions' => 'الإيجار سنوي يُدفع على أقساط شهرية، والصيانة الدورية على المالك ما لم يكن العطل بسبب المستأجر.',
                    'is_signed' => $i !== 2,
                    'landlord_signed' => true,
                    'tenant_signed' => $i !== 2,
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Payments (tenant_id -> users.id, tied to lease tenants) ---
        foreach ($leaseRows as $i => $lease) {
            for ($m = 1; $m <= 3; $m++) {
                $date = now()->subMonths($m);
                Payment::updateOrCreate(
                    [
                        'tenant_id' => $lease->tenant_id,
                        'payment_date' => $date->toDateString(),
                        'amount' => $lease->rent_amount,
                    ],
                    [
                        'status' => 'completed',
                        'payment_method' => 'bank_transfer',
                        'team_id' => $teamId,
                    ]
                );
            }
            Payment::updateOrCreate(
                [
                    'tenant_id' => $lease->tenant_id,
                    'payment_date' => now()->startOfMonth()->toDateString(),
                    'amount' => $lease->rent_amount,
                ],
                [
                    'status' => $i === 0 ? 'pending' : 'completed',
                    'payment_method' => 'cash',
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Maintenance requests (tenant_id -> tenants.id, vendor_id) ---
        $maintSeeds = [
            ['title' => 'تسريب مياه في مطبخ الشقة', 'description' => 'تسريب مستمر من مواسير المطبخ مع رطوبة في الجدار المجاور، يلزم إصلاح عاجل.', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'عطل في مكيف الصالة', 'description' => 'المكيف لا يبرد منذ يومين، ويُسمع صوت احتكاك من الوحدة الخارجية.', 'status' => 'pending', 'priority' => 'normal'],
            ['title' => 'استبدال سخان مياه الدورة', 'description' => 'السخان القديم توقف عن العمل تمامًا، ومطلوب تركيب وحدة جديدة بسعة 80 لتر.', 'status' => 'completed', 'priority' => 'normal'],
            ['title' => 'انقطاع الكهرباء في غرفة النوم الرئيسية', 'description' => 'القاطعة تفصل باستمرار عند تشغيل مكيف غرفة النوم الرئيسية.', 'status' => 'pending', 'priority' => 'urgent'],
            ['title' => 'صيانة باب الحديقة الخلفية', 'description' => 'مفصلات باب الحديقة صدئة وتصدر صريراً عالياً، تحتاج تشحيم أو استبدال.', 'status' => 'pending', 'priority' => 'normal'],
            ['title' => 'إصلاح تسريب في خزان المياه العلوي', 'description' => 'ملاحظة تسريب بسيط من قاعدة الخزان العلوي أثناء الامتلاء الكامل.', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'استبدال مصابيح الإنارة الخارجية', 'description' => 'ثلاث مصابيح في واجهة المبنى متوقفة عن العمل منذ أسبوع.', 'status' => 'completed', 'priority' => 'normal'],
            ['title' => 'فحص شبكة الإنترنت الداخلية', 'description' => 'ضعف ملحوظ في تغطية الواي فاي بالطابق العلوي.', 'status' => 'pending', 'priority' => 'normal'],
            ['title' => 'تصليح قفل الباب الرئيسي', 'description' => 'القفل الإلكتروني للباب الرئيسي يتعطل أحياناً عند إدخال الرمز.', 'status' => 'in_progress', 'priority' => 'urgent'],
        ];
        foreach ($maintSeeds as $i => $m) {
            $prop = $fallbackRentals->get($i % max(1, $fallbackRentals->count()));
            if (! $prop || ! isset($tenants[$i % count($tenants)])) {
                continue;
            }
            MaintenanceRequest::updateOrCreate(
                ['title' => $m['title'], 'property_id' => $prop->id],
                $m + [
                    'tenant_id' => $tenants[$i % count($tenants)]->id,
                    'requested_date' => now()->subDays(10 - $i * 2)->toDateString(),
                    'vendor_id' => $vendors[$i % count($vendors)]->id,
                    // Assign the first two jobs to the demo contractor persona.
                    'contractor_id' => $i < 2 && $contractorProfile ? $contractorProfile->id : null,
                    'payment_status' => $m['status'] === 'completed' ? 'paid' : 'unpaid',
                    'completed_at' => $m['status'] === 'completed' ? now()->subDays(2) : null,
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Contacts + rental applications (applicant_id -> contacts.id) ---
        $applicantSeeds = [
            ['first_name' => 'وليد', 'last_name' => 'بن بريك', 'email' => 'walid.benbrik@novixa.dev', 'phones' => ['+967 777 100 200']],
            ['first_name' => 'سماح', 'last_name' => 'الحضرمي', 'email' => 'samah.alhadhrami@novixa.dev', 'phones' => ['+967 778 300 400']],
            ['first_name' => 'طارق', 'last_name' => 'العطاس', 'email' => 'tareq.alattas@novixa.dev', 'phones' => ['+967 779 500 600']],
            ['first_name' => 'ريم', 'last_name' => 'باحميد', 'email' => 'reem.bahamid@novixa.dev', 'phones' => ['+967 771 900 810']],
            ['first_name' => 'فيصل', 'last_name' => 'الجابري', 'email' => 'faisal.jaberi@novixa.dev', 'phones' => ['+967 773 220 660']],
            ['first_name' => 'نجلاء', 'last_name' => 'باوزير', 'email' => 'najla.bawazir@novixa.dev', 'phones' => ['+967 775 440 330']],
        ];
        $applicants = [];
        foreach ($applicantSeeds as $c) {
            $existing = Contact::where('emails', 'like', '%'.$c['email'].'%')->first();
            $applicants[] = $existing ?: Contact::create([
                'type' => 'lead',
                'first_name' => $c['first_name'],
                'last_name' => $c['last_name'],
                'emails' => [$c['email']],
                'phones' => $c['phones'],
                'status' => 'active',
                'preferred_language' => 'ar',
                'team_id' => $teamId,
            ]);
        }
        $statuses = ['pending', 'approved', 'rejected', 'approved', 'pending', 'approved'];
        foreach ($applicants as $i => $contact) {
            $prop = $fallbackRentals->get($i % max(1, $fallbackRentals->count()));
            if (! $prop) {
                continue;
            }
            $decided = $statuses[$i] !== 'pending';
            RentalApplication::updateOrCreate(
                ['property_id' => $prop->id, 'applicant_id' => $contact->id],
                [
                    'status' => $statuses[$i],
                    'employment_status' => 'employed',
                    'monthly_income' => 350000 + $i * 75000,
                    'application_date' => now()->subDays(14 - $i * 3)->toDateString(),
                    'desired_move_in_date' => now()->addDays(15 + $i * 5)->toDateString(),
                    'employer_reference' => 'شركة الحضرموت للتجارة والمقاولات - قسم الموارد البشرية',
                    'background_check_status' => $decided ? 'passed' : 'pending',
                    'submitted_at' => now()->subDays(14 - $i * 3),
                    'decided_at' => $decided ? now()->subDays(2) : null,
                    'decision_notes' => $statuses[$i] === 'approved'
                        ? 'الملف مكتمل والدخل ثابت، تمت الموافقة بعد مراجعة المراجع.'
                        : ($statuses[$i] === 'rejected' ? 'تعارض في موعد الإشغال مع مستأجر حالي.' : null),
                    'team_id' => $teamId,
                ]
            );
        }

        // --- Property viewings / bookings ---
        $bookingStatuses = ['confirmed', 'completed', 'pending', 'confirmed', 'completed', 'pending'];
        foreach ([0, 1, 2, 3, 4, 5] as $i) {
            $prop = Property::orderBy('id')->skip($i * 2)->first();
            if (! $prop || ! $buyerUser || ! $agent) {
                break;
            }
            Booking::updateOrCreate(
                [
                    'property_id' => $prop->id,
                    'user_id' => $buyerUser->id,
                    'date' => now()->addDays($i + 1)->toDateString(),
                ],
                [
                    'time' => '10:00',
                    'staff_id' => $agent->id,
                    'status' => $bookingStatuses[$i],
                    'name' => $buyerUser->name,
                    'email' => $buyerUser->email,
                    'contact' => '+967 771 000 111',
                    'visit_type' => 'in_person',
                    'booking_type' => 'viewing',
                    'notes' => $i === 0 ? 'يفضَّل الموعد صباحًا.' : null,
                    'team_id' => $teamId,
                ]
            );
        }

        $this->command->info('Demo workflow data seeded: '
            .Vendor::count().' vendors, '.Tenant::count().' tenants, '
            .Lease::count().' leases, '.LeaseAgreement::count().' lease agreements, '
            .Payment::count().' payments, '
            .MaintenanceRequest::count().' maintenance requests, '
            .RentalApplication::count().' rental applications, '
            .Booking::count().' bookings, '
            .Transaction::count().' transactions.');
    }
}
