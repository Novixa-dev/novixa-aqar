<?php

namespace Database\Seeders;

use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PropertySeeder extends Seeder
{
    /** The seeded demo agent that properties should be attributed to, rather than the admin account. */
    private ?int $agentUserId = null;

    /**
     * Matches DemoWorkflowSeeder's own hardcoded team id (the single demo
     * agency team created by DefaultTeamSeeder). Without this, every
     * property created here had a NULL team_id — PropertyPolicy's
     * ownership check (direct user_id match OR shared team_id) then
     * silently blocked staff/agent from editing the vast majority of the
     * seeded inventory (measured: staff could edit only 3 of 36 properties
     * before this fix, all of which happened to be team-scoped by
     * coincidence via the live Filament create form during testing).
     */
    private int $teamId = 1;

    public function run()
    {
        $faker = Faker::create('ar_SA');
        $this->agentUserId = User::role('agent')->first()?->id ?? User::where('email', 'admin@darhadhramaut.ye')->first()?->id;
        $salesCategory = PropertyCategory::where('name', 'sales')->first();
        $lettingsCategory = PropertyCategory::where('name', 'lettings')->first();
        $hmoCategory = PropertyCategory::where('name', 'hmo')->first();

        $this->createArabicProperties($salesCategory, 12, 'sales');
        $this->createArabicProperties($lettingsCategory, 12, 'lettings');

        if ($hmoCategory) {
            $this->createHmoProperties($hmoCategory, 4);
        }

        $this->attachGalleryPhotos();
    }

    /**
     * Attach the bundled demo gallery photos (database/seeders/media/*.jpg)
     * to every property so listings render with real imagery. Photos cycle
     * deterministically; each property gets 2–3 shots.
     */
    private function attachGalleryPhotos(): void
    {
        $photos = glob(database_path('seeders/media/prop-*.jpg'));

        if (empty($photos)) {
            return;
        }

        sort($photos);

        Property::query()->chunkById(10, function ($properties) use ($photos) {
            static $offset = 0;

            foreach ($properties as $property) {
                if ($property->getMedia('images')->isNotEmpty()) {
                    continue;
                }

                // Deterministic window into the photo pool so re-seeding is stable.
                $count = ($property->id % 2 === 0) ? 3 : 2;

                for ($j = 0; $j < $count; $j++) {
                    $photo = $photos[($offset + $j) % count($photos)];

                    $property
                        ->addMedia($photo)
                        ->preservingOriginal()
                        ->usingName($property->title)
                        ->usingFileName("property-{$property->id}-".($j + 1).'.jpg')
                        ->toMediaCollection('images');
                }

                $offset += $count;
            }
        });
    }

    /**
     * Realistic per-listing currency: most Al-Mukalla listings are priced in
     * YER, but investment-oriented commercial listings are plausibly marketed
     * in USD or SAR to attract regional/diaspora investors. Returns
     * [currency, price] using a price range sensible for that currency —
     * never derived from a converted YER figure.
     */
    private function priceFor(string $type, int $index, $faker): array
    {
        if ($type === 'sales' && $index === 6) { // عمارة استثمارية (investment building)
            return ['USD', $faker->numberBetween(150000, 400000)];
        }
        if ($type === 'sales' && $index === 7) { // أرض تجارية (commercial land, two main streets)
            return ['SAR', $faker->numberBetween(400000, 1200000)];
        }
        if ($type === 'lettings' && $index === 8) { // شقة مفروشة للإيجار السنوي (furnished annual rental)
            return ['USD', $faker->numberBetween(300, 800)];
        }

        return $type === 'sales'
            ? ['YER', $faker->numberBetween(2500000, 95000000)]
            : ['YER', $faker->numberBetween(250000, 1500000)];
    }

    private function createArabicProperties($category, $count, $type)
    {
        $faker = Faker::create();
        $neighborhoods = Neighborhood::all();

        $arabicTitles = [
            'شقة فاخرة للبيع إطلالة بحرية مباشرة حي السلام',
            'فيلا حديثة دورين حوش واسع حي فوه المساكن',
            'أرض سكنية مستوية جاهزة للبناء حي الشرج',
            'شقة عائلية 4 غرف واسعة حي الديس بالمكلا',
            'محل تجاري مميز موقع حيوي حي الديس',
            'فيلا راقية مع كراج وحمام سباحة حي خلف',
            'عمارة استثمارية 4 أدوار موقع استراتيجي بالمكلا',
            'أرض تجارية على شارعين رئيسيين حي 40 شقة',
            'شقة مفروشة للإيجار السنوي حي فوه',
            'مكتب تجاري للشركات والمؤسسات حي الشرج',
            'بيت شعبي ممرمم بالكامل حي الشهيد',
            'شقة تمليك 3 غرف نوم حي ربوة الخلفة',
        ];

        $arabicDescriptions = [
            'شقة فاخرة بمشطيبات سوبر لوكس تتكون من 3 غرف نوم ومجلس واسع وحمامين ومطبخ مجهز بالكامل. قريبة من المساجد والمدارس والمراكز التجارية.',
            'فيلا عصرية تصميم معماري مميز تتكون من دورين وحوش مبلط يتسع لسيارتين، خزان أرضي وعلوي مستقل، تقع في منطقة هادئة وراقية بالمكلا.',
            'أرض سكنية ممتازة بمساحة واسعة وشوارع مبلطة، توفر كافات الخدمات العامة (كهرباء، مياه، شبكة اتصالات).',
            'محل تجاري جاهز للتشغيل فوراً في موقع حاد ومكتظ بالسكان، يصلح لكافة الأنشطة التجارية والمحلات الاستثمارية.',
        ];

        $propertyTypes = ['Apartment', 'Villa', 'Land', 'Commercial', 'Building'];

        for ($i = 0; $i < $count; $i++) {
            $title = $arabicTitles[$i % count($arabicTitles)];
            $desc = $arabicDescriptions[$i % count($arabicDescriptions)];
            $neighborhood = $neighborhoods->count() > 0 ? $neighborhoods->get($i % $neighborhoods->count()) : null;
            [$currency, $price] = $this->priceFor($type, $i, $faker);

            Property::create([
                'title' => $title,
                'description' => $desc,
                'location' => 'المكلا - ' . ($neighborhood ? $neighborhood->name : 'حضرموت'),
                'price' => $price,
                'currency' => $currency,
                'bedrooms' => $faker->numberBetween(1, 6),
                'bathrooms' => $faker->numberBetween(1, 4),
                'area_sqft' => $faker->numberBetween(120, 600),
                'year_built' => $faker->numberBetween(2015, 2026),
                'property_type' => $propertyTypes[$i % count($propertyTypes)],
                'status' => $type === 'sales' ? 'For Sale' : 'For Rent',
                'is_featured' => $i % 3 === 0,
                'list_date' => now()->subDays($i * 3),
                'user_id' => $this->agentUserId,
                'agent_id' => $this->agentUserId,
                'team_id' => $this->teamId,
                'property_category_id' => $category?->id,
                'latitude' => 14.5385 + ($i * 0.002),
                'longitude' => 49.1235 + ($i * 0.002),
                'postal_code' => '00967',
                'country' => 'Yemen',
                'neighborhood_id' => $neighborhood?->id,
                'virtual_tour_url' => $i % 4 === 0 ? 'https://kuula.co/share/collection/7l2nJ' : null,
                'virtual_tour_provider' => $i % 4 === 0 ? 'kuula' : null,
                'live_tour_available' => $i % 4 === 0,
            ]);
        }
    }

    private function createHmoProperties($category, $count)
    {
        $faker = Faker::create();
        $neighborhoods = Neighborhood::all();

        for ($i = 0; $i < $count; $i++) {
            $neighborhood = $neighborhoods->count() > 0 ? $neighborhoods->random() : null;
            $property = Property::create([
                'title' => 'مجمع غرف مفروشة للطلاب والشباب ' . ($i + 1),
                'description' => 'مجمع غرف سكنية مجهزة بتكييف وخدمات مشتركة وانترنت مجاني بالقرب من كليات جامعة حضرموت.',
                'location' => 'المكلا - فوه',
                'price' => $faker->numberBetween(80000, 200000),
                'currency' => 'YER',
                'bedrooms' => 6,
                'bathrooms' => 3,
                'area_sqft' => 250,
                'year_built' => 2022,
                'property_type' => 'HMO',
                'status' => 'For Rent',
                'is_featured' => false,
                'list_date' => now(),
                'user_id' => $this->agentUserId,
                'agent_id' => $this->agentUserId,
                'team_id' => $this->teamId,
                'property_category_id' => $category->id,
                'latitude' => 14.5385,
                'longitude' => 49.1235,
                'country' => 'Yemen',
                'neighborhood_id' => $neighborhood?->id,
            ]);

            for ($j = 0; $j < $property->bedrooms; $j++) {
                $property->rooms()->create([
                    'room_number' => 'غرفة ' . ($j + 1),
                    'size' => 25,
                    'rent' => $faker->numberBetween(40000, 80000),
                    'is_available' => true,
                ]);
            }
        }
    }
}
