<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Lead;
use App\Models\Neighborhood;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadAndBuyerSeeder extends Seeder
{
    public function run()
    {
        $user = User::first() ?? User::factory()->create();
        $team = Team::first() ?? Team::factory()->create();
        $neighborhood = Neighborhood::first();

        $demoLeads = [
            [
                'name' => 'عمر عبد الله باحشوان',
                'email' => 'omar.bahashwan@example.com',
                'phone' => '+967 777 123 456',
                'message' => 'أود الاستفسار عن الشقة المعروضة للبيع في حي السلام بالمكلا وتحديد موعد للمعاينة.',
                'status' => 'new',
                'category' => 'hot',
                'score' => 85,
                'team_id' => $team->id,
            ],
            [
                'name' => 'خالد سعيد بن ماضي',
                'email' => 'khaled.madi@example.com',
                'phone' => '+967 770 987 654',
                'message' => 'يبحث عن أرض سكنية بمساحة لا تقل عن 400 متر في حي فوه المساكن.',
                'status' => 'contacted',
                'category' => 'warm',
                'score' => 65,
                'team_id' => $team->id,
            ],
            [
                'name' => 'فهد بن الشيخ أبو بكر',
                'email' => 'fahad.abubakr@example.com',
                'phone' => '+967 733 456 789',
                'message' => 'راغب في استئجار محل تجاري بحي الشرج للأنشطة التجارية.',
                'status' => 'qualified',
                'category' => 'hot',
                'score' => 90,
                'team_id' => $team->id,
            ],
        ];

        foreach ($demoLeads as $leadData) {
            Lead::firstOrCreate(['email' => $leadData['email']], $leadData);
        }

        $demoBuyers = [
            [
                'name' => 'سالم أحمد العمودي',
                'phone' => '+967 771 111 222',
                'email' => 'salem.alamoudi@example.com',
                'status' => 'active',
                'user_id' => $user->id,
                'team_id' => $team->id,
                'search_criteria' => [
                    'property_type' => 'villa',
                    'neighborhood_id' => $neighborhood?->id,
                    'min_price' => 50000000,
                    'max_price' => 120000000,
                    'bedrooms' => 4,
                ],
            ],
            [
                'name' => 'حسن حسين باوزير',
                'phone' => '+967 772 333 444',
                'email' => 'hassan.bawazir@example.com',
                'status' => 'active',
                'user_id' => $user->id,
                'team_id' => $team->id,
                'search_criteria' => [
                    'property_type' => 'apartment',
                    'neighborhood_id' => $neighborhood?->id,
                    'min_price' => 15000000,
                    'max_price' => 35000000,
                    'bedrooms' => 3,
                ],
            ],
        ];

        foreach ($demoBuyers as $buyerData) {
            Buyer::firstOrCreate(['email' => $buyerData['email']], $buyerData);
        }
    }
}
