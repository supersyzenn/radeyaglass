@extends('layouts.store')
@section('content')
<section class="hero hero-premium">
    <div class="hero-grain" aria-hidden="true"></div>
    <div class="container hero-inner">
        <div class="hero-copy">
            <div class="hero-kicker"><span class="status-dot"></span> DETAIL YANG MEMBENTUK RUANG</div>
            <h1>{{ $store->name }}</h1>
            <p class="hero-statement">Lebih terang.<br>Lebih <em>berkarakter.</em></p>
            <p class="hero-description">Pilihan kaca dan aluminium untuk melengkapi ruang Anda. Kenali produknya, temukan detailnya, lalu bicarakan kebutuhan Anda bersama kami.</p>
            <div class="hero-actions"><a class="button" href="#produk">Jelajahi Produk <span class="button-arrow"><x-icon /></span></a><a class="hero-secondary" href="{{ route('store.about') }}">Cerita tentang kami <span>↗</span></a></div>
            <div class="hero-note"><x-icon name="whatsapp" /><span>Konsultasi &amp; pemesanan langsung melalui WhatsApp</span></div>
        </div>
        <div class="hero-scene" aria-hidden="true">
            <div class="scene-orb"></div><div class="scene-grid"></div>
            <span class="scene-label">01 — MATERIAL &amp; RUANG</span>
            <div class="scene-plinth"></div><div class="scene-back-panel"></div>
            <div class="scene-window"><span class="glass-pane"></span><span class="glass-pane"></span><span class="glass-pane"></span><span class="glass-pane"></span><span class="glass-handle"></span><span class="glass-reflection"></span></div>
            <div class="scene-brand-card"><img src="{{ asset('images/radeya-logo.png') }}" alt="" width="210" height="140"><span>DETAIL KECIL. KESAN BERBEDA.</span></div>
            <span class="scene-side-label">KACA · CAHAYA · RUANG</span>
            <div class="scene-coordinate"><span>+</span> SEBUAH PERSPEKTIF BARU</div>
        </div>
    </div>
    <div class="container hero-bottom"><span>JELAJAHI KATALOG</span><a href="#produk" aria-label="Gulir ke koleksi produk">↓</a><span>DIRANCANG UNTUK DILIHAT LEBIH DEKAT</span></div>
</section>
<div class="material-ribbon" aria-hidden="true"><div class="ribbon-track"><span>KACA <i>✦</i> ALUMINIUM <i>✦</i> RUANG <i>✦</i> KARAKTER <i>✦</i></span><span>KACA <i>✦</i> ALUMINIUM <i>✦</i> RUANG <i>✦</i> KARAKTER <i>✦</i></span></div></div>
<section class="section container collection-section" id="produk">
    <div class="section-heading" data-reveal><div><span class="eyebrow blue"><span class="small-line"></span> KATALOG PILIHAN</span><h2>Temukan detail<br>untuk ruang Anda.</h2><p>Foto, informasi, dan harga. Semua dalam satu tempat.</p></div><div class="collection-aside"><span class="product-count">{{ $products->total() }} produk</span><p>Klik produk untuk<br>melihat lebih dekat.</p></div></div>
    @if($products->isEmpty())
        <div class="empty-catalog" data-reveal><div class="empty-blueprint" aria-hidden="true"><span></span><span></span><span></span><span></span></div><div><span class="eyebrow blue">SESUATU YANG BARU AKAN HADIR</span><h3>Koleksi sedang dipersiapkan.</h3><p>Untuk sementara, ceritakan kebutuhan Anda kepada kami.<br>Kami bisa memulai dari percakapan.</p><a class="button button-outline" href="{{ route('store.contact') }}">Hubungi Toko <x-icon /></a></div><span class="empty-corner" aria-hidden="true">↗</span></div>
    @else
        <div class="product-grid" data-reveal-group>
            @foreach($products as $product)
            <article class="product-card" data-reveal>
                <a class="product-image" href="{{ route('catalog.show', $product->slug) }}"><img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" loading="lazy" decoding="async" width="600" height="480">@if($product->discount_price !== null)<span class="discount-label">Harga spesial</span>@endif<span class="image-arrow"><x-icon /></span></a>
                <div class="product-info"><span class="product-eyebrow">KATALOG RADEYA</span><h3><a href="{{ route('catalog.show', $product->slug) }}">{{ $product->name }}</a></h3><p>{{ Str::limit($product->description, 90) }}</p><x-price :product="$product" /><a class="product-cta" href="{{ $store->whatsappUrl($product) ?? route('store.contact') }}" @if($store->whatsappUrl()) target="_blank" rel="noopener noreferrer" @endif><x-icon name="whatsapp" /> Tanya Produk <x-icon /></a></div>
            </article>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $products->fragment('produk')->links('components.pagination') }}</div>
    @endif
</section>
<section class="steps-section"><div class="container"><div class="section-heading" data-reveal><div><span class="eyebrow blue">SEDERHANA, DARI AWAL</span><h2>Dari pilihan,<br>menuju percakapan.</h2></div><p class="section-side-copy">Kami membuat langkahnya sederhana.<br>Anda cukup memulai dari kebutuhan Anda.</p></div><div class="steps-grid" data-reveal-group>
    <article class="step-card" data-reveal><div class="step-top"><span>01</span><x-icon name="grid" /></div><h3>Temukan pilihan.</h3><p>Jelajahi katalog dan lihat foto, keterangan, serta harga produk yang Anda minati.</p></article>
    <article class="step-card" data-reveal><div class="step-top"><span>02</span><x-icon name="whatsapp" /></div><h3>Bicarakan detailnya.</h3><p>Tekan tombol WhatsApp untuk bertanya tentang produk dan mendiskusikan kebutuhan Anda.</p></article>
    <article class="step-card" data-reveal><div class="step-top"><span>03</span><x-icon name="check" /></div><h3>Lanjutkan bersama toko.</h3><p>Sepakati detail pemesanan dan pembayaran langsung dengan toko melalui WhatsApp.</p></article>
</div></div></section>
@endsection
