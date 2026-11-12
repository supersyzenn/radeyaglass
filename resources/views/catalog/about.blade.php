@extends('layouts.store')
@section('title', 'Tentang Toko')
@section('content')
<div class="page-intro" data-reveal><div class="container"><span class="eyebrow blue">KENALI KAMI LEBIH DEKAT</span><h1>Tentang Radeya.</h1><p>Kaca dan aluminium untuk melengkapi ruang Anda.</p></div></div>
<section class="container section about-grid"><div class="about-brand" data-reveal><img src="{{ asset('images/radeya-logo.png') }}" alt="{{ $store->name }}" width="540" height="360"></div><div class="about-content" data-reveal><span class="eyebrow blue">{{ $store->name }}</span><h2>Berawal dari kebutuhan.<br>Temukan pilihan Anda.</h2><p class="preserve-lines">{{ $store->about ?: 'Selamat datang di katalog Radeya Glass & Aluminium. Jelajahi produk yang tersedia, lihat informasi dan harganya, lalu hubungi toko untuk mendiskusikan kebutuhan Anda.' }}</p><a class="button" href="{{ route('catalog.index') }}#produk">Lihat Katalog Produk <x-icon /></a></div></section>
@endsection
