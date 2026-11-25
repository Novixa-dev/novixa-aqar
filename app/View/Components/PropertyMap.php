<?php

namespace App\View\Components;

use App\Models\Property;
use Illuminate\View\Component;

class PropertyMap extends Component
{
    public array $properties;

    /** Enough markers to cover the demo market without bloating the page. */
    public const MAX_MARKERS = 300;

    /**
     * Use the markers the caller passes (the homepage does) -- an empty list
     * means an empty map, not "show everything"; with no argument, load the
     * public, active, geolocated listings. The constructor used to ignore
     * its argument and select only id/title/location/coordinates, so every
     * popup's price rendered as "NaN undefined".
     */
    public function __construct($properties = null)
    {
        $rows = $properties !== null
            ? collect($properties)
            : self::markerQuery()->get();

        $this->properties = $rows->map(fn ($p) => [
            'id' => data_get($p, 'id'),
            'title' => (string) data_get($p, 'title'),
            'price' => data_get($p, 'price'),
            'currency' => data_get($p, 'currency') ?: config('currencies.default'),
            'rent' => Property::canonicalStatus(data_get($p, 'status')) === Property::STATUS_FOR_RENT,
            'latitude' => (float) data_get($p, 'latitude'),
            'longitude' => (float) data_get($p, 'longitude'),
        ])->values()->all();
    }

    /** Public, still-available (not sold/rented), geolocated listings. */
    public static function markerQuery()
    {
        return Property::query()->publiclyVisible()->active()
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->where('latitude', '!=', 0)->where('longitude', '!=', 0)
            ->latest()
            ->limit(self::MAX_MARKERS)
            ->select(['id', 'title', 'price', 'currency', 'status', 'latitude', 'longitude']);
    }

    public function render()
    {
        return view('components.property-map', [
            'properties' => $this->properties,
        ]);
    }
}
