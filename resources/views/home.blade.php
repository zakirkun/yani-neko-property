@extends('layouts.app')
@section('title', 'Haven & Key | Exceptional homes, personal service')
@section('description', 'Search homes for sale and rent and get expert local guidance from the Haven & Key property team.')
@section('content')
<section class="hero">
    <div class="container hero-layout">
        <div class="hero-copy">
            <p class="eyebrow light-text">Property, personally curated</p>
            <h1>Find a place that feels like yours.</h1>
            <p class="hero-intro">Exceptional homes, honest local knowledge and a refreshingly personal approach to your next move.</p>
            <div class="hero-trust" aria-label="Our service promises">
                <span><b>12+</b> curated listings</span>
                <span><b>1:1</b> expert guidance</span>
                <span><b>Local</b> market insight</span>
            </div>
        </div>
        <form class="hero-search" action="{{ route('properties.index') }}" method="GET" aria-label="Quick property search">
            <div class="search-field search-location">
                <label for="hero-keyword">Where would you like to live?</label>
                <input id="hero-keyword" name="keyword" placeholder="City, address or keyword" autocomplete="off">
            </div>
            <div class="search-field">
                <label for="hero-purpose">I want to</label>
                <select id="hero-purpose" name="purpose">
                    <option value="">Buy or rent</option>
                    <option value="sale">Buy a home</option>
                    <option value="rent">Rent a home</option>
                </select>
            </div>
            <div class="search-field">
                <label for="hero-type">Property type</label>
                <select id="hero-type" name="property_type">
                    <option value="">Any type</option>
                    @foreach($propertyTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
                </select>
            </div>
            <button class="button search-button" type="submit"><span>Search properties</span><span aria-hidden="true">→</span></button>
        </form>
    </div>
</section>

@if($featured->isNotEmpty())
<section class="section container">
    <div class="section-heading">
        <div><p class="eyebrow">Handpicked for you</p><h2>Featured properties</h2><p>Distinctive homes selected by our local team.</p></div>
        <a class="text-link" href="{{ route('properties.index') }}">View all properties <span aria-hidden="true">→</span></a>
    </div>
    <div class="card-grid">@foreach($featured as $property)<x-property-card :property="$property" />@endforeach</div>
</section>
@endif

<section class="section services">
    <div class="container">
        <div class="center-heading"><p class="eyebrow">Why Haven & Key</p><h2>Property expertise, without the pressure</h2><p>Clear advice and attentive service at every stage of your move.</p></div>
        <div class="service-grid">
            <article><span class="service-number">01</span><div><h3>Local insight</h3><p>Detailed neighbourhood knowledge helps you make a confident, informed decision.</p></div></article>
            <article><span class="service-number">02</span><div><h3>Considered matches</h3><p>We listen first, then surface homes that genuinely fit your life and ambitions.</p></div></article>
            <article><span class="service-number">03</span><div><h3>End-to-end care</h3><p>From your first viewing to closing day, one trusted team stays by your side.</p></div></article>
        </div>
    </div>
</section>

@if($latest->isNotEmpty())
<section class="section container">
    <div class="section-heading"><div><p class="eyebrow">Just listed</p><h2>Fresh to the market</h2><p>Be among the first to explore our newest homes.</p></div></div>
    <div class="card-grid">@foreach($latest as $property)<x-property-card :property="$property" />@endforeach</div>
</section>
@endif

<section class="cta">
    <div class="container cta-inner">
        <div><p class="eyebrow light-text">Let’s find your next home</p><h2>Ready to make a move?</h2><p>Tell us what you are looking for and let our team do the rest.</p></div>
        <a class="button button-light" href="{{ route('contact') }}">Speak with our team <span aria-hidden="true">→</span></a>
    </div>
</section>
@endsection
