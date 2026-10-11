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
        $this->assertStringContainsString(config('branding.name.ar'), $html);
        $this->assertStringNotContainsString('دار حضرموت', $html);
        $this->assertStringNotContainsString('Dar Hadhramaut', $html);
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
