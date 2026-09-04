<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#173f36">
    <title>@yield('title', 'Haven & Key Property')</title>
    <meta name="description" content="@yield('description', 'Discover exceptional homes and trusted property advice with Haven & Key Property.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <script>document.documentElement.classList.add('js')</script>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <script src="{{ asset('js/site.js') }}" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>
<header class="site-header" data-site-header>
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="Haven and Key home">
            <span aria-hidden="true">H&K</span>
            <strong>Haven & Key</strong>
        </a>
        <button class="nav-toggle" type="button" aria-label="Open navigation" aria-controls="site-nav" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <nav class="nav" id="site-nav" aria-label="Main navigation">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a class="{{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index') }}" @if(request()->routeIs('properties.*')) aria-current="page" @endif>Properties</a>
            <a class="{{ request()->routeIs('agents.*') ? 'active' : '' }}" href="{{ route('agents.index') }}" @if(request()->routeIs('agents.*')) aria-current="page" @endif>Our agents</a>
            <a class="{{ request()->routeIs('contact*') ? 'active' : '' }}" href="{{ route('contact') }}" @if(request()->routeIs('contact*')) aria-current="page" @endif>Contact</a>
            <a class="button button-small nav-cta" href="{{ route('contact') }}">Talk to an agent</a>
        </nav>
    </div>
</header>
<button class="nav-backdrop" type="button" tabindex="-1" aria-hidden="true" aria-label="Close navigation"></button>
<main id="main-content" tabindex="-1">
    @if(session('success'))
        <div class="container alert-wrap">
            <div class="alert success" role="status">
                <span aria-hidden="true">✓</span>
                <div><strong>Message sent</strong><br>{{ session('success') }}</div>
            </div>
        </div>
    @endif
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-about">
            <a class="brand light" href="{{ route('home') }}"><span aria-hidden="true">H&K</span><strong>Haven & Key</strong></a>
            <p>Thoughtful property guidance and remarkable homes, matched to the way you want to live.</p>
        </div>
        <div>
            <h3>Explore</h3>
            <a href="{{ route('properties.index') }}">Properties</a>
            <a href="{{ route('agents.index') }}">Our agents</a>
            <a href="{{ route('contact') }}">Contact us</a>
        </div>
        <div>
            <h3>Property</h3>
            <a href="{{ route('properties.index', ['purpose' => 'sale']) }}">Homes for sale</a>
            <a href="{{ route('properties.index', ['purpose' => 'rent']) }}">Homes to rent</a>
            <a href="{{ route('contact') }}">List with us</a>
        </div>
        <div>
            <h3>Get in touch</h3>
            <a href="mailto:hello@havenandkey.com">hello@havenandkey.com</a>
            <a href="tel:+15550142210">+1 (555) 014-2210</a>
            <p>Mon–Fri, 9am–6pm</p>
        </div>
    </div>
    <div class="container copyright">
        <span>&copy; {{ date('Y') }} Haven & Key Property.</span>
        <span>Exceptional homes, personal service.</span>
    </div>
</footer>
</body>
</html>
