<?php

namespace Database\Seeders;

use App\Models\CommunityEvent;
use App\Models\Property;
use Illuminate\Database\Seeder;

class CommunityEventSeeder extends Seeder
{
    /**
     * Realistic local events for the demo's cities.
     *
     * This used to call CommunityEventFactory, whose titles and lorem
     * descriptions are English and whose coordinates fall around London
     * (lat 51-52), with random email domains - so Yemeni property pages
     * showed "Holiday Parade" and "Ebert Union". The factory stays for
     * tests; the demo gets a curated, Arabic, correctly located set.
     */
    public function run(): void
    {
        // No natural unique key: create the demo set exactly once (same guard
        // as PropertySeeder), so a re-seed never duplicates events.
        if (CommunityEvent::exists()) {
            return;
        }

        $cities = [
            'mukalla' => ['lat' => 14.5425, 'lng' => 49.1242, 'name' => 'المكلا'],
            'sanaa' => ['lat' => 15.3694, 'lng' => 44.1910, 'name' => 'صنعاء'],
            'aden' => ['lat' => 12.7855, 'lng' => 45.0187, 'name' => 'عدن'],
        ];

        $events = [
            ['mukalla', 'سوق المكلا الأسبوعي للمنتجات المحلية', 'سوق مفتوح للمزارعين والحرفيين على كورنيش المكلا: تمور حضرمية، عسل دوعني، ومشغولات يدوية.', 'market', 'جمعية تجار المكلا', 'كورنيش المكلا', 9, 3],
            ['mukalla', 'مهرجان البلدة السياحي', 'فعاليات تراثية وفنون شعبية وعروض بحرية احتفاءً بموسم البلدة على ساحل حضرموت.', 'festival', 'مكتب السياحة بحضرموت', 'شاطئ خلف — المكلا', 20, 4],
            ['mukalla', 'يوم تنظيف شاطئ فوه', 'مبادرة تطوعية لتنظيف الشاطئ وزراعة أشجار الظل بمشاركة سكان الحي.', 'community', 'مبادرة المكلا الخضراء', 'شاطئ فوه', 13, 3],
            ['mukalla', 'ورشة: كيف تشتري عقارك الأول', 'ورشة مجانية تشرح خطوات الشراء الآمن: التحقق من الصكوك، التثمين، والتوثيق.', 'workshop', 'نوفيكسا عقار', 'قاعة الغرفة التجارية — المكلا', 6, 2],
            ['sanaa', 'معرض صنعاء للبناء والتشييد', 'أحدث مواد البناء والتشطيبات وحلول الطاقة الشمسية للمنازل بمشاركة أكثر من 60 عارضاً.', 'market', 'الغرفة التجارية بأمانة العاصمة', 'مركز صنعاء الدولي للمعارض', 16, 6],
            ['sanaa', 'ليلة الإنشاد في صنعاء القديمة', 'أمسية إنشاد وموروث صنعاني في ساحة باب اليمن.', 'concert', 'جمعية الموروث الشعبي', 'باب اليمن — صنعاء القديمة', 25, 3],
            ['sanaa', 'بطولة الأحياء لكرة القدم', 'دوري ودي بين فرق أحياء حدة والسبعين والأصبحي.', 'sports', 'نادي حدة الرياضي', 'ملعب حدة — صنعاء', 11, 4],
            ['aden', 'مهرجان عدن للتسوق', 'تخفيضات وعروض عائلية في أسواق كريتر والمعلا مع فعاليات للأطفال.', 'festival', 'الغرفة التجارية والصناعية بعدن', 'سوق الطويل — كريتر', 18, 5],
            ['aden', 'سباق الخيرية على ساحل أبين', 'سباق جري خيري تعود عائداته لدعم المدارس في الأحياء الساحلية.', 'fundraiser', 'جمعية عدن الخيرية', 'ساحل أبين — خور مكسر', 30, 3],
            ['aden', 'ورشة صيانة المنازل في المناخ الساحلي', 'نصائح عملية للعزل ومقاومة الرطوبة والملوحة في المباني القريبة من البحر.', 'workshop', 'نوفيكسا عقار', 'المعلا — عدن', 8, 2],
        ];

        foreach ($events as $i => [$city, $title, $description, $category, $organizer, $location, $inDays, $hours]) {
            $start = now()->addDays($inDays)->setTime(17, 0);
            CommunityEvent::create([
                'title' => $title,
                'description' => $description,
                'event_date' => $start,
                'end_date' => $start->copy()->addHours($hours),
                'location' => $location.'، '.$cities[$city]['name'],
                // Small offsets so the pins don't stack on one point.
                'latitude' => $cities[$city]['lat'] + (($i % 3) - 1) * 0.004,
                'longitude' => $cities[$city]['lng'] + (($i % 2) ? 0.005 : -0.005),
                'category' => $category,
                'organizer' => $organizer,
                'contact_email' => 'events@novixa.dev',
                'contact_phone' => config('branding.phone'),
                'website_url' => null,
                'is_public' => true,
            ]);
        }

        // Each of the first few mapped properties gets one event right next
        // door, so "nearby events" on a property page is never empty.
        Property::whereNotNull('latitude')->whereNotNull('longitude')->limit(5)->get()
            ->each(fn (Property $property, int $i) => CommunityEvent::create([
                'title' => 'لقاء الجيران الشهري',
                'description' => 'لقاء مفتوح لسكان الحي للتعارف ومناقشة خدمات المنطقة.',
                'event_date' => now()->addDays(10 + $i * 3)->setTime(18, 0),
                'end_date' => now()->addDays(10 + $i * 3)->setTime(20, 0),
                'location' => $property->location ?: 'مجلس الحي',
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'category' => 'community',
                'organizer' => 'لجنة الحي',
                'contact_email' => 'events@novixa.dev',
                'contact_phone' => config('branding.phone'),
                'website_url' => null,
                'is_public' => true,
                'property_id' => $property->id,
            ]));
    }
}
