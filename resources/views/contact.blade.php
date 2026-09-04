@extends('layouts.app')
@section('title', 'Contact Our Property Team | Haven & Key')
@section('description', 'Contact Haven & Key for personal help buying, renting, selling or letting a property.')
@section('content')
<section class="page-hero"><div class="container page-hero-inner"><div><p class="eyebrow">Start a conversation</p><h1>How can we help?</h1><p>Whether you are planning a move or just exploring, we would love to hear from you.</p></div><div class="page-hero-note page-hero-note-text"><span>1:1</span><small>personal advice</small></div></div></section>
<section class="section container contact-grid">
    <div class="contact-copy">
        <p class="eyebrow">Get in touch</p><h2>Real people, thoughtful advice</h2><p>Share a little about your property plans and the right member of our team will respond.</p>
        <div class="contact-details">
            <div><span aria-hidden="true">✉</span><div><small>Email us</small><a href="mailto:hello@havenandkey.com">hello@havenandkey.com</a></div></div>
            <div><span aria-hidden="true">☎</span><div><small>Call us</small><a href="tel:+15550142210">+1 (555) 014-2210</a></div></div>
            <div><span aria-hidden="true">◷</span><div><small>Office hours</small><p>Monday–Friday, 9am–6pm</p></div></div>
        </div>
        <p class="response-note"><strong>Usually replies within one working day.</strong><br>No sales pressure, just clear answers.</p>
    </div>
    <form class="contact-form" action="{{ route('contact.store') }}" method="POST">@csrf<p class="eyebrow">Send a message</p><h2>Tell us about your plans</h2><p>Fields marked * are required.</p>@include('partials.inquiry-fields')<button class="button full" type="submit">Send message <span aria-hidden="true">→</span></button><p class="form-assurance">Your details are only used to respond to your inquiry.</p></form>
</section>
@endsection
