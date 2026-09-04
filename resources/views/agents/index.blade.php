@extends('layouts.app')
@section('title', 'Meet Our Property Agents | Haven & Key')
@section('description', 'Meet the active Haven & Key agents bringing local knowledge, careful listening and experienced property guidance.')
@section('content')
<section class="page-hero"><div class="container page-hero-inner"><div><p class="eyebrow">People first</p><h1>Meet our agents</h1><p>Experienced property professionals, here to listen and guide.</p></div><div class="page-hero-note"><span>{{ $agents->total() }}</span><small>local {{ Str::plural('expert', $agents->total()) }}</small></div></div></section>
<section class="section container">
    @if($agents->isEmpty())
        <div class="empty-state"><span aria-hidden="true">◇</span><h2>Our team page is being updated</h2><p><a href="{{ route('contact') }}">Contact us</a> and the right specialist will get back to you.</p></div>
    @else
        <div class="agent-grid">
            @foreach($agents as $agent)
                <article class="agent-card">
                    @php $hasAvatar = $agent->avatar && Illuminate\Support\Facades\Storage::disk('public')->exists($agent->avatar); @endphp
                    <div class="agent-photo">
                        @if($hasAvatar)<img src="{{ Illuminate\Support\Facades\Storage::url($agent->avatar) }}" alt="{{ $agent->name }}" width="500" height="620" loading="lazy" decoding="async" sizes="(max-width: 640px) 100vw, 190px">@else<span class="placeholder" role="img" aria-label="No portrait available for {{ $agent->name }}"><b>{{ Str::substr($agent->name, 0, 1) }}</b><small>Property advisor</small></span>@endif
                    </div>
                    <div class="agent-content"><p class="eyebrow">Property advisor</p><h2>{{ $agent->name }}</h2><p>{{ Str::limit($agent->bio ?: 'Dedicated to making every property journey clear, considered and personal.', 180) }}</p><p class="agent-listings"><strong>{{ $agent->properties_count }}</strong> published {{ Str::plural('listing', $agent->properties_count) }}</p><div class="agent-links">@if($agent->email)<a href="mailto:{{ $agent->email }}">Email {{ Str::before($agent->name, ' ') }} <span aria-hidden="true">→</span></a>@endif @if($agent->phone)<a href="tel:{{ $agent->phone }}">{{ $agent->phone }}</a>@endif</div></div>
                </article>
            @endforeach
        </div>
        @include('partials.pagination', ['paginator' => $agents])
    @endif
</section>
<section class="cta"><div class="container cta-inner"><div><p class="eyebrow light-text">Personal guidance</p><h2>Not sure who to speak to?</h2><p>Tell us about your plans and we will connect you with the right local expert.</p></div><a class="button button-light" href="{{ route('contact') }}">Contact our team <span aria-hidden="true">→</span></a></div></section>
@endsection
