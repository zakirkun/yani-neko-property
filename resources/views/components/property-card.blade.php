@php
    $image = $property->images->firstWhere('is_primary', true) ?? $property->images->first();
    $hasImage = $image && Illuminate\Support\Facades\Storage::disk('public')->exists($image->path);
@endphp
<article class="property-card">
    <a class="card-image" href="{{ route('properties.show', $property->slug) }}" aria-label="View {{ $property->title }}">
        @if($hasImage)
            <img
                src="{{ Illuminate\Support\Facades\Storage::url($image->path) }}"
                alt="{{ $image->alt_text ?: $property->title }}"
                width="720"
                height="480"
                loading="lazy"
                decoding="async"
                sizes="(max-width: 640px) 100vw, (max-width: 960px) 50vw, 33vw"
            >
        @else
            <span class="placeholder" role="img" aria-label="Photography coming soon for {{ $property->title }}"><b>H&K</b><small>Photography coming soon</small></span>
        @endif
        <span class="purpose">For {{ ucfirst($property->purpose->value) }}</span>
    </a>
    <div class="card-body">
        <p class="card-meta"><span>{{ $property->propertyType->name }}</span><span aria-hidden="true">•</span><span>{{ $property->city }}</span></p>
        <h3><a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a></h3>
        <p class="price">${{ number_format((float) $property->price, 0) }}@if($property->purpose->value === 'rent') <small>/ month</small>@endif</p>
        <div class="facts" aria-label="Property details">
            @if($property->bedrooms > 0)<span><b>{{ $property->bedrooms }}</b> {{ Str::plural('bed', $property->bedrooms) }}</span>@endif
            @if($property->bathrooms > 0)<span><b>{{ $property->bathrooms }}</b> {{ Str::plural('bath', $property->bathrooms) }}</span>@endif
            @if($property->area)<span><b>{{ number_format((float) $property->area) }}</b> sq ft</span>@endif
        </div>
    </div>
</article>
