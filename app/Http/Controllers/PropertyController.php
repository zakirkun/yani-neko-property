<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\PropertyInquiryRequest;
use App\Http\Requests\PropertySearchRequest;
use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(PropertySearchRequest $request): View
    {
        $filters = $request->validated();
        $properties = Property::published()->with(['propertyType', 'images'])
            ->when($filters['keyword'] ?? null, function ($query, $keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            })
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->where('city', $city))
            ->when($filters['purpose'] ?? null, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($filters['property_type'] ?? null, fn ($query, $type) => $query->where('property_type_id', $type))
            ->when(isset($filters['min_price']), fn ($query) => $query->where('price', '>=', $filters['min_price']))
            ->when(isset($filters['max_price']), fn ($query) => $query->where('price', '<=', $filters['max_price']))
            ->when(isset($filters['bedrooms']), fn ($query) => $query->where('bedrooms', '>=', $filters['bedrooms']))
            ->latest('published_at')->paginate(12)->withQueryString();

        return view('properties.index', [
            'properties' => $properties,
            'propertyTypes' => PropertyType::query()->orderBy('name')->get(),
            'cities' => Property::published()->select('city')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(string $slug): View
    {
        $property = Property::published()->where('slug', $slug)
            ->with(['agent', 'propertyType', 'images', 'amenities'])->firstOrFail();
        $similar = Property::published()->whereKeyNot($property->id)
            ->where(function ($query) use ($property) {
                $query->where('city', $property->city)->orWhere('property_type_id', $property->property_type_id);
            })->with(['propertyType', 'images'])->latest('published_at')->take(3)->get();

        return view('properties.show', compact('property', 'similar'));
    }

    public function inquire(PropertyInquiryRequest $request, string $slug): RedirectResponse
    {
        $property = Property::published()->where('slug', $slug)->firstOrFail();
        Lead::create(array_merge($request->validated(), [
            'property_id' => $property->id, 'agent_id' => $property->agent_id, 'status' => LeadStatus::New,
        ]));

        return redirect()->route('properties.show', $property->slug)
            ->with('success', 'Thank you. Your inquiry has been sent and we will be in touch soon.');
    }
}
