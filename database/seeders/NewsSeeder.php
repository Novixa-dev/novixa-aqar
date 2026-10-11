<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\User;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();
        if (!$author) {
            return;
        }

        $newsArticles = [
            [
                'title' => 'تقرير السوق العقاري في حضرموت وصنعاء لعام 2026: نمو ملحوظ وفرص واعدة',
                'slug' => 'yemen-real-estate-market-report-2026',
                'excerpt' => 'يشهد القطاع العقاري في المكلا وصنعاء وعدن إقبالاً متزايداً على المخططات السكنية الحديثة والمجمعات التجارية المطورة.',
                'content' => '<p>كشف التقرير السنوي الصادر عن <strong>' . config('branding.name.ar') . '</strong> عن تسجيل نمو إيجابي في حجم التداولات العقارية في المحافظات اليمنية الرئيسية بنسبة 18% مقارنة بالعام السابق.</p><p>وتصدرت مدينة <strong>المكلا (أحياء فوه، الديس، والشرج)</strong> قائمة المناطق الأكثر جذباً للاستثمارات السكنية، مدفوعة بتوفر الخدمات وتوسع المخططات العمرانية المنظمة وقربها من الشواطئ السياحية.</p><p>وفي <strong>صنعاء</strong>، واصلت أحياء <strong>حدة وبيت بوس</strong> استقطاب الاستثمارات العقارية الفاخرة، خاصة في قطاع الشقق المفروشة والمباني التجارية الحديثة.</p>',
                'is_featured' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'دليل التملك العقاري في اليمن: خطوات توثيق البصائر والعقود الشرعية',
                'slug' => 'property-ownership-guide-yemen-deeds',
                'excerpt' => 'تعرف على الخطوات القانونية والشرعية الصحيحة لفحص وثائق الملكية والبصائر والتعميد في المحاكم والتوثيق العقاري.',
                'content' => '<p>يعد التأكد من صحة وثائق الملكية الخطوة الأهم قبل الشراء. يقدم مستشارو <strong>' . config('branding.name.ar') . '</strong> أهم الإرشادات:</p><ol><li><strong>فحص أصل البصيرة:</strong> التحقق من تسلسل الملكية وخلو العقار من أي نزاعات أو رهونات.</li><li><strong>المطابقة الميدانية:</strong> فحص الحدود والمساحة على أرض الواقع بواسطة مهندس مساحة معتمد.</li><li><strong>التعميد الرسمي:</strong> توثيق العقد لدى قلم التوثيق في المحكمة المختصة ودفع الرسوم القانونية.</li></ol>',
                'is_featured' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'نصائح لتصميم وتشطيب المنازل الحديثة المقاومة للرطوبة والحرارة',
                'slug' => 'modern-home-design-climate-resilience',
                'excerpt' => 'أفضل الحلول الهندسية واختيارات مواد البناء المناسبة للمناخ الساحلي والجبلي لتقليل استهلاك الطاقة.',
                'content' => '<p>تعتبر كفاءة الطاقة والعزل الحراري من أهم معايير البناء العصري في اليمن. استخدام الطوب العازل، وتركيب منظومات الطاقة الشمسية الهجينة، واختيار الدهانات المقاومة للملوحة في المدن الساحلية مثل المكلا وعدن يضمن الحفاظ على جودة المبنى لعقود طويلة.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'كيف تحدد القيمة السوقية العادلة لعقارك باستخدام التقييم الذكي؟',
                'slug' => 'how-to-determine-property-valuation-smart-tools',
                'excerpt' => 'استكشف معايير تثمين العقارات وفق موقعها ومساحتها وحركة العرض والطلب الحالية.',
                'content' => '<p>تعتمد منصة <strong>' . config('branding.name.ar') . '</strong> على خوارزميات تثمين ذكية تقارن الصفقات المماثلة في نفس الحي وتأخذ بعين الاعتبار واجهة العقار، عرض الشارع، وقربه من المرافق الحيوية لمنح البائع والمشتري تقييماً عادلاً وشفافاً.</p>',
                'is_featured' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                // This article was the only one of the five still authored in
                // English — every other row here has an Arabic title/excerpt/
                // content (this table has no separate _ar/_en columns, so
                // whatever language the row is written in is what every
                // visitor sees regardless of locale). On an Arabic-first news
                // list it stood out as the one raw-English card.
                'title' => 'استراتيجيات الاستثمار العقاري في اليمن لعام 2026',
                'slug' => 'real-estate-investment-strategies-2026',
                'excerpt' => 'نظرة استراتيجية على فرص الاستثمار العقاري ذات العائد المرتفع في المكلا وصنعاء وعدن.',
                'content' => '<p>يوفر الاستثمار في العقارات التجارية والسكنية بالمراكز الساحلية والحضرية الناشئة تحوطاً قوياً ضد التضخم، إلى جانب عوائد إيجارية مستقرة تتجاوز 9% سنوياً.</p>',
                'is_featured' => false,
                'published_at' => now()->subDays(10),
            ]
        ];

        foreach ($newsArticles as $article) {
            News::updateOrCreate(
                ['slug' => $article['slug']],
                array_merge($article, [
                    'author_id' => $author->id,
                ])
            );
        }

        $this->command->info('Rich news articles seeded successfully!');
    }
}
