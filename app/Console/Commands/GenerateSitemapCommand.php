<?php

namespace App\Console\Commands;

use App\Models\News;
use App\Models\Property;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the XML sitemap for Aqar property listings';

    public function handle()
    {
        $sitemap = Sitemap::create();

        $sitemap->add(Url::create('/')
            ->setPriority(1.0)
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // Static public pages - previously entirely absent from the sitemap
        // (only home + individual properties were included), so the primary
        // internal search-landing page, news index, and every static/legal
        // page were undiscoverable via the sitemap.
        $staticPages = [
            ['route' => 'property.list', 'priority' => 0.9, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'about', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'contact', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'services', 'priority' => 0.6, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'calculators', 'priority' => 0.5, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'news', 'priority' => 0.7, 'frequency' => Url::CHANGE_FREQUENCY_DAILY],
            ['route' => 'property.submit', 'priority' => 0.5, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'property.request', 'priority' => 0.5, 'frequency' => Url::CHANGE_FREQUENCY_MONTHLY],
            ['route' => 'terms-and-conditions', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
            ['route' => 'privacypolicy', 'priority' => 0.3, 'frequency' => Url::CHANGE_FREQUENCY_YEARLY],
        ];

        foreach ($staticPages as $page) {
            try {
                $sitemap->add(Url::create(route($page['route']))
                    ->setPriority($page['priority'])
                    ->setChangeFrequency($page['frequency']));
            } catch (\Throwable $e) {
                // Route not registered in this build - skip rather than fail the whole sitemap
            }
        }

        try {
            // Two status-value conventions coexist for the same field: seeded
            // data uses 'For Sale'/'For Rent' (PropertySeeder), but the
            // Filament property form's own Select options save
            // 'for_sale'/'for_rent' (snake_case) - matching only one leaves
            // the other silently excluded. The previous filter here checked
            // for 'published'/'active'/'available', which matched neither
            // convention, so the sitemap has never actually included a
            // single property regardless of which one a given record uses.
            Property::whereIn('status', ['For Sale', 'For Rent', 'for_sale', 'for_rent'])
                ->get()
                ->each(function (Property $property) use ($sitemap) {
                    $sitemap->add(
                        Url::create(route('property.detail', $property->id))
                            ->setLastModificationDate($property->updated_at ?? now())
                            ->setPriority(0.8)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    );
                });
        } catch (\Throwable $e) {
            // If DB unavailable, proceed with static sitemap
        }

        try {
            News::published()
                ->get()
                ->each(function (News $article) use ($sitemap) {
                    $sitemap->add(
                        Url::create(route('news.detail', $article->slug))
                            ->setLastModificationDate($article->updated_at ?? now())
                            ->setPriority(0.6)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    );
                });
        } catch (\Throwable $e) {
            // If DB unavailable or News has no published scope, skip
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->syncRobotsTxtSitemapDirective();

        $this->info('Sitemap generated successfully at public/sitemap.xml');
        return Command::SUCCESS;
    }

    /**
     * robots.txt is a plain static file (not a Laravel route - a real
     * webserver serves it directly and never reaches the router), so it
     * can't read config('app.url') at request time the way sitemap.xml's
     * own route does. Rewriting its Sitemap: line here, using this
     * artisan run's live APP_URL, means it stays correct through the same
     * "fix APP_URL, then regenerate the sitemap" deployment step this
     * project's docs already call for - no separate step to forget.
     */
    protected function syncRobotsTxtSitemapDirective(): void
    {
        $path = public_path('robots.txt');

        if (! file_exists($path)) {
            return;
        }

        $lines = array_values(array_filter(
            explode("\n", rtrim(file_get_contents($path))),
            fn (string $line) => trim($line) !== '' && ! str_starts_with(trim($line), 'Sitemap:')
        ));

        $lines[] = '';
        $lines[] = 'Sitemap: ' . url('/sitemap.xml');

        file_put_contents($path, implode("\n", $lines) . "\n");
    }
}
