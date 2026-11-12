# Radeya Glass & Aluminium

Katalog produk Laravel 13, PHP 8.5, MySQL, Blade, CSS responsif, dan JavaScript native. Tema biru/navy mengikuti logo asli toko. Pemesanan dan pembayaran melalui WhatsApp.

## Fitur

- Katalog dan detail: foto, nama, keterangan, harga normal/coret, dan harga diskon opsional.
- Kontak email/WhatsApp, pesan WhatsApp berisi produk dan tautannya, serta tentang toko.
- Dashboard admin terpisah: login, CRUD produk, unggah foto, pengaturan kontak dan tentang toko.
- Tanpa menu atau tautan admin pada halaman konsumen. Pemilik membuka `/admin/login` langsung.
- Validasi harga/foto, session auth, CSRF, hash kata sandi, dan pembatasan percobaan login.
- Tidak ada akun konsumen, keranjang, checkout, payment gateway, produk demo, atau akun admin bawaan.

## Menjalankan lokal

### Mode coba tanpa akun MySQL

Konfigurasi lokal `.env` telah dialihkan ke SQLite untuk mencoba katalog tanpa login database. Tabel sudah dibuat; konfigurasi MySQL sebelumnya disimpan di `.env.backup`. Tidak ada akun admin atau produk contoh yang dibuat otomatis.

Jalankan `bin/dev` jika server belum berjalan, lalu buka `http://127.0.0.1:8000` untuk katalog publik tanpa login. Area `/admin` tetap membutuhkan akun admin. Untuk kembali ke MySQL, isi kembali `DB_CONNECTION=mysql`, `DB_DATABASE=radeyaglass`, dan kredensial MySQL yang valid sebelum menjalankan migrasi.

### Instalasi dengan MySQL

Prasyarat: **PHP 8.5**, Composer, MySQL, Node.js 20+. Aktifkan ekstensi Laravel standar serta `pdo_mysql`, `fileinfo`, `mbstring`, `openssl`, dan `dom`/`xml` untuk tes.

`bin/php` otomatis memakai PHP 8.5 Homebrew jika tersedia, tanpa mengubah PHP default sistem. Di mesin lain, script menggunakan `php` dari PATH.

1. Pasang dependensi:

   ```sh
   composer install
   ```

   Jika PHP default macOS masih 8.2:

   ```sh
   PATH="/opt/homebrew/opt/php@8.5/bin:$PATH" composer install
   ```

2. Jika `.env` belum tersedia:

   ```sh
   cp .env.example .env
   bin/php artisan key:generate
   ```

3. Buat database MySQL `radeyaglass` beserta akun database, lalu isi `.env`:

   ```dotenv
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=radeyaglass
   DB_USERNAME=radeya
   DB_PASSWORD=isi_kata_sandi_database_Anda
   ```

   Untuk socket MySQL, tambahkan `DB_SOCKET` sesuai lokasi socket Anda. Jalankan `bin/php artisan config:clear` setelah mengubah `.env`.

4. Buat tabel, pengaturan awal, dan tautan foto:

   ```sh
   bin/php artisan migrate --seed
   bin/php artisan storage:link
   ```

   Seeder hanya menyiapkan pengaturan toko, tanpa produk contoh dan tanpa akun admin bawaan.

5. Build aset. Tidak perlu `npm install` atau unduhan package frontend:

   ```sh
   npm run build
   ```

6. Buat akun admin. Kata sandi diminta melalui prompt tersembunyi, minimal 12 karakter:

   ```sh
   bin/php artisan admin:create
   ```

   Nama/email dapat diberikan lewat opsi; kata sandi tetap melalui prompt:

   ```sh
   bin/php artisan admin:create --name="Pemilik Toko" --email="email-Anda@example.com"
   ```

7. Jalankan aplikasi:

   ```sh
   bin/dev
   ```

   Katalog: `http://127.0.0.1:8000`. Admin: `http://127.0.0.1:8000/admin/login`.

   Untuk port lain, gunakan `PORT=8001 bin/dev` dan sesuaikan `APP_URL` agar tautan foto/produk benar.

8. Masuk admin, buka **Pengaturan**, isi nomor WhatsApp, alamat Gmail/email, dan tentang toko. Kemudian tambahkan produk. Kontak kosong tidak menghasilkan tautan ke nomor atau alamat palsu.

## Mengelola produk

- Foto utama wajib saat menambah produk. Saat mengedit, kosongkan input foto untuk mempertahankan foto lama.
- Foto JPG/PNG/WebP maksimal 5 MB. Nama file disusun server; SVG dan file non-gambar ditolak.
- Masukkan harga rupiah tanpa titik, koma, atau pecahan: `250000`.
- Diskon boleh kosong. Jika diisi, harus positif dan lebih rendah dari harga normal. Harga normal dicoret saat diskon tersedia.
- Penggantian/penghapusan produk membersihkan foto lama setelah perubahan database berhasil. Foto yang masih dipakai produk lain tetap disimpan.
- Mengganti nama produk tidak mengubah URL yang sudah dibagikan.

## Pemeriksaan

```sh
bin/php vendor/bin/phpunit
npm run build
bin/php artisan route:list --except-vendor
```

Tes default memakai SQLite in-memory yang terpisah dari database toko. Cakupan: akses admin, login/logout, pembatasan login, CRUD, foto, harga, slug, kontak, dan WhatsApp.

