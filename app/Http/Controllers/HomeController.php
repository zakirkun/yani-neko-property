<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Property::published()->where('is_featured', true)
            ->with(['propertyType', 'images'])->latest('published_at')->take(6)->get();
        $latest = Property::published()
            ->with(['propertyType', 'images'])->latest('published_at')->take(6)->get();

        return view('home', [
            'featured' => $featured,
            'latest' => $latest,
            'propertyTypes' => PropertyType::query()->orderBy('name')->get(),
            'cities' => Property::published()->select('city')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }
}
