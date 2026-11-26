<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Property;
use App\View\Components\PropertyMap;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::publiclyVisible()->where('is_featured', true)->with('media')->take(3)->get();

        $mapProperties = PropertyMap::markerQuery()->get();

        // Type and area of every listing a visitor can actually open; small
        // (one row per listing, two columns) and grouped here rather than in
        // SQL so mixed-case types ('Villa'/'villa') count as one.
        $listings = Property::query()->publiclyVisible()->active()->get(['property_type', 'location']);

        $latestNews = News::published()->latest('published_at')->take(3)->get();

        return view('home', [
            'featuredProperties' => $featuredProperties,
            'mapProperties' => $mapProperties,
            'browseTypes' => $this->countByType($listings),
            'browseAreas' => $this->countByArea($listings),
            'latestNews' => $latestNews,
        ]);
    }

    /** @return Collection<int, array{value: string, label: string, count: int}> */
    private function countByType(Collection $listings): Collection
    {
        return $listings
            ->filter(fn (Property $p) => filled($p->property_type))
            ->groupBy(fn (Property $p) => strtolower($p->property_type))
            ->map(fn (Collection $group) => [
                'value' => $group->first()->property_type,
                'label' => Property::typeLabel($group->first()->property_type),
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->values();
    }

    /**
     * Listings per city: locations are stored as "City - District", so the
     * part before the first " - " is the city.
     *
     * @return Collection<int, array{name: string, count: int}>
     */
    private function countByArea(Collection $listings): Collection
    {
        return $listings
            ->map(fn (Property $p) => trim(Str::before((string) $p->location, ' - ')))
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(6)
            ->map(fn (int $count, string $name) => ['name' => $name, 'count' => $count])
            ->values();
    }
}
