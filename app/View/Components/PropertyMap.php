<?php

namespace App\View\Components;

use App\Models\Property;
use Illuminate\View\Component;

class PropertyMap extends Component
{
    public array $properties;

    /**
     * Use the markers the caller passes (the homepage does); otherwise load
     * every public, geolocated listing. The constructor used to ignore its
     * argument and select only id/title/location/coordinates, so every
     * popup's price rendered as "NaN undefined".
     */
    public function __construct($properties = null)
    {
        $rows = $properties !== null && count($properties) > 0
            ? collect($properties)
            : Property::query()->publiclyVisible()
                ->whereNotNull('latitude')->whereNotNull('longitude')
                ->where('latitude', '!=', 0)->where('longitude', '!=', 0)
                ->get(['id', 'title', 'price', 'currency', 'status', 'latitude', 'longitude']);

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

    public function render()
    {
        return view('components.property-map', [
            'properties' => $this->properties,
        ]);
    }
}
