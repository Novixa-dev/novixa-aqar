<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Neighborhood;

class NeighborhoodSeeder extends Seeder
{
    public function run()
    {
        $neighborhoods = [
            [
                'name' => 'فوه',
                'description' => 'منطقة سكنية حيوية غرب مدينة المكلا تتميز بالقرب من الكليات والمرافق العامة والخدمات.',
                'schools' => [
                    ['name' => 'مدرسة فوه النموذجية', 'rating' => 9],
                    ['name' => 'ثانوية ابن خلدون', 'rating' => 8],
                ],
                'amenities' => ['مراكز تسوق', 'مطاعم', 'حدائق', 'مستشفيات', 'مواقف عامة'],
                'crime_rate' => 'منخفض',
                'median_income' => 450000,
                'population' => 45000,
                'walk_score' => 85,
                'transit_score' => 80,
                'last_updated' => now(),
            ],
            [
                'name' => 'الديس',
                'description' => 'حي سكني وتجاري عريق في قلب المكلا يتميز بتوفر كافة الخدمات والأسواق التجارية.',
                'schools' => [
                    ['name' => 'مدرسة الديس الحديثة', 'rating' => 9],
                    ['name' => 'مجمع 22 مايو', 'rating' => 9],
                ],
                'amenities' => ['أسواق تجارية', 'مساجد', 'مراكز صحية', 'مطاعم وفنادق'],
                'crime_rate' => 'منخفض جداً',
                'median_income' => 500000,
                'population' => 38000,
                'walk_score' => 90,
                'transit_score' => 85,
                'last_updated' => now(),
            ],
            [
                'name' => 'الشرج',
                'description' => 'المركز التجاري الرئيسي لمدينة المكلا يضم الأسواق والمؤسسات والمحلات التجارية.',
                'schools' => [
                    ['name' => 'مدرسة الشرج الأساسية', 'rating' => 8],
                ],
                'amenities' => ['أسواق مركزية', 'بنوك ومصارف', 'مراكز صرافة', 'مطاعم'],
                'crime_rate' => 'منخفض',
                'median_income' => 520000,
                'population' => 40000,
                'walk_score' => 95,
                'transit_score' => 90,
                'last_updated' => now(),
            ],
            [
                'name' => 'حي السلام',
                'description' => 'حي هادئ مطل على الساحل يجمع بين الطابع السكني والواجهة البحرية الساحرة.',
                'schools' => [
                    ['name' => 'مدرسة السلام الأهلية', 'rating' => 9],
                ],
                'amenities' => ['إطلالة بحرية', 'متنزهات', 'مطاعم راقية', 'مقاهي'],
                'crime_rate' => 'منخفض جداً',
                'median_income' => 600000,
                'population' => 20000,
                'walk_score' => 88,
                'transit_score' => 75,
                'last_updated' => now(),
            ],
            [
                'name' => 'بويش',
                'description' => 'منطقة سكنية وتجارية متنامية تتميز بالمساحات الواسعة والمشاريع الحديثة.',
                'schools' => [
                    ['name' => 'مدرسة بويش للتعليم الأساسي', 'rating' => 8],
                ],
                'amenities' => ['مشاريع حديثة', 'مساحات واسعة', 'خدمات عامة'],
                'crime_rate' => 'منخفض',
                'median_income' => 420000,
                'population' => 30000,
                'walk_score' => 75,
                'transit_score' => 70,
                'last_updated' => now(),
            ],
            [
                'name' => 'خلف',
                'description' => 'منطقة ساحلية واعدة تضم الميناء والمرافق السياحية والإطلالات البحرية الفريدة.',
                'schools' => [
                    ['name' => 'مدرسة خلف الأساسية', 'rating' => 8],
                ],
                'amenities' => ['إطلالة على الميناء', 'فنادق سياحية', 'منتجعات'],
                'crime_rate' => 'منخفض',
                'median_income' => 480000,
                'population' => 15000,
                'walk_score' => 80,
                'transit_score' => 75,
                'last_updated' => now(),
            ],
        ];

        foreach ($neighborhoods as $neighborhood) {
            Neighborhood::create($neighborhood);
        }
    }
}
