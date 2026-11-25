<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\View\Components\PropertyMap;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::publiclyVisible()->where('is_featured', true)->with('media')->take(3)->get();

        $mapProperties = PropertyMap::markerQuery()->get();

        return view('home', [
            'featuredProperties' => $featuredProperties,
            'mapProperties' => $mapProperties
        ]);
    }
}
