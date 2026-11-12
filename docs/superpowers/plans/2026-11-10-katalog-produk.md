# Katalog Radeya Glass Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [x]`) syntax for tracking.

**Goal:** Katalog Radeya Glass dan Aluminium dengan kontak WhatsApp dan CRUD produk khusus admin.

**Architecture:** Satu aplikasi Laravel server-rendered. Blade menampilkan katalog publik dan layout admin terpisah; session authentication melindungi admin; MySQL menyimpan produk dan pengaturan; foto disimpan di public storage.

**Tech Stack:** Laravel 13, PHP 8.5, MySQL, Blade, Tailwind CSS/Vite, PHPUnit.

**Spec:** `docs/superpowers/specs/2026-11-10-katalog-produk-design.md`

## Global Constraints

- Antarmuka berbahasa Indonesia dan harga dalam rupiah tanpa pecahan.
- Konsumen tidak memiliki akun, keranjang, atau checkout; WhatsApp menangani pemesanan/pembayaran.
- Tidak ada menu, tautan, atau kontrol admin di halaman konsumen, termasuk saat admin sedang login.
- Gunakan logo asli `IMG_2701.PNG`, biru `#0055C7`, navy `#08245B`, putih `#FFFFFF`, latar `#F3F7FC`, teks `#20252D`.
- Foto produk JPEG/PNG/WebP maksimal 5 MB. Harga normal positif; diskon opsional positif dan lebih rendah dari harga normal.
- Jangan mengarang kontak toko atau memasang kata sandi admin tetap.
- Workspace awal bukan repository Git; simpan hasil langsung di direktori proyek dan catat kemajuan di dokumen, tanpa worktree/commit yang tidak tersedia.

## Review Focus

- Diskon kosong versus nol, sama dengan harga normal, pecahan, dan nilai terlalu besar: server menolak input tidak valid tanpa pembulatan diam-diam.
- Penggantian foto dan penghapusan produk: foto lama dibersihkan setelah perubahan tersimpan, foto lama tetap tersedia jika validasi gagal.
- Produk dengan nama sama atau nama diubah: slug unik dan URL yang sudah dibagikan tetap stabil.
- Kontak kosong atau nomor lokal: tidak membuat tautan palsu; nomor Indonesia dinormalisasi ke format internasional.
- Admin membuka halaman publik dan pengunjung mengirim request perubahan langsung: halaman publik tetap bebas kontrol admin dan operasi perubahan menuntut autentikasi.

## Peta file

- `app/Models/{Product,StoreSetting}.php`: data, harga terformat, slug, dan konfigurasi toko.
- `database/migrations/*`: tabel produk dan singleton pengaturan.
- `app/Http/Controllers/CatalogController.php`: katalog/detail/kontak/tentang.
- `app/Http/Controllers/Admin/{AuthController,ProductController,StoreSettingController}.php`: autentikasi dan aksi admin.
- `app/Http/Requests/{ProductRequest,StoreSettingRequest}.php`: validasi input server.
- `app/Console/Commands/CreateAdmin.php`: bootstrap admin interaktif tanpa pendaftaran publik.
- `resources/views/layouts/{store,admin}.blade.php`: layout terpisah.
- `resources/views/{catalog,admin,components}/`: halaman dan komponen.
- `resources/css/app.css`, `resources/js/app.js`: tema responsif, navigasi mobile, pratinjau foto, konfirmasi hapus.
- `public/images/radeya-logo.png`: salinan logo pengguna.
- `tests/Feature/{Catalog,AdminAuth,ProductManagement,StoreSettings}Test.php`: regresi fitur dan keamanan.
- `README.md`, `.env.example`, `bin/php`, `bin/dev`: instruksi dan runtime lokal.

## Task 1: Katalog publik dan fondasi data

**Interfaces:** `Product` memiliki `name`, `slug`, `description`, `image_path`, `price`, `discount_price`; `StoreSetting::current()` memberikan pengaturan tunggal dan `whatsappUrl(?Product $product = null): ?string`. Route `catalog.index`, `catalog.show`, `store.contact`, `store.about`.

- [x] Scaffold Laravel tanpa mengganti logo/dokumen yang sudah ada; install dependency dan siapkan Vite/Tailwind.
- [x] Tulis `CatalogTest`: homepage 200 dan nama toko; detail harga 250000/199000 menghasilkan harga coret dan diskon; produk tidak ada 404; kontak kosong tidak menghasilkan `wa.me/`; nama kembar menghasilkan slug berbeda; perubahan nama mempertahankan slug; kontak terkonfigurasi mengodekan produk dan URL dengan benar; layout publik tanpa `/admin` juga saat authenticated.
- [x] Jalankan `bin/php artisan test --filter=CatalogTest`; konfirmasi gagal karena fitur belum tersedia.
- [x] Implementasikan migrasi/model/controller publik/routes/views, tema biru/navy, aset logo, keadaan kosong, pagination, dan konfigurasi toko awal yang netral.
- [x] Jalankan `bin/php artisan test --filter=CatalogTest`; harapkan semua lulus. Catat hasil.

