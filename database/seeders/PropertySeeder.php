<?php

namespace Database\Seeders;

use App\Support\DemoAccounts;
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
        $this->agentUserId = User::role('agent')->first()?->id ?? User::where('email', DemoAccounts::email('super_admin'))->first()?->id;

        // Properties have no natural unique key (nothing stable to
        // firstOrCreate against), so re-running this unconditionally created
        // 28 brand-new duplicate properties on every invocation -- silently
        // multiplying the demo inventory across repeated `db:seed` runs
        // (confirmed live: multiple partial reseed attempts on Railway while
        // iterating on an unrelated storage-permission fix left the property
        // count at multiples of 28). Property creation itself needs to run
        // exactly once; gallery-photo attachment already has its own
        // per-property guard below and must still run every time to backfill
        // any property that doesn't have photos yet.
        if (! Property::exists()) {
            $salesCategory = PropertyCategory::where('name', 'sales')->first();
            $lettingsCategory = PropertyCategory::where('name', 'lettings')->first();
            $hmoCategory = PropertyCategory::where('name', 'hmo')->first();

            $this->createArabicProperties($salesCategory, 'sales');
            $this->createArabicProperties($lettingsCategory, 'lettings');

            if ($hmoCategory) {
                $this->createHmoProperties($hmoCategory, 4);
            }
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

                // Responsive-image generation (Property::images collection registers
                // ->withResponsiveImages()) runs synchronously here (QUEUE_CONNECTION
                // is sync on Railway), and intermittently threw
                // Spatie\MediaLibrary\...\DiskCannotBeAccessed deep inside
                // ResponsiveImageGenerator -- reproduced live: crashed every
                // `migrate:fresh --seed` attempt at this exact call, aborting the
                // seeder (and, under this script's `set -e`, the whole container
                // boot) every time. Chaining ->withResponsiveImagesIf(false) directly
                // on this addMedia() call does NOT work: FileAdder::processMediaItem()
                // unconditionally calls checkGenerateResponsiveImages(), which
                // re-derives the flag from the collection's own registered default
                // and silently overrides any per-call override -- confirmed live,
                // that approach still crashed identically. The only place that
                // actually sticks is the collection registration itself, gated by
                // this instance flag (see Property::$skipResponsiveImagesForSeeding).
                $property->skipResponsiveImagesForSeeding = true;

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
     * The demo inventory: every listing is one coherent record.
     *
     * Titles used to rotate independently of type, status and bedrooms, so
     * the demo showed "شقة عائلية 4 غرف" typed Commercial with 5 bedrooms,
     * shops with bedrooms, land with bathrooms, "…للبيع" titles on rentals
     * and duplicate titles. Each row below is [title, type, district,
     * bedrooms, bathrooms, area m², currency, price, description]; titles
     * carry no "for sale / for rent" wording because the status shows that
     * (and DemoWorkflowSeeder later marks some sold or rented).
     */
    private const SALES = [
        ['شقة فاخرة بإطلالة بحرية — 3 غرف', 'Apartment', 'حي السلام', 3, 2, 165, 'YER', 48000000, 'شقة بتشطيب سوبر لوكس في الدور الخامس بإطلالة مباشرة على البحر: 3 غرف نوم، مجلس واسع، حمامان ومطبخ مجهز. مصعد وموقف سيارة خاص.'],
        ['فيلا حديثة من دورين مع حوش واسع', 'Villa', 'فوه', 5, 4, 380, 'YER', 165000000, 'فيلا بتصميم معماري عصري من دورين، 5 غرف نوم و4 حمامات، حوش مبلط يتسع لسيارتين، خزان أرضي وعلوي مستقل، في حي هادئ قريب من الخدمات.'],
        ['أرض سكنية مستوية جاهزة للبناء', 'Land', 'الشرج', 0, 0, 400, 'YER', 32000000, 'أرض سكنية مستوية بصك ملكية سليم على شارع مسفلت، تتوفر بها خدمات الكهرباء والمياه وشبكة الاتصالات.'],
        ['شقة عائلية واسعة — 4 غرف', 'Apartment', 'الديس', 4, 3, 210, 'YER', 56000000, 'شقة عائلية في عمارة حديثة: 4 غرف نوم، صالة ومجلس منفصلان، 3 حمامات، قريبة من المدارس والمساجد ومجمع 22 مايو.'],
        ['محل تجاري على شارع رئيسي', 'Commercial', 'الديس', 0, 1, 60, 'YER', 38000000, 'محل تجاري جاهز للتشغيل فوراً في موقع حيوي مكتظ بالحركة، واجهة زجاجية وحمام خاص، يصلح لمختلف الأنشطة التجارية.'],
        ['فيلا راقية مع كراج وحديقة', 'Villa', 'خلف', 6, 5, 450, 'YER', 210000000, 'فيلا فاخرة: 6 غرف نوم، 5 حمامات، مجلس رجال ونساء، كراج مغلق وحديقة خلفية، على بعد دقائق من كورنيش خلف.'],
        ['عمارة استثمارية من 4 أدوار', 'Building', 'الشرج', 12, 8, 600, 'USD', 285000, 'عمارة استثمارية من 4 أدوار تضم 8 شقق مؤجرة بالكامل بعائد سنوي مستقر، على شارع تجاري رئيسي.'],
        ['أرض تجارية على شارعين', 'Land', 'فوه', 0, 0, 900, 'SAR', 780000, 'أرض تجارية زاوية على شارعين رئيسيين، تصلح لمجمع تجاري أو مبنى إداري، بصك ملكية جاهز للنقل.'],
        ['بيت شعبي مرمم بالكامل', 'house', 'بويش', 4, 2, 240, 'YER', 42000000, 'بيت شعبي تقليدي أعيد ترميمه بالكامل مع الحفاظ على طابعه الحضرمي: 4 غرف، حوش داخلي وسطح واسع.'],
        ['شقة تمليك — 3 غرف نوم', 'Apartment', 'بويش', 3, 2, 150, 'YER', 36000000, 'شقة تمليك في عمارة جديدة: 3 غرف نوم، صالة، حمامان، مطبخ راكب، مع خزان مياه مستقل وعداد كهرباء خاص.'],
        ['مكتب إداري في برج تجاري', 'Commercial', 'حي السلام', 0, 1, 95, 'YER', 45000000, 'مكتب إداري مجهز في برج تجاري حديث: صالة استقبال، 3 غرف مكاتب ومطبخ صغير، مع مواقف للزوار.'],
        ['شقة اقتصادية للأسر الصغيرة — غرفتان', 'Apartment', 'خلف', 2, 1, 95, 'YER', 24000000, 'شقة اقتصادية مناسبة للأسر الصغيرة: غرفتا نوم، صالة، حمام ومطبخ، في حي سكني هادئ وقريب من الخدمات.'],
    ];

    private const LETTINGS = [
        ['شقة مفروشة — غرفتان وصالة', 'Apartment', 'فوه', 2, 2, 110, 'YER', 220000, 'شقة مفروشة بالكامل جاهزة للسكن: غرفتا نوم، صالة، مطبخ مجهز بالأجهزة، إنترنت ومكيفات في جميع الغرف. الإيجار شهري.'],
        ['فيلا للعائلات مع حوش', 'Villa', 'حي السلام', 5, 4, 360, 'YER', 650000, 'فيلا عائلية واسعة: 5 غرف نوم، مجلسان، حوش مبلط وموقف لسيارتين، قريبة من المدارس الخاصة.'],
        ['أرض مسوّرة للتخزين أو المعارض', 'Land', 'الشرج', 0, 0, 800, 'YER', 300000, 'أرض مسوّرة بالكامل على طريق رئيسي، تصلح مستودعاً أو ساحة عرض سيارات أو معرضاً مؤقتاً. الإيجار شهري.'],
        ['شقة عائلية — 4 غرف', 'Apartment', 'الديس', 4, 3, 200, 'YER', 280000, 'شقة عائلية غير مفروشة: 4 غرف نوم، صالة واسعة، 3 حمامات، في عمارة نظيفة بها حارس وخزان مياه مركزي.'],
        ['محل تجاري في سوق الديس', 'Commercial', 'الديس', 0, 1, 45, 'YER', 180000, 'محل تجاري في قلب سوق الديس، مناسب للملابس أو الإلكترونيات أو الخدمات، بحركة زبائن عالية طوال اليوم.'],
        ['فيلا راقية مع مسبح', 'Villa', 'خلف', 6, 5, 480, 'USD', 1800, 'فيلا فاخرة بمسبح خاص وحديقة: 6 غرف نوم، 5 حمامات، مناسبة للعائلات الكبيرة أو مقرات الضيافة. الإيجار شهري.'],
        ['عمارة سكنية كاملة — 6 شقق', 'Building', 'بويش', 18, 12, 700, 'YER', 1500000, 'عمارة سكنية كاملة من 6 شقق، مناسبة لسكن موظفي الشركات والمنظمات، مع مواقف وخزانات مستقلة.'],
        ['مستودع على الطريق الدائري', 'Commercial', 'الشرج', 0, 1, 500, 'YER', 400000, 'مستودع بسقف مرتفع وبوابة واسعة لدخول الشاحنات، مناسب للتخزين والتوزيع، على الطريق الدائري مباشرة.'],
        ['شقة مفروشة للإيجار السنوي', 'Apartment', 'فوه', 2, 1, 95, 'USD', 450, 'شقة مفروشة أنيقة بعقد سنوي: غرفتا نوم، صالة ومطبخ، قريبة من جامعة حضرموت ومراكز التسوق.'],
        ['مكتب للشركات والمؤسسات', 'Commercial', 'حي السلام', 0, 2, 140, 'YER', 350000, 'مكتب بمساحة مفتوحة وغرفتي اجتماعات في مبنى تجاري حديث، مع مولد احتياطي وإنترنت عالي السرعة.'],
        ['بيت شعبي — 3 غرف', 'house', 'بويش', 3, 2, 180, 'YER', 160000, 'بيت شعبي مرمم بالكامل: 3 غرف نوم، حوش داخلي، مطبخ وحمامان، في حي هادئ قريب من البحر.'],
        ['شقة عائلية — 3 غرف نوم', 'Apartment', 'خلف', 3, 2, 140, 'YER', 200000, 'شقة عائلية مشمسة: 3 غرف نوم، صالة، حمامان، ومطبخ مجهز جزئياً، في عمارة بها مصعد.'],
    ];

    /**
     * A rotating pool of distinct, publicly embeddable virtual-tour links,
     * one per provider actually supported by TourViewer (see
     * resources/views/components/tour-viewer.blade.php) -- previously every
     * tour-enabled property shared the exact same single Kuula link, so the
     * demo could never show what a Matterport or 360° iframe embed actually
     * looked like.
     */
    private const VIRTUAL_TOURS = [
        ['url' => 'https://kuula.co/share/collection/7l2nJ', 'provider' => 'kuula'],
        ['url' => 'https://my.matterport.com/show/?m=zEWsxhZpGba', 'provider' => 'matterport'],
        ['url' => 'https://kuula.co/share/7Jd0M', 'provider' => 'kuula'],
    ];

    private function createArabicProperties($category, string $type): void
    {
        $neighborhoods = Neighborhood::all()->keyBy('name');
        $rows = $type === 'sales' ? self::SALES : self::LETTINGS;

        foreach ($rows as $i => [$title, $propertyType, $district, $bedrooms, $bathrooms, $area, $currency, $price, $description]) {
            $neighborhood = $neighborhoods->get($district);
            $tour = $i % 4 === 0 ? self::VIRTUAL_TOURS[intdiv($i, 4) % count(self::VIRTUAL_TOURS)] : null;

            Property::create([
                'title' => $title,
                'description' => $description,
                'location' => 'المكلا - '.$district,
                'price' => $price,
                'currency' => $currency,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'area_sqft' => $area,
                'year_built' => $propertyType === 'Land' ? null : 2008 + ($i * 3) % 18,
                'property_type' => $propertyType,
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
                'virtual_tour_url' => $tour['url'] ?? null,
                'virtual_tour_provider' => $tour['provider'] ?? null,
                'live_tour_available' => $tour !== null,
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
