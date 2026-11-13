<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoAndBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_keeps_crawlers_out_of_a_non_indexable_deployment(): void
    {
        config(['branding.indexable' => false]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSeeText('Disallow: /')
            ->assertDontSeeText('Sitemap:');
    }

    public function test_robots_txt_lists_panel_rules_and_the_sitemap_when_indexable(): void
    {
        config(['branding.indexable' => true]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSeeText('Disallow: /admin')
            ->assertSeeText('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_pages_carry_one_complete_set_of_social_tags_and_the_brand(): void
    {
        config(['branding.indexable' => false]);

        $html = $this->get('/')->assertOk()->getContent();

        foreach (['og:title', 'og:description', 'og:image', 'og:url', 'og:type', 'og:locale', 'og:site_name'] as $property) {
            $this->assertSame(1, substr_count($html, 'property="'.$property.'"'), "{$property} must appear exactly once");
        }
        foreach (['twitter:card', 'twitter:title', 'twitter:image'] as $name) {
            $this->assertSame(1, substr_count($html, 'name="'.$name.'"'), "{$name} must appear exactly once");
        }

        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow">', $html);
        // Social platforms only accept absolute image URLs.
        $this->assertMatchesRegularExpression('#<meta property="og:image" content="https?://[^"]+/images/og-default\.jpg">#', $html);
        $this->assertStringContainsString(config('branding.name.ar'), $html);
        $this->assertStringNotContainsString('دار حضرموت', $html);
        $this->assertStringNotContainsString('Dar Hadhramaut', $html);
    }

    public function test_the_homepage_publishes_organization_and_website_structured_data(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $blocks);
        $graph = collect($blocks[1])
            ->map(fn (string $json) => json_decode($json, true))
            ->firstWhere('@context', 'https://schema.org')['@graph'] ?? [];
        $types = array_column($graph, '@type');

        $this->assertContains('RealEstateAgent', $types);
        $this->assertContains('WebSite', $types);
        $this->assertSame(url('/'), $graph[array_search('WebSite', $types)]['url']);
    }

    public function test_an_english_stored_copyright_is_not_shown_on_the_arabic_site(): void
    {
        $settings = app(\App\Settings\GeneralSettings::class);
        $settings->footer_copyright = '© 2026 Novixa Aqar. All rights reserved.';
        $settings->save();
        $brand = app(\App\Support\Brand::class);

        app()->setLocale('ar');
        $this->assertStringContainsString('جميع الحقوق محفوظة', $brand->copyright());

        app()->setLocale('en');
        $this->assertSame('© 2026 Novixa Aqar. All rights reserved.', $brand->copyright());

        $this->get('/')->assertOk();
    }

    public function test_an_indexable_deployment_has_no_noindex_tag(): void
    {
        config(['branding.indexable' => true]);

        $this->get('/')->assertOk()->assertDontSee('noindex', false);
    }

    public function test_the_default_share_image_exists(): void
    {
        $this->assertFileExists(public_path(config('branding.og_image')));
    }
}
