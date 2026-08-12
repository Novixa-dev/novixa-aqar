<?php

namespace App\Console\Commands;

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

        try {
            Property::whereIn('status', ['published', 'active', 'available'])
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

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at public/sitemap.xml');
        return Command::SUCCESS;
    }
}