## Task 2: Login dan pembuatan admin

**Consumes:** Laravel `User`, layout publik Task 1. **Produces:** route `admin.login`, `admin.login.store`, `admin.logout`, `admin.dashboard`; middleware session `auth`; command `admin:create`.

- [x] Tulis `AdminAuthTest`: tamu dialihkan login; kredensial benar masuk; salah ditolak dengan pesan Indonesia; logout menghilangkan akses; login berulang dibatasi; tidak ada route registrasi publik; perintah membuat akun berhash dan menolak email duplikat.
- [x] Jalankan `bin/php artisan test --filter=AdminAuthTest`; konfirmasi gagal untuk autentikasi yang belum tersedia.
- [x] Implementasikan autentikasi session dengan regenerate/invalidate, rate limiting, command admin interaktif, dan layout dashboard yang tidak dipakai di halaman publik.
- [x] Jalankan `bin/php artisan test`; harapkan semua lulus. Catat hasil.

## Task 3: CRUD produk dan pengaturan toko

**Consumes:** model dan auth Task 1–2. **Produces:** resource `admin.products.*` (tanpa show), `admin.settings.edit/update`; `ProductRequest` dan `StoreSettingRequest`.

- [x] Tulis `ProductManagementTest`: tamu tidak dapat POST/PATCH/DELETE; admin dapat membuat dengan foto, mengedit tanpa foto, mengganti foto (lama dibersihkan), menghapus (foto dibersihkan), dan melihat form. Payload invalid (diskon sama/lebih/0/pecahan/terlalu besar, file SVG/terlalu besar/bukan gambar) ditolak dan tidak mengubah produk atau foto lama.
- [x] Tulis `StoreSettingsTest`: kontak/tentang diperbarui dan terlihat publik, nomor `08...` menjadi `628...`, email/nomor invalid ditolak, kontak kosong diizinkan, tamu ditolak.
- [x] Jalankan `bin/php artisan test --filter='ProductManagementTest|StoreSettingsTest'`; konfirmasi gagal sebelum CRUD tersedia.
- [x] Implementasikan controller/request CRUD dan settings, form berbahasa Indonesia, validasi batas angka/input, disk local public, cleanup foto setelah commit, flash status, konfirmasi hapus, dan pratinjau unggahan.
- [x] Jalankan `bin/php artisan test`; harapkan semua lulus. Catat hasil.

## Task 4: Jalankan dan verifikasi aplikasi

**Consumes:** aplikasi Task 1–3. **Produces:** build produksi, dokumentasi, dan hasil review.

- [x] Siapkan `.env.example` MySQL dan README langkah instalasi/runtime/admin/storage/build. Jangan commit rahasia atau memasang akun default.
- [x] Jalankan `bin/php artisan test`, `npm run build`, pemeriksaan route, dan validasi konfigurasi deployment PHP.
- [x] Coba MySQL terisolasi di workspace jika service sistem tidak tersedia; verifikasi migrasi dan alur data melalui MySQL tanpa mengubah service global. Bila sandbox mencegah proses server, laporkan batas verifikasi secara tepat.
- [ ] QA visual desktop/mobile dan interaksi browser langsung: terhambat sandbox HTTP dan screenshot CUA kosong. Render kernel dan tes fitur telah diperiksa; pemeriksaan visual tetap terbuka.
- [x] Minta satu reviewer independen meninjau seluruh implementasi terhadap spec/plan. Perbaiki temuan penting dengan tes regresi RED→GREEN dan suite hijau.
- [x] Perbarui ledger dan kriteria penerimaan, lalu serahkan URL preview serta instruksi membuat akun admin.

## Keputusan eksekusi

Pengguna telah menyetujui rancangan dan meminta “lakukan”. Asisten mengeksekusi langsung di sesi ini tanpa meminta persetujuan tambahan terhadap rincian teknis. Gunakan satu reviewer independen di akhir sebagaimana skill executing-plans, tanpa delegasi implementasi per tugas.

## Hasil akhir

35 tes lulus /196 assertions pada SQLite; MySQL dan live HTTP terhambat lingkungan. Hasil render statis tersedia, tetapi QA visual desktop/mobile belum terverifikasi karena screenshot browser kosong. Dua minor hasil review tercatat dalam ledger untuk tindak lanjut. Tidak ada commit karena workspace tidak berupa repository Git.
