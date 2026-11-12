# Modern Catalog and SEO Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Tampilan modern premium dengan animasi ringan serta fondasi SEO katalog Radeya Glass & Aluminium.

**Architecture:** Pertahankan Laravel/Blade dan build Node tanpa dependency. Metadata server-rendered melalui `App\Support\Seo`; controller khusus mengirim sitemap XML dan robots dinamis. CSS storefront terpisah dari dashboard, dengan gerakan progresif yang menghormati reduced motion.

**Tech Stack:** Laravel 13/PHP 8.5, Blade, CSS, JavaScript native, PHPUnit.

**Spec:** Permintaan pengguna: desain lebih modern, beranimasi, dan optimasi pencarian nama toko. Pengguna memilih “langsung gunakan rekomendasi”, sehingga desain dijalankan langsung tanpa pertanyaan tambahan. Katalog tanpa login, admin terpisah, SQLite lokal, serta logo asli tetap berlaku sesuai rancangan `docs/superpowers/specs/2026-11-10-katalog-produk-design.md`.

## Global Constraints

- Nama: Radeya Glass & Aluminium; logo asli, palet biru/navy, konten Indonesia.
- Konten bisa dibaca tanpa JavaScript; animasi tidak memblokir katalog dan dinonaktifkan saat reduced motion.
- Jangan mengarang produk, alamat, kota, testimoni, rating, jam buka, atau stok.
- Tidak menambahkan tautan admin pada halaman konsumen.
- SEO tidak menjanjikan peringkat pertama; domain/hosting, Search Console dan profil bisnis belum tersedia.
- Local/staging memakai noindex. Pengindeksan hanya aktif dengan APP_ENV=production, SEO_INDEXABLE=true dan APP_URL publik yang valid.
- Canonical/schema/sitemap memakai APP_URL, bukan Host header dari request.
- Data terstruktur hanya menyatakan informasi yang tersedia. Alamat belum diketahui: gunakan Organization/WebSite, bukan LocalBusiness palsu.

## Review Focus

- Metadata produk mengandung teks berbahaya: JSON-LD tetap valid tanpa menutup tag script.
- Query tracking dan Host palsu tidak mengubah canonical; pagination memiliki canonical tersendiri.
- Diskon kosong/aktif menghasilkan harga schema yang sama dengan harga pada tampilan.
- Metadata nonproduksi dan robots tidak mengizinkan indexing walau APP_URL menyerupai domain produksi.
- Konten tetap terlihat bila JavaScript/IntersectionObserver tidak tersedia atau reduced motion aktif.

## Task 1: SEO server-rendered

**Files:** `config/seo.php`, `app/Support/Seo.php`, `app/Http/Controllers/SeoController.php`, `app/Http/Controllers/CatalogController.php`, `resources/views/components/seo.blade.php`, `resources/views/layouts/store.blade.php`, `routes/web.php`, `public/robots.txt`, `tests/Feature/SeoTest.php`.

**Interfaces:** `Seo::page(string $page, StoreSetting $store, ?Product $product = null, int $number = 1): array`, `Seo::baseUrl(): string`, `Seo::indexable(): bool`; controller passes `$seo` to public views. `seo.sitemap` and `seo.robots` routes are public infrastructure, not nav links.

- [x] Write failing SeoTest assertions for parsed titles/descriptions/canonical/schema, local versus production indexability, sitemap XML URLs and exclusion of admin, product price, hostile content and pagination.
- [x] Run `bin/php vendor/bin/phpunit --filter=SeoTest --no-progress`; verify missing metadata/routes fail.
- [x] Implement server metadata, script-safe JSON-LD, robots and streaming sitemap. Remove static robots.txt so web requests reach the controller.
- [x] Run the complete PHPUnit suite and inspect output.

## Task 2: Modern storefront and motion

**Files:** `resources/css/storefront.css`, `resources/views/layouts/store.blade.php`, `resources/views/catalog/*.blade.php`, `resources/js/app.js`, `scripts/build-assets.mjs`.

**Interfaces:** Data attributes `data-reveal`, `data-reveal-group`, `data-store-header`; CSS scoped under `.storefront`; separate versioned `storefront.css` entry only loaded by store layout. Existing admin forms retain their style and behavior.

- [x] Replace homepage hero with brand-focused typography, CSS glass scene, clear catalog CTA, collection area, and an explanation of the WhatsApp ordering flow without invented claims.
- [x] Restyle public header/footer/product/contact/about/detail; preserve all existing links and actual prices. Mobile header and navigation must fit narrow screens.
- [x] Add progressive reveal, hover interactions, subtle hero motion, sticky header state and keyboard-accessible mobile navigation. Do not hide content unless an observer is successfully installed. Handle live reduced-motion changes.
- [x] Build assets and render all public pages using actual local SQLite configuration. Inspect layout in the available browser; report any screenshot limitations.

## Task 3: Handoff and SEO activation guidance

**Files:** `.env.example`, `README.md`, `docs/superpowers/plans/2026-11-10-modern-design-seo-progress.md`.

- [x] Document production APP_URL/SEO_INDEXABLE, Search Console verification, sitemap submission, and genuine Google Business Profile details; make clear localhost and readiness do not establish rank.
- [x] Run PHPUnit, asset build, syntax checks, Blade compilation and kernel route checks; do not overwrite local SQLite config or product data.
- [x] One independent read-only code review per requesting-code-review. Fix important defects with regression tests and a green suite.
- [x] Update local preview and report delivered changes, verification, and remaining public-domain setup.

## Verification

43 tests / 230 assertions passed, 3 asset entries built, JS syntax and Blade compilation passed. Actual local SQLite rendered public routes with HTTP 200 and admin with login redirect. Chrome reloaded the new SEO title at localhost; full visual/mobile screenshot verification was unavailable. The static preview has the latest CSS/JS. No domain was published or submitted to Google.
