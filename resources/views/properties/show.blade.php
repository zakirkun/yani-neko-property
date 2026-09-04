@extends('layouts.app')
@section('title', $property->title.' | Haven & Key')
@section('description', Str::limit(strip_tags($property->description ?: $property->title.' in '.$property->city), 155))
@section('content')
<section class="property-head container">
    <a class="back-link" href="{{ route('properties.index') }}"><span aria-hidden="true">←</span> Back to properties</a>
    <div class="property-title">
        <div><p class="eyebrow">For {{ $property->purpose->value }} · {{ $property->propertyType->name }}</p><h1>{{ $property->title }}</h1><p class="property-address">{{ $property->address }}, {{ $property->city }}@if($property->state), {{ $property->state }}@endif @if($property->postcode) {{ $property->postcode }}@endif</p></div>
        <div class="property-price-block"><small>Asking price</small><p class="detail-price">${{ number_format((float) $property->price, 0) }}@if($property->purpose->value === 'rent') <small>/ month</small>@endif</p></div>
    </div>
</section>
@php
    $validImages = $property->images->filter(fn ($image) => Illuminate\Support\Facades\Storage::disk('public')->exists($image->path));
@endphp
<section class="container gallery {{ $validImages->count() < 2 ? 'single' : '' }}" aria-label="Property gallery">
    @forelse($validImages->take(5) as $image)
        <img
            class="gallery-image"
            src="{{ Illuminate\Support\Facades\Storage::url($image->path) }}"
            alt="{{ $image->alt_text ?: $property->title }}"
            width="1200"
            height="800"
            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
            decoding="async"
            @if($loop->first) fetchpriority="high" @endif
            sizes="(max-width: 640px) 100vw, (max-width: 960px) 66vw, 50vw"
        >
    @empty
        <span class="placeholder hero-placeholder" role="img" aria-label="Photography coming soon for {{ $property->title }}"><b>H&K</b><small>Photography coming soon</small></span>
    @endforelse
    @if($validImages->count() > 1)<span class="gallery-count">1 / {{ min($validImages->count(), 5) }} photos</span>@endif
</section>
<section class="section container detail-layout">
    <div class="detail-main">
        <div class="detail-facts" aria-label="Property details">
            @if($property->bedrooms > 0)<div><b>{{ $property->bedrooms }}</b><span>Bedrooms</span></div>@endif
            @if($property->bathrooms > 0)<div><b>{{ $property->bathrooms }}</b><span>Bathrooms</span></div>@endif
            @if($property->area)<div><b>{{ number_format((float) $property->area) }}</b><span>Square feet</span></div>@endif
            <div><b>{{ $property->propertyType->name }}</b><span>Property type</span></div>
        </div>
        <article class="description"><p class="eyebrow">About this property</p><h2>A closer look</h2>@if($property->description)@foreach(preg_split('/\r\n|\r|\n/', $property->description) as $paragraph)@if(trim($paragraph))<p>{{ $paragraph }}</p>@endif @endforeach @else<p>Contact our team for more information about this property.</p>@endif</article>
        @if($property->amenities->isNotEmpty())<div class="amenities"><p class="eyebrow">Features</p><h2>Everything you need</h2><ul>@foreach($property->amenities as $amenity)<li><span aria-hidden="true">✓</span>{{ $amenity->name }}</li>@endforeach</ul></div>@endif
    </div>
    <aside class="inquiry-aside">
        <div class="inquiry-card">
            @if($property->agent)
                <div class="agent-mini">
                    @php $hasAvatar = $property->agent->avatar && Illuminate\Support\Facades\Storage::disk('public')->exists($property->agent->avatar); @endphp
                    @if($hasAvatar)<img src="{{ Illuminate\Support\Facades\Storage::url($property->agent->avatar) }}" alt="{{ $property->agent->name }}" width="96" height="96" loading="lazy" decoding="async" sizes="62px">@else<span class="avatar-placeholder" aria-hidden="true">{{ Str::substr($property->agent->name, 0, 1) }}</span>@endif
                    <div><small>Your property expert</small><h3>{{ $property->agent->name }}</h3>@if($property->agent->phone)<a href="tel:{{ $property->agent->phone }}">{{ $property->agent->phone }}</a>@endif</div>
                </div>
            @endif
            <p class="eyebrow">Arrange a viewing</p><h2>Interested in this property?</h2><p>Leave your details and we will get back to you personally.</p>
            <form action="{{ route('properties.inquiries.store', $property->slug) }}" method="POST">@csrf @include('partials.inquiry-fields', ['defaultMessage' => 'I would like to know more about '.$property->title.'.'])<button class="button full" type="submit">Send inquiry <span aria-hidden="true">→</span></button></form>
            <p class="form-assurance">No pressure. Your details stay private.</p>
        </div>
    </aside>
</section>
@if($similar->isNotEmpty())
<section class="section similar"><div class="container"><div class="section-heading"><div><p class="eyebrow">You may also like</p><h2>Similar properties</h2></div><a class="text-link" href="{{ route('properties.index') }}">Explore all homes <span aria-hidden="true">→</span></a></div><div class="card-grid">@foreach($similar as $property)<x-property-card :property="$property" />@endforeach</div></div></section>
@endif
@endsection
