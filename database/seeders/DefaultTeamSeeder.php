<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Branch;
use App\Models\OrganisationProfile;

class DefaultTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::firstOrCreate(
            ['id' => 1],
            [
                'name' => app(\App\Support\Brand::class)->bilingualName(),
                'personal_team' => false,
                'user_id' => 1,
            ]
        );

        Branch::firstOrCreate(
            ['name' => 'الفرع الرئيسي - المكلا', 'team_id' => $team->id],
            [
                'address' => 'شارع الملكة أروى، حي فوه، المكلا، حضرموت، اليمن',
                'phone_number' => '+967 776 248 526',
                'phone' => config('branding.phone'),
                'email' => config('branding.email'),
            ]
        );

        // Structured company identity + regional/currency defaults, used by the
        // country->currency resolution chain (see PropertyController) and any
        // future agency-management features. Yemen is primary; Saudi Arabia is
        // included since some investment listings are SAR-denominated.
        OrganisationProfile::firstOrCreate(
            ['team_id' => $team->id],
            [
                'agency_name' => app(\App\Support\Brand::class)->bilingualName(),
                'email' => config('branding.email'),
                'phone' => config('branding.phone'),
                'address' => [
                    'line_1' => 'شارع الملكة أروى، حي فوه',
                    'city' => 'المكلا',
                    'governorate' => 'حضرموت',
                ],
                'operating_countries' => ['YE', 'SA'],
                'primary_country' => 'YE',
                'currency' => 'YER',
                'locale' => 'ar_YE',
                'language' => 'ar',
                'timezone' => 'Asia/Aden',
                'date_format' => 'd/m/Y',
                'measurement_system' => 'metric',
                'area_unit' => 'sq_m',
                'setup_completed_at' => now(),
            ]
        );
    }
}
