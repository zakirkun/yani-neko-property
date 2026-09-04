@extends('layouts.app')
@section('title', 'Properties for Sale & Rent | Haven & Key')
@section('description', 'Browse published homes for sale and rent. Filter by location, property type, price and bedrooms.')
@section('content')
@php
    $filterNames = ['keyword', 'city', 'purpose', 'property_type', 'min_price', 'max_price', 'bedrooms'];
    $activeFilterCount = collect(request()->only($filterNames))->filter(fn ($value) => filled($value))->count();
@endphp
<section class="page-hero">
    <div class="container page-hero-inner"><div><p class="eyebrow">Find your place</p><h1>Properties</h1><p>Explore homes selected for their quality, character and location.</p></div><div class="page-hero-note"><span>{{ $properties->total() }}</span><small>{{ Str::plural('home', $properties->total()) }} available</small></div></div>
</section>
<section class="section container listing-layout">
    <form class="filter-panel" action="{{ route('properties.index') }}" method="GET">
        <div class="filter-heading">
            <div><p class="eyebrow">Refine your search</p><h2>Find the right home</h2></div>
            <button class="filter-toggle" type="button" aria-controls="property-filters" aria-expanded="{{ $activeFilterCount ? 'true' : 'false' }}" data-active-filters="{{ $activeFilterCount }}">
                <span data-filter-label>{{ $activeFilterCount ? 'Hide filters' : 'Show filters' }}</span>
                @if($activeFilterCount)<b>{{ $activeFilterCount }}</b>@endif
                <span aria-hidden="true">⌄</span>
            </button>
        </div>
        <div class="filter-body" id="property-filters">
            <div class="filter-grid">
                <div class="field wide"><label for="keyword">Keyword</label><input id="keyword" name="keyword" value="{{ request('keyword') }}" placeholder="Address, feature or property name"></div>
                <div class="field"><label for="city">City</label><select id="city" name="city"><option value="">Any city</option>@foreach($cities as $city)<option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>@endforeach</select></div>
                <div class="field"><label for="purpose">Purpose</label><select id="purpose" name="purpose"><option value="">Buy or rent</option><option value="sale" @selected(request('purpose') === 'sale')>For sale</option><option value="rent" @selected(request('purpose') === 'rent')>For rent</option></select></div>
                <div class="field"><label for="property_type">Property type</label><select id="property_type" name="property_type"><option value="">Any type</option>@foreach($propertyTypes as $type)<option value="{{ $type->id }}" @selected((string) request('property_type') === (string) $type->id)>{{ $type->name }}</option>@endforeach</select></div>
                <div class="field"><label for="min_price">Minimum price</label><input id="min_price" type="number" min="0" step="100" inputmode="numeric" name="min_price" value="{{ request('min_price') }}" placeholder="$0"></div>
                <div class="field"><label for="max_price">Maximum price</label><input id="max_price" type="number" min="0" step="100" inputmode="numeric" name="max_price" value="{{ request('max_price') }}" placeholder="No maximum"></div>
                <div class="field"><label for="bedrooms">Minimum bedrooms</label><select id="bedrooms" name="bedrooms"><option value="">Any</option>@for($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected((string) request('bedrooms') === (string) $i)>{{ $i }}+</option>@endfor</select></div>
            </div>
            @if($errors->any())<div class="alert error-summary" role="alert"><strong>Please check your search.</strong> {{ $errors->first() }}</div>@endif
            <div class="filter-actions"><button class="button" type="submit">Show properties</button>@if($activeFilterCount)<a class="clear-link" href="{{ route('properties.index') }}">Clear all filters</a>@endif</div>
        </div>
    </form>
    <div class="results-heading"><div><p class="eyebrow">Available now</p><h2>{{ $properties->total() }} {{ Str::plural('property', $properties->total()) }}</h2></div><p>Sorted by newest first</p></div>
    @if($properties->isEmpty())
        <div class="empty-state"><span aria-hidden="true">⌂</span><h2>No properties matched your search</h2><p>Try broadening your filters or <a href="{{ route('contact') }}">tell us what you need</a>.</p><a class="button" href="{{ route('properties.index') }}">Reset search</a></div>
    @else
        <div class="card-grid">@foreach($properties as $property)<x-property-card :property="$property" />@endforeach</div>
        @include('partials.pagination', ['paginator' => $properties])
    @endif
</section>
@endsection
