<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ContactMessage;
use App\Models\EmailCampaign;
use App\Models\EnergyConsumption;
use App\Models\Favorite;
use App\Models\KeyLocation;
use App\Models\Lease;
use App\Models\Message;
use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\PropertyFeature;
use App\Models\PropertyTemplate;
use App\Models\Review;
use App\Models\User;
use App\Models\UtilityPayment;
use Illuminate\Database\Seeder;

/**
 * Fills the panels/pages that DemoWorkflowSeeder and PropertySeeder don't
 * touch at all, which otherwise render as genuinely empty tables/sections
 * in a fresh demo: property amenities, reviews (property + neighborhood),
 * favorites, key locations, property templates, email campaigns, energy
 * consumption/utility payments, the internal message/communication hub,
 * chatbot conversation history, and inbound contact-form messages.
 * Demo-only, like DemoWorkflowSeeder: never runs against production.
 */
class DemoDataCompletionSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->warn('DemoDataCompletionSeeder skipped: production environment.');

            return;
        }

        $teamId = 1;

        $admin = User::where('email', 'admin@darhadhramaut.ye')->first();
        $staff = User::where('email', 'staff@darhadhramaut.ye')->first();
        $agent = User::where('email', 'agent@darhadhramaut.ye')->first();
        $buyer = User::where('email', 'buyer@darhadhramaut.ye')->first();
        $seller = User::where('email', 'seller@darhadhramaut.ye')->first();
        $landlord = User::where('email', 'landlord@darhadhramaut.ye')->first();
        $tenant = User::where('email', 'tenant@darhadhramaut.ye')->first();
        $appUser = User::where('email', 'appuser@darhadhramaut.ye')->first();

        $this->seedPropertyFeatures($teamId);
        $this->seedReviews($buyer, $tenant, $appUser, $landlord, $seller);
        $this->seedFavorites($teamId, $buyer, $tenant);
        $this->seedKeyLocations($teamId);
        $this->seedPropertyTemplates();
        $this->seedEmailCampaigns($teamId, $staff, $admin);
        $this->seedEnergyAndUtilities($teamId);
        $this->seedCommunicationHub($staff, $landlord, $tenant);
        $this->seedChatConversations($buyer, $tenant);
        $this->seedContactMessages();

        $this->command->info('Demo data completion seeded: '
            .PropertyFeature::count().' property features, '
            .Review::count().' reviews, '
            .Favorite::count().' favorites, '
            .KeyLocation::count().' key locations, '
            .PropertyTemplate::count().' property templates, '
            .EmailCampaign::count().' email campaigns, '
            .EnergyConsumption::count().' energy consumption records, '
            .UtilityPayment::count().' utility payments, '
            .Message::count().' internal messages, '
            .ChatConversation::count().' chat conversations, '
            .ContactMessage::count().' contact messages.');
    }

    /** 4-6 realistic Arabic amenities per property, deterministic by id so re-seeding is stable. */
    private function seedPropertyFeatures(int $teamId): void
    {
        $pool = [
            'مصعد', 'موقف سيارات خاص', 'حديقة خارجية', 'مسبح', 'أمن وحراسة 24 ساعة',
            'تكييف مركزي', 'إنترنت عالي السرعة', 'مطبخ مجهز بالكامل', 'شرفة واسعة',
            'نظام إنذار وكاميرات مراقبة', 'خزان مياه علوي وأرضي', 'غرفة خادمة',
            'قريب من المدارس والمساجد', 'مصلى داخلي',
        ];

        Property::all()->each(function (Property $property) use ($pool, $teamId) {
            if ($property->features()->exists()) {
                return;
            }

            $count = 4 + ($property->id % 3);
            $offset = $property->id % count($pool);

            for ($k = 0; $k < $count; $k++) {
                PropertyFeature::firstOrCreate([
                    'property_id' => $property->id,
                    'feature_name' => $pool[($offset + $k) % count($pool)],
                ], ['team_id' => $teamId]);
            }
        });
    }

    /** Property + neighborhood reviews, tied to users who plausibly interacted (booked/leased). */
    private function seedReviews(?User $buyer, ?User $tenant, ?User $appUser, ?User $landlord, ?User $seller): void
    {
        $bookedPropertyIds = $buyer
            ? Booking::where('user_id', $buyer->id)->pluck('property_id')->unique()->values()
            : collect();
        $leasedPropertyIds = $tenant
            ? Lease::where('tenant_id', $tenant->id)->pluck('property_id')->unique()->values()
            : collect();

        $propertyReviews = [
            ['user' => $buyer, 'ids' => $bookedPropertyIds, 'rating' => 5, 'title' => 'تجربة ممتازة من البداية للنهاية', 'comment' => 'العقار مطابق تماماً للصور المعروضة، والموقع قريب من كل الخدمات. تعامل احترافي من فريق نوفيكسا عقار خلال الحجز والمعاينة.'],
            ['user' => $buyer, 'ids' => $bookedPropertyIds->slice(1), 'rating' => 4, 'title' => 'عقار جيد بموقع مميز', 'comment' => 'التشطيبات جيدة والموقع ممتاز، كنا نتمنى مواقف سيارات أوسع قليلاً لكن التجربة العامة مُرضية.'],
            ['user' => $tenant, 'ids' => $leasedPropertyIds, 'rating' => 4, 'title' => 'سكن مريح ومالك متجاون', 'comment' => 'أسكن هنا منذ عدة أشهر، الصيانة سريعة عند الحاجة والتواصل مع الإدارة سهل ومباشر.'],
        ];

        foreach ($propertyReviews as $r) {
            $propertyId = $r['ids']->first();
            if (! $r['user'] || ! $propertyId) {
                continue;
            }
            Review::firstOrCreate([
                'user_id' => $r['user']->id,
                'reviewable_id' => $propertyId,
                'reviewable_type' => Property::class,
            ], [
                'rating' => $r['rating'],
                'title' => $r['title'],
                'comment' => $r['comment'],
                'review_date' => now()->subDays(random_int(5, 60)),
                'approved' => true,
                'moderation_status' => 'approved',
            ]);
        }

        // A couple of generic reviews on properties not already reviewed
        // above, for broader coverage. `appuser@darhadhramaut.ye` (the
        // roleless self-registered persona referenced elsewhere in
        // DemoWorkflowSeeder) does not actually exist in UserSeeder's
        // output -- verified via a fresh seed, zero such row -- so this
        // uses real, always-present personas instead of silently no-oping.
        $genericReviewer = $buyer ?? $appUser;
        if ($genericReviewer) {
            $genericTargets = Property::whereDoesntHave('reviews')->orderBy('id')->take(2)->get();
            $genericCopy = [
                ['rating' => 4, 'title' => 'عقار جيد بشكل عام', 'comment' => 'السعر مناسب مقارنة بمثيلاته في نفس الحي، ويستحق الزيارة قبل اتخاذ القرار.'],
                ['rating' => 3, 'title' => 'يحتاج بعض الصيانة البسيطة', 'comment' => 'الموقع ممتاز، لكن العقار يحتاج بعض أعمال الصيانة البسيطة قبل الانتقال إليه مباشرة.'],
            ];
            foreach ($genericTargets as $i => $property) {
                Review::firstOrCreate([
                    'user_id' => $genericReviewer->id,
                    'reviewable_id' => $property->id,
                    'reviewable_type' => Property::class,
                ], $genericCopy[$i % count($genericCopy)] + [
                    'review_date' => now()->subDays(random_int(2, 20)),
                    'approved' => true,
                    'moderation_status' => 'approved',
                ]);
            }
        }

        // One genuinely pending review, so the moderation queue behind
        // "approved => true" filtering isn't permanently empty either.
        $pendingReviewer = $tenant ?? $appUser;
        $pendingTarget = Property::whereDoesntHave('reviews')->orderBy('id')->first();
        if ($pendingReviewer && $pendingTarget) {
            Review::firstOrCreate([
                'user_id' => $pendingReviewer->id,
                'reviewable_id' => $pendingTarget->id,
                'reviewable_type' => Property::class,
            ], [
                'rating' => 4,
                'title' => 'مراجعة قيد المراجعة',
                'comment' => 'زرت العقار مؤخراً، تقييم تفصيلي قادم بعد التأكد من بعض التفاصيل.',
                'review_date' => now()->subDay(),
                'approved' => false,
                'moderation_status' => 'pending',
            ]);
        }

        // Neighborhood reviews.
        $neighborhoods = Neighborhood::orderBy('id')->take(4)->get();
        $reviewers = array_values(array_filter([$buyer, $tenant, $landlord, $seller]));
        $neighborhoodCopy = [
            ['rating' => 5, 'title' => 'حي هادئ ومناسب للعائلات', 'comment' => 'حي هادئ وآمن، قريب من المدارس والأسواق، ومن أفضل الخيارات لمن يبحث عن الاستقرار.'],
            ['rating' => 4, 'title' => 'حي حيوي بخدمات جيدة', 'comment' => 'حركة تجارية جيدة وخدمات قريبة، يزداد الازدحام قليلاً في المساء لكنه مقبول.'],
            ['rating' => 5, 'title' => 'من أفضل الأحياء في المكلا', 'comment' => 'بنية تحتية ممتازة وقرب من الخدمات الأساسية، أنصح به بشدة.'],
            ['rating' => 3, 'title' => 'جيد بشكل عام مع ملاحظات بسيطة', 'comment' => 'الحي جيد إجمالاً، ويحتاج فقط لتحسين إنارة بعض الشوارع الفرعية ليلاً.'],
        ];

        foreach ($neighborhoods as $i => $neighborhood) {
            $reviewer = $reviewers[$i % max(1, count($reviewers))] ?? null;
            if (! $reviewer) {
                continue;
            }
            $copy = $neighborhoodCopy[$i % count($neighborhoodCopy)];
            Review::firstOrCreate([
                'user_id' => $reviewer->id,
                'reviewable_id' => $neighborhood->id,
                'reviewable_type' => Neighborhood::class,
            ], $copy + [
                'review_date' => now()->subDays(random_int(10, 90)),
                'approved' => true,
                'moderation_status' => 'approved',
            ]);
        }
    }

    private function seedFavorites(int $teamId, ?User $buyer, ?User $tenant): void
    {
        if ($buyer) {
            Property::where('status', Property::STATUS_FOR_SALE)->orderBy('id')->take(4)->get()
                ->each(fn (Property $p) => Favorite::firstOrCreate(
                    ['user_id' => $buyer->id, 'property_id' => $p->id],
                    ['team_id' => $teamId]
                ));
        }
        if ($tenant) {
            Property::where('status', Property::STATUS_FOR_RENT)->orderBy('id')->take(2)->get()
                ->each(fn (Property $p) => Favorite::firstOrCreate(
                    ['user_id' => $tenant->id, 'property_id' => $p->id],
                    ['team_id' => $teamId]
                ));
        }
    }

    private function seedKeyLocations(int $teamId): void
    {
        $locations = [
            ['location_name' => 'خزانة مفاتيح المكتب الرئيسي', 'address' => 'شارع الملكة أروى، حي فوه، المكلا، حضرموت'],
            ['location_name' => 'خزانة مفاتيح فرع الديس', 'address' => 'شارع الستين، حي الديس، المكلا، حضرموت'],
            ['location_name' => 'صندوق أمانات العقارات المعروضة', 'address' => 'المكتب الرئيسي، حي فوه، المكلا، حضرموت'],
        ];
        foreach ($locations as $loc) {
            KeyLocation::firstOrCreate(['location_name' => $loc['location_name']], $loc + ['team_id' => $teamId]);
        }
    }

    private function seedPropertyTemplates(): void
    {
        $templates = [
            [
                'name' => 'قالب وصف عقار سكني',
                'content' => "عقار سكني مميز يقع في {الحي}، يتكون من {عدد الغرف} غرف نوم و{عدد الحمامات} حمامات، بمساحة إجمالية {المساحة} متر مربع.\nيتميز بموقعه القريب من الخدمات الأساسية والمرافق العامة. للاستفسار والمعاينة يرجى التواصل مع فريق المبيعات.",
            ],
            [
                'name' => 'قالب عقد إيجار موجز',
                'content' => "عقد إيجار بين المالك {اسم المالك} والمستأجر {اسم المستأجر} للعقار الكائن في {عنوان العقار}، بقيمة إيجار شهري {قيمة الإيجار} ريال يمني، لمدة سنة تبدأ من {تاريخ البداية}.\nتشمل الشروط: الصيانة الدورية على المالك ما لم يكن العطل بسبب المستأجر، ودفعة تأمين استرجاعية عند انتهاء العقد.",
            ],
            [
                'name' => 'قالب إعلان عقار للبيع',
                'content' => "فرصة استثمارية للبيع: {عنوان العقار} بسعر {السعر}.\nالمواصفات: {عدد الغرف} غرف، {المساحة} متر مربع، {سنة البناء}.\nللتواصل والمعاينة الفورية يرجى الاتصال بالوكيل المسؤول.",
            ],
        ];
        foreach ($templates as $t) {
            PropertyTemplate::firstOrCreate(['name' => $t['name']], $t);
        }
    }

    private function seedEmailCampaigns(int $teamId, ?User $staff, ?User $admin): void
    {
        $createdBy = $staff?->id ?? $admin?->id;

        EmailCampaign::firstOrCreate(['name' => 'نشرة عقارات جديدة - سبتمبر 2026'], [
            'subject' => 'أحدث العقارات المضافة هذا الأسبوع',
            'content' => 'اطلع على أحدث العقارات السكنية والتجارية المضافة حديثاً في المكلا وحضرموت.',
            'team_id' => $teamId,
            'created_by' => $createdBy,
            'audience_filters' => ['segment' => 'all_subscribers'],
            'status' => 'sent',
            'recipients_count' => 120,
            'delivered_count' => 115,
            'opened_count' => 64,
            'clicked_count' => 22,
            'sent_at' => now()->subDays(5),
        ]);

        EmailCampaign::firstOrCreate(['name' => 'تذكير بمواعيد الدفع الشهرية'], [
            'subject' => 'تذكير: موعد استحقاق الإيجار الشهري',
            'content' => 'نذكّر المستأجرين الكرام بموعد استحقاق دفعة الإيجار الشهرية القادمة.',
            'team_id' => $teamId,
            'created_by' => $createdBy,
            'audience_filters' => ['segment' => 'active_tenants'],
            'status' => 'scheduled',
            'scheduled_at' => now()->addDays(3),
        ]);

        EmailCampaign::firstOrCreate(['name' => 'عروض العقارات الصيفية 2026'], [
            'subject' => 'عروض حصرية على مجموعة مختارة من العقارات',
            'content' => 'مسودة حملة تسويقية للعروض الصيفية على العقارات السكنية المختارة.',
            'team_id' => $teamId,
            'created_by' => $createdBy,
            'audience_filters' => ['segment' => 'prospective_buyers'],
            'status' => 'draft',
        ]);
    }

    private function seedEnergyAndUtilities(int $teamId): void
    {
        $properties = Property::where('status', Property::STATUS_RENTED)
            ->orWhere('status', Property::STATUS_FOR_RENT)
            ->orderBy('id')
            ->take(4)
            ->get();

        foreach ($properties as $i => $property) {
            $status = $i % 2 === 0 ? 'paid' : 'due';
            $consumption = EnergyConsumption::firstOrCreate([
                'property_id' => $property->id,
                'consumption_date' => now()->subMonth()->startOfMonth()->toDateString(),
            ], [
                'electricity_usage' => 210.5 + $i * 15,
                'gas_usage' => 12.0 + $i,
                'water_usage' => 18.5 + $i,
                'total_cost' => 45000 + $i * 5000,
                'status' => $status,
                'due_date' => now()->addDays(10),
            ]);

            if ($status === 'paid') {
                UtilityPayment::firstOrCreate([
                    'energy_consumption_id' => $consumption->id,
                    'payment_date' => now()->subDays(3)->toDateString(),
                ], [
                    'amount' => $consumption->total_cost,
                    'payment_method' => 'bank_transfer',
                    'notes' => 'سداد فاتورة الخدمات الشهرية.',
                ]);
            }
        }
    }

    private function seedCommunicationHub(?User $staff, ?User $landlord, ?User $tenant): void
    {
        if ($staff && $tenant) {
            Message::create(['sender_id' => $staff->id, 'recipient_id' => $tenant->id, 'content' => 'مرحباً، نود التأكيد على استلام طلب الصيانة الخاص بكم وسيتم إرسال الفني خلال يومين.', 'read_at' => now()->subDays(2)]);
            Message::create(['sender_id' => $tenant->id, 'recipient_id' => $staff->id, 'content' => 'شكراً لكم، بانتظار الفني في الموعد المحدد.', 'read_at' => null]);
        }
        if ($staff && $landlord) {
            Message::create(['sender_id' => $landlord->id, 'recipient_id' => $staff->id, 'content' => 'هل يوجد تحديث بخصوص تجديد عقد الإيجار المنتهي هذا الشهر؟', 'read_at' => now()->subDay()]);
            Message::create(['sender_id' => $staff->id, 'recipient_id' => $landlord->id, 'content' => 'نعم، المستأجر أبدى رغبته بالتجديد، سنرسل لكم نسخة العقد المحدثة اليوم.', 'read_at' => null]);
        }
    }

    private function seedChatConversations(?User $buyer, ?User $tenant): void
    {
        $conversations = [
            [
                'user' => $buyer,
                'session' => 'demo-chat-session-1',
                'status' => 'closed',
                'messages' => [
                    ['sender_type' => 'user', 'message' => 'مرحباً، هل يتوفر لديكم عقار للبيع في حي فوه؟'],
                    ['sender_type' => 'bot', 'message' => 'أهلاً بك! نعم، تتوفر لدينا عدة عقارات في حي فوه، يمكنك تصفحها من صفحة العقارات أو إخباري بميزانيتك التقريبية لأقترح عليك الأنسب.'],
                    ['sender_type' => 'user', 'message' => 'الميزانية حوالي 30 مليون ريال.'],
                ],
            ],
            [
                'user' => $tenant,
                'session' => 'demo-chat-session-2',
                'status' => 'active',
                'messages' => [
                    ['sender_type' => 'user', 'message' => 'كيف يمكنني جدولة معاينة لعقار؟'],
                    ['sender_type' => 'bot', 'message' => 'يمكنك حجز موعد معاينة مباشرة من صفحة تفاصيل العقار عبر زر "حجز معاينة"، وسيتواصل معك أحد وكلائنا لتأكيد الموعد.'],
                ],
            ],
            [
                'user' => null,
                'session' => 'demo-chat-session-3',
                'status' => 'escalated',
                'escalated_at' => now()->subHours(4),
                'messages' => [
                    ['sender_type' => 'user', 'message' => 'أواجه مشكلة في الدفع الإلكتروني، هل يمكن مساعدتي بشكل عاجل؟'],
                    ['sender_type' => 'bot', 'message' => 'أعتذر عن الإزعاج، سيتم تحويل محادثتك الآن إلى أحد موظفي الدعم البشري لمساعدتك في أقرب وقت.'],
                ],
            ],
        ];

        foreach ($conversations as $c) {
            $conversation = ChatConversation::firstOrCreate(['session_id' => $c['session']], [
                'user_id' => $c['user']?->id,
                'status' => $c['status'],
                'escalated_at' => $c['escalated_at'] ?? null,
            ]);

            if ($conversation->messages()->exists()) {
                continue;
            }

            foreach ($c['messages'] as $m) {
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'message' => $m['message'],
                    'sender_type' => $m['sender_type'],
                    'sender_id' => $m['sender_type'] === 'user' ? $c['user']?->id : null,
                ]);
            }
        }
    }

    private function seedContactMessages(): void
    {
        $messages = [
            ['name' => 'سلطان عبدالله', 'email' => 'sultan.abdullah@example.ye', 'phone' => '+967 770 112 233', 'interest' => 'buying', 'message' => 'أرغب بمعرفة تفاصيل أكثر عن العقارات السكنية المتوفرة حالياً في حي السلام.', 'is_read' => true],
            ['name' => 'إيمان الحبشي', 'email' => 'eman.habashi@example.ye', 'phone' => '+967 772 445 667', 'interest' => 'renting', 'message' => 'هل يوجد شقق مفروشة للإيجار الشهري بالقرب من جامعة حضرموت؟', 'is_read' => true],
            ['name' => 'مراد سالم', 'email' => 'murad.salem@example.ye', 'phone' => '+967 774 889 001', 'interest' => 'selling', 'message' => 'أملك أرضاً سكنية وأرغب بعرضها للبيع من خلالكم، كيف يمكنني البدء؟', 'is_read' => false],
            ['name' => 'أروى ثابت', 'email' => 'arwa.thabet@example.ye', 'phone' => '+967 776 220 998', 'interest' => 'other', 'message' => 'هل تقدمون خدمات تقييم عقاري مستقل؟', 'is_read' => false],
        ];

        foreach ($messages as $m) {
            ContactMessage::firstOrCreate(['email' => $m['email']], $m);
        }
    }
}