Untuk menjalankan suite terhadap **database MySQL khusus pengujian yang kosong**:

```sh
DB_CONNECTION=mysql DB_DATABASE=radeyaglass_test bin/php vendor/bin/phpunit
```

Host/akun dari `.env` atau variabel environment. `RefreshDatabase` menjalankan migrasi ulang; gunakan database tes terpisah.

## Deployment

- Hosting PHP 8.5 + MySQL. Document root diarahkan ke **`public/`**, bukan root repository.
- `composer install --no-dev --optimize-autoloader`, kemudian `npm run build` pada tahap build.
- Isi `.env` produksi: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS, dan `SESSION_SECURE_COOKIE=true`.
- Buat application key untuk instalasi baru, jalankan `bin/php artisan migrate --force`, `bin/php artisan storage:link`, dan `bin/php artisan optimize`.
- `storage/` dan `bootstrap/cache/` harus dapat ditulis. Foto `storage/app/public/products/` harus persisten dan ikut backup bersama database.
- PHP `upload_max_filesize` minimal `6M`, `post_max_size` minimal `8M`. Nonaktifkan eksekusi script pada direktori unggahan di web server.
- Buat admin melalui `admin:create`, lalu isi kontak toko. Tidak ada kredensial tetap dalam kode.

## Tampilan modern dan animasi

Storefront menggunakan tema modern premium biru/navy: judul besar, ilustrasi kaca CSS, kartu produk, dan alur pemesanan WhatsApp. Animasi masuk, scroll reveal, refleksi kaca, hover, dan header sticky dibuat tanpa library tambahan. `resources/css/storefront.css` hanya dimuat di halaman konsumen.

Konten tetap terlihat tanpa JavaScript atau IntersectionObserver. Animasi mengikuti preferensi perangkat **Reduce motion/Kurangi gerakan**, termasuk jika preferensi berubah saat halaman terbuka. Navigasi mobile dapat ditutup dengan Escape.

## SEO dan pencarian nama toko

Website menyiapkan judul/deskripsi unik, canonical URL, Open Graph, metadata berbagi, data terstruktur Organization/WebSite/Product/Breadcrumb, `/sitemap.xml`, serta `/robots.txt` dinamis. Harga schema mengikuti harga diskon yang tampil; tidak ada rating, stok, atau alamat yang dikarang.

Pengaturan ini membantu mesin pencari memahami website, tetapi **tidak menjamin peringkat pertama atau pengindeksan**. Google menyatakan hal tersebut dalam [panduan SEO](https://developers.google.com/search/docs/fundamentals/seo-starter-guide). Website `localhost` tidak dapat diindeks sebagai website publik.

Ketika domain dan hosting publik siap:

1. Isi `.env` produksi dengan domain HTTPS milik toko. Ganti contoh domain berikut dengan domain yang benar:

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-toko-anda.com
   SEO_INDEXABLE=true
   GOOGLE_SITE_VERIFICATION=
   ```

2. Jalankan `bin/php artisan config:cache` setelah mengubah konfigurasi. Buka website publik, `/robots.txt`, dan `/sitemap.xml`; sitemap harus memuat URL domain toko yang benar. Versi lokal/staging tetap memakai `SEO_INDEXABLE=false` dan noindex. Nilai true saja tidak mengaktifkan indexing jika APP_ENV bukan production atau URL masih lokal.
3. Tambahkan properti domain di [Google Search Console](https://search.google.com/search-console). Ikuti verifikasi DNS dari Google. Alternatif verifikasi URL-prefix dapat memakai token HTML di `GOOGLE_SITE_VERIFICATION`, lalu cache ulang konfigurasi.
4. Kirim sitemap domain toko melalui Search Console, lalu gunakan URL Inspection untuk memeriksa halaman utama. Pengiriman sitemap bukan jaminan index atau peringkat.
5. Lengkapi profil Google Business Profile dengan nama **Radeya Glass & Aluminium**, alamat/kota, nomor telepon, kategori, dan jam buka yang benar. Hubungkan ke domain yang sama. Gunakan ulasan pelanggan asli; jangan membuat ulasan atau rating palsu.
6. Isi produk asli, foto, harga, kontak, dan keterangan toko. Pantau pengindeksan serta pencarian nama toko melalui Search Console. Konten/alamat yang belum diberikan tetap tidak dikarang oleh aplikasi.

APP_URL menjadi sumber canonical, schema, dan sitemap sehingga tidak mengikuti Host header pengunjung. Halaman admin memiliki noindex dan tidak masuk sitemap. Robots bukan pengganti autentikasi admin.

## Status verifikasi

Suite fitur terverifikasi dengan SQLite. Versi lokal dapat dicoba tanpa login di `http://127.0.0.1:8000` ketika `bin/dev` berjalan. Server lokal yang sudah dijalankan pengguna dapat memuat desain baru; sandbox tidak dapat membuka server HTTP tambahan. Koneksi MySQL produksi belum terverifikasi.

`docs/preview/` berisi hasil render statis untuk meninjau tampilan, bukan server aplikasi dan bukan formulir yang dapat menyimpan data. Gunakan langkah lokal di atas untuk mencoba fungsi lengkap.

Dependency PHP dipasang dari cache dan dikunci di `composer.lock`. Frontend dibangun tanpa dependency npm; manifest kompatibel dengan helper aset Laravel. Rancangan dan catatan kerja tersimpan di `docs/superpowers/`.
