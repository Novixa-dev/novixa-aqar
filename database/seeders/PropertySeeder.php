<?php

namespace Database\Seeders;

use App\Models\Neighborhood;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PropertySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');
        $salesCategory = PropertyCategory::where('name', 'sales')->first();
        $lettingsCategory = PropertyCategory::where('name', 'lettings')->first();
        $hmoCategory = PropertyCategory::where('name', 'hmo')->first();

        $this->createArabicProperties($salesCategory, 12, 'sales');
        $this->createArabicProperties($lettingsCategory, 12, 'lettings');
        
        if ($hmoCategory) {
            $this->createHmoProperties($hmoCategory, 4);
        }
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

            Property::create([
                'title' => $title,
                'description' => $desc,
                'location' => 'المكلا - ' . ($neighborhood ? $neighborhood->name : 'حضرموت'),
                'price' => $type === 'sales' ? $faker->numberBetween(25000000, 180000000) : $faker->numberBetween(250000, 1500000),
                'bedrooms' => $faker->numberBetween(1, 6),
                'bathrooms' => $faker->numberBetween(1, 4),
                'area_sqft' => $faker->numberBetween(120, 600),
                'year_built' => $faker->numberBetween(2015, 2026),
                'property_type' => $propertyTypes[$i % count($propertyTypes)],
                'status' => $type === 'sales' ? 'For Sale' : 'For Rent',
                'is_featured' => $i % 3 === 0,
                'list_date' => now()->subDays($i * 3),
                'user_id' => 1,
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
                'bedrooms' => 6,
                'bathrooms' => 3,
                'area_sqft' => 250,
                'year_built' => 2022,
                'property_type' => 'HMO',
                'status' => 'For Rent',
                'is_featured' => false,
                'list_date' => now(),
                'user_id' => 1,
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
