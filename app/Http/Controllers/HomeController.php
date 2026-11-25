<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::publiclyVisible()->where('is_featured', true)->with('media')->take(3)->get();

        $mapProperties = Property::publiclyVisible()->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            // currency/status feed the map popups' price and "monthly" label.
            ->get(['id', 'title', 'price', 'currency', 'status', 'latitude', 'longitude']);

        return view('home', [
            'featuredProperties' => $featuredProperties,
            'mapProperties' => $mapProperties
        ]);
    }
}
