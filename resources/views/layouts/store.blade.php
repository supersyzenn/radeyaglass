<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-seo :seo="$seo" />
    <meta name="theme-color" content="#08245B">
    <link rel="icon" type="image/png" href="{{ asset('images/radeya-logo.png') }}">
    @vite(['resources/css/app.css', 'resources/css/storefront.css', 'resources/js/app.js'])
</head>
<body class="storefront">
<a href="#main" class="skip-link">Langsung ke konten</a>
<header class="store-header" data-store-header>
    <div class="container header-inner">
        <a class="brand" href="{{ route('catalog.index') }}" aria-label="{{ $store->name }} — Beranda">
            <img src="{{ asset('images/radeya-logo.png') }}" alt="Logo {{ $store->name }}" width="112" height="75" fetchpriority="high">
            <span>{{ $store->name }}<small>KACA &amp; ALUMINIUM</small></span>
        </a>
        <button class="menu-toggle" type="button" data-menu-toggle aria-expanded="false" aria-controls="store-navigation" aria-label="Buka navigasi"><x-icon name="menu" /></button>
        <nav id="store-navigation" class="store-nav" aria-label="Navigasi utama">
            <a href="{{ route('catalog.index') }}" @if(request()->routeIs('catalog.*')) aria-current="page" @endif>Produk</a>
            <a href="{{ route('store.about') }}" @if(request()->routeIs('store.about')) aria-current="page" @endif>Tentang Toko</a>
            <a href="{{ route('store.contact') }}" @if(request()->routeIs('store.contact')) aria-current="page" @endif>Kontak</a>
            <a class="button button-small" href="{{ $store->whatsappUrl() ?? route('store.contact') }}" @if($store->whatsappUrl()) target="_blank" rel="noopener noreferrer" @endif><x-icon name="whatsapp" /> Mari Terhubung <span class="button-arrow">↗</span></a>
        </nav>
    </div>
</header>
<main id="main" tabindex="-1">@yield('content')</main>
<section class="contact-cta">
    <div class="container">
        <div class="contact-cta-card" data-reveal>
            <div class="cta-decoration" aria-hidden="true"><x-icon name="window" /></div>
            <div class="cta-copy"><span class="eyebrow">DARI IDE, MENJADI PILIHAN</span><h2>Ruang Anda berikutnya,<br>dimulai dari percakapan.</h2><p>Mari bicarakan kebutuhan kaca dan aluminium Anda.</p></div>
            <a class="button button-white" href="{{ $store->whatsappUrl() ?? route('store.contact') }}" @if($store->whatsappUrl()) target="_blank" rel="noopener noreferrer" @endif><x-icon name="whatsapp" /> Hubungi Toko <span class="button-arrow">↗</span></a>
        </div>
    </div>
</section>
<footer class="store-footer">
    <div class="container footer-top">
        <div class="footer-brand"><span class="eyebrow blue">KACA. ALUMINIUM. KARAKTER.</span><strong>{{ $store->name }}</strong><p>Temukan detail yang melengkapi ruang Anda.</p></div>
        <nav aria-label="Navigasi footer"><a href="{{ route('catalog.index') }}">Produk <span>↗</span></a><a href="{{ route('store.about') }}">Tentang Toko <span>↗</span></a><a href="{{ route('store.contact') }}">Kontak <span>↗</span></a></nav>
    </div>
    <div class="container footer-bottom"><span>© {{ date('Y') }} {{ $store->name }}.</span><span>Kualitas Terbaik · Desain Modern · Tahan Lama</span><a href="#main">Kembali ke atas ↑</a></div>
</footer>
<a class="floating-contact" href="{{ $store->whatsappUrl() ?? route('store.contact') }}" @if($store->whatsappUrl()) target="_blank" rel="noopener noreferrer" @endif aria-label="Hubungi {{ $store->name }}"><x-icon name="whatsapp" /></a>
</body>
</html>
