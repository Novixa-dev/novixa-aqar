<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Search-snippet rules shared by every public page:
 *  - pages that set no title/description of their own get a route-specific
 *    one here instead of the site-wide default (duplicate titles and
 *    descriptions across pages are an SEO defect);
 *  - titles drop the "| Brand" suffix when it would push them past what
 *    search results display (~60 chars), and descriptions are trimmed to
 *    ~160 chars on a word boundary.
 */
final class PageMeta
{
    public const TITLE_MAX = 60;

    public const DESCRIPTION_MAX = 160;

    /** @var array<string, array{title: array{ar: string, en: string}, description: array{ar: string, en: string}}> */
    private const ROUTES = [
        'property.submit' => [
            'title' => ['ar' => 'أضف عقارك', 'en' => 'List your property'],
            'description' => [
                'ar' => 'اعرض عقارك للبيع أو الإيجار على :brand: أضف الصور والسعر والموقع، وتتم مراجعة الإعلان قبل نشره.',
                'en' => 'List your property for sale or rent on :brand: add photos, price and location; every listing is reviewed before it goes live.',
            ],
        ],
        'property.request' => [
            'title' => ['ar' => 'اطلب عقاراً', 'en' => 'Request a property'],
            'description' => [
                'ar' => 'أخبرنا بالعقار الذي تبحث عنه — النوع والمدينة والميزانية — وسيتواصل معك فريق :brand بالعروض المناسبة.',
                'en' => 'Tell us what you are looking for — type, city and budget — and the :brand team will contact you with matching listings.',
            ],
        ],
        'calculators' => [
            'title' => ['ar' => 'الحاسبات العقارية', 'en' => 'Real estate calculators'],
            'description' => [
                'ar' => 'احسب القسط الشهري للتمويل العقاري وتكاليف الانتقال قبل اتخاذ قرار الشراء أو الإيجار.',
                'en' => 'Estimate monthly mortgage payments and moving costs before you decide to buy or rent.',
            ],
        ],
        'login' => [
            'title' => ['ar' => 'تسجيل الدخول', 'en' => 'Log in'],
            'description' => [
                'ar' => 'سجّل الدخول إلى حسابك في :brand لمتابعة عقاراتك ومواعيدك وعقودك ومدفوعاتك.',
                'en' => 'Log in to your :brand account to manage your properties, viewings, leases and payments.',
            ],
        ],
        'register' => [
            'title' => ['ar' => 'إنشاء حساب', 'en' => 'Create an account'],
            'description' => [
                'ar' => 'أنشئ حساباً مجانياً في :brand لحفظ العقارات المفضلة وعمليات البحث وتلقي التنبيهات.',
                'en' => 'Create a free :brand account to save favourite listings and searches and receive alerts.',
            ],
        ],
        'password.request' => [
            'title' => ['ar' => 'استعادة كلمة المرور', 'en' => 'Reset your password'],
            'description' => [
                'ar' => 'أدخل بريدك الإلكتروني لاستلام رابط إعادة تعيين كلمة المرور لحسابك في :brand.',
                'en' => 'Enter your email to receive a link to reset your :brand password.',
            ],
        ],
        'services' => [
            'title' => ['ar' => 'خدماتنا', 'en' => 'Our services'],
            'description' => [
                'ar' => 'خدمات :brand: بيع وتسويق العقارات، إدارة الأملاك والتأجير، التقييم والاستشارات العقارية.',
                'en' => ':brand services: property sales and marketing, property management and leasing, valuation and advice.',
            ],
        ],
        'about' => [
            'title' => ['ar' => 'من نحن', 'en' => 'About us'],
            'description' => [
                'ar' => 'تعرّف على :brand: رؤيتنا وقيمنا وكيف نساعد الباحثين عن العقارات والملاك على اتخاذ قرارات أفضل.',
                'en' => 'Meet :brand: our vision, our values and how we help property seekers and owners make better decisions.',
            ],
        ],
        'contact' => [
            'title' => ['ar' => 'اتصل بنا', 'en' => 'Contact us'],
            'description' => [
                'ar' => 'تواصل مع فريق :brand عبر الهاتف أو البريد أو نموذج المراسلة، وسنرد عليك في أقرب وقت.',
                'en' => 'Get in touch with the :brand team by phone, email or the contact form — we reply as soon as possible.',
            ],
        ],
    ];

    /** @return array{title: ?string, description: ?string} */
    public static function forRoute(?string $routeName = null, ?string $locale = null): array
    {
        $routeName ??= Route::currentRouteName();
        $locale = ($locale ?? app()->getLocale()) === 'ar' ? 'ar' : 'en';
        $meta = self::ROUTES[$routeName] ?? null;
        if ($routeName === 'contact.show') {
            $meta = self::ROUTES['contact'];
        }

        if (! $meta) {
            return ['title' => null, 'description' => null];
        }

        $brand = app(Brand::class);

        return [
            'title' => $brand->title($meta['title'][$locale]),
            'description' => str_replace(':brand', $brand->name(), $meta['description'][$locale]),
        ];
    }

    /** Drop the " | Brand" suffix when it would push the title past TITLE_MAX. */
    public static function title(string $title, string $brandName): string
    {
        $title = trim($title);
        $suffix = ' | '.$brandName;

        if (mb_strlen($title) > self::TITLE_MAX && Str::endsWith($title, $suffix)) {
            return mb_substr($title, 0, mb_strlen($title) - mb_strlen($suffix));
        }

        return $title;
    }

    /** Plain text, at most $max characters, cut on a word boundary. */
    public static function description(string $description, int $max = self::DESCRIPTION_MAX): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        $cut = mb_substr($text, 0, $max - 1);
        $space = mb_strrpos($cut, ' ');

        return rtrim($space > $max * 0.6 ? mb_substr($cut, 0, $space) : $cut, " ،,.;:—-").'…';
    }
}
