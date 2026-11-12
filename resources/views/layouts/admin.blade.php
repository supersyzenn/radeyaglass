<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex, nofollow"><title>@yield('title', 'Dashboard') · Admin Radeya</title><link rel="icon" type="image/png" href="{{ asset('images/radeya-logo.png') }}">@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="admin-body">
<a href="#main" class="skip-link">Langsung ke konten</a>
<aside class="admin-sidebar"><a class="admin-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/radeya-logo.png') }}" alt="Radeya Glass &amp; Aluminium" width="150" height="100"></a><span class="eyebrow">KELOLA TOKO</span><nav aria-label="Navigasi admin"><a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif><x-icon name="grid" /> Dashboard</a><a href="{{ url('/admin/produk') }}" @if(request()->is('admin/produk*')) aria-current="page" @endif><x-icon name="window" /> Produk</a><a href="{{ url('/admin/pengaturan') }}" @if(request()->is('admin/pengaturan')) aria-current="page" @endif><x-icon name="settings" /> Pengaturan</a></nav><div class="sidebar-bottom"><a href="{{ route('catalog.index') }}" target="_blank" rel="noopener">Lihat katalog ↗</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="logout-button" type="submit"><x-icon name="logout" /> Keluar</button></form></div></aside>
<main class="admin-main" id="main"><div class="admin-topbar"><span>Panel Admin / @yield('title', 'Dashboard')</span><div class="admin-identity"><span class="avatar">{{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }}</span></div></div><x-flash />@yield('content')</main>
</body>
</html>
