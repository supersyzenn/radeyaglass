# Rancangan katalog produk toko

Status: disetujui pengguna untuk implementasi melalui instruksi “lakukan”.

## Tujuan dan kebutuhan yang disampaikan pengguna

Membuat website katalog agar pengunjung dapat melihat produk dan menghubungi toko. Pemesanan dan pembayaran berlangsung di WhatsApp. Admin memiliki akun untuk menambah, melihat, mengedit, dan menghapus produk beserta foto, nama, keterangan, harga normal, dan harga diskon. Pengunjung juga dapat melihat kontak Gmail/WhatsApp dan informasi tentang toko.

Pengguna memilih rekomendasi teknologi Laravel + MySQL. Pengguna meminta logo toko digunakan dan warna serta tema website mengikuti logo tersebut. Aset `IMG_2701.PNG` sudah ditemukan di root proyek dan diperiksa secara visual: logo bertuliskan “Radeya Glass dan Aluminium”, bergambar kaca/aluminium dan atap, dengan biru, navy, putih, serta hitam/abu-abu. Gunakan aset ini tanpa menggambar ulang logonya.

Pengguna juga menegaskan tampilan admin tidak ditampilkan di tampilan konsumen. Area publik tidak memiliki menu, tombol, tautan login admin, maupun kontrol pengelolaan produk. Admin masuk melalui alamat terpisah yang didokumentasikan untuk pemilik.

## Batas cakupan

- Katalog publik, detail produk, kontak, tentang toko, dan dashboard admin.
- Tidak ada akun konsumen, keranjang, checkout, payment gateway, atau pencatatan transaksi. Tombol WhatsApp membuka percakapan; website tidak menganggap pesanan sudah dibuat atau pembayaran sudah diterima.
- Stok, kategori, pengiriman, ulasan, multi-toko, dan galeri banyak foto tidak termasuk versi pertama.
- Data produk dan foto harus tersimpan di server/database, bukan hanya di browser.
- Akun admin dan operasi perubahan data wajib dilindungi di server.

## Pendekatan yang dipertimbangkan

1. **PHP + Laravel + MySQL, Blade + Tailwind CSS. Dipilih pengguna atas rekomendasi asisten.** Satu aplikasi menangani tampilan, session login, validasi form, penyimpanan foto, dan database; sesuai kebutuhan katalog dan CRUD.
2. **TypeScript + Next.js + PostgreSQL.** Layak untuk pendekatan React, dengan tambahan pilihan dan konfigurasi autentikasi serta penyimpanan foto. Tidak dipilih karena pendekatan pertama lebih langsung untuk cakupan ini.

Tidak diperlukan aplikasi frontend terpisah atau API publik untuk versi pertama. Gunakan halaman Blade dan form Laravel. Livewire bukan kewajiban.

## Kondisi lingkungan dan pilihan teknis asisten

- Direktori awal kosong dan belum berupa repository Git.
- PHP default adalah 8.2, tetapi PHP 8.5.10 tersedia di `/opt/homebrew/opt/php@8.5/bin/php`, dengan driver PDO MySQL. Composer dan klien MySQL tersedia; koneksi server MySQL belum diperiksa.
- Gunakan Laravel 13 dan PHP 8.5 yang sudah tersedia, tanpa mengubah PHP default sistem. Jalankan Composer/Artisan dengan runtime yang kompatibel. Laravel 13 mendukung PHP 8.3–8.5 menurut [catatan rilis resmi](https://laravel.com/framework/docs/12.x/releases).
- MySQL menjadi database aplikasi. Pengujian terisolasi boleh memakai SQLite untuk perilaku umum, disertai verifikasi migrasi dan alur utama dengan MySQL sebelum menyatakan dukungan MySQL terverifikasi.
- Tahap implementasi awal berjalan lokal di workspace ini. Publikasi memerlukan hosting PHP + MySQL; target hosting belum ditentukan. Laravel tidak dipaketkan sebagai website statis atau Cloudflare Worker Sites.
- Antarmuka berbahasa Indonesia dan harga dalam rupiah tanpa pecahan.

## Pengalaman konsumen

- `/` menampilkan katalog, navigasi Produk, Kontak, dan Tentang Toko. Produk terbaru muncul lebih dulu, dengan pagination agar daftar tetap ringan saat bertambah.
- Setiap kartu menampilkan foto, nama, harga berlaku, tautan detail, dan tombol WhatsApp terkait produk.
- `/produk/{slug}` menampilkan foto utama, nama, keterangan, harga normal/diskon, dan tombol bertanya atau memesan lewat WhatsApp.
- Jika harga diskon kosong, hanya harga normal ditampilkan. Jika diskon valid, harga normal dicoret dan harga diskon menjadi harga utama. Contoh: harga normal Rp250.000 dicoret, harga diskon Rp199.000.
- Pesan WhatsApp memuat nama produk dan tautan detail, misalnya: “Halo, saya tertarik dengan produk [nama]. [tautan]”. Encode pesan dan gunakan nomor toko dalam format internasional pada `wa.me`.
- `/kontak` menyediakan tautan email `mailto:` dan WhatsApp yang benar-benar terisi dari pengaturan toko. Pengiriman email otomatis dari server tidak termasuk cakupan.
- `/tentang` menampilkan nama dan keterangan toko.
- Tampilkan keadaan katalog kosong yang jelas serta halaman 404 untuk produk yang sudah dihapus atau tidak ada.

## Pengalaman admin

- `/admin/login` menyediakan login email dan kata sandi; tidak ada pendaftaran publik. Logout mengakhiri session dengan benar. URL ini tidak ditautkan dari header, footer, atau konten halaman konsumen.
- Area admin menggunakan layout dashboard tersendiri. Form edit/tambah/hapus, navigasi admin, dan status login admin tidak disisipkan di layout publik, termasuk ketika admin sedang membuka halaman konsumen.
- Semua pengguna di tabel akun merupakan admin yang dibuat melalui perintah lokal, bukan akun konsumen. Pembuatan admin pertama melalui perintah Artisan interaktif; tidak ada kata sandi admin bawaan yang tetap atau disimpan di repository.
- Dashboard `/admin` menyediakan daftar produk dengan tautan tambah dan edit. Penghapusan meminta konfirmasi.
- Form produk: nama, keterangan, foto utama, harga normal, dan harga diskon opsional. Foto wajib saat membuat produk dan opsional saat mengedit agar foto lama tetap dapat digunakan.
- Pilihan asisten yang perlu tercakup saat peninjauan: halaman pengaturan toko agar admin dapat mengubah nama toko, alamat Gmail/email, nomor WhatsApp, serta teks tentang toko. Logo awal berasal dari pengguna; unggah ulang logo melalui dashboard tidak termasuk versi pertama.
- Pesan validasi terlihat di dekat input; pengisian yang gagal tidak menghilangkan teks yang sudah diketik. Berikan notifikasi saat produk berhasil ditambah, diperbarui, atau dihapus.

## Data dan aturan validasi

- `users`: nama, email unik, hash kata sandi, serta metadata session standar Laravel.
- `products`: id, nama, slug unik, keterangan teks biasa, lokasi foto, harga normal, harga diskon nullable, dan timestamps.
- `store_settings`: satu konfigurasi toko berisi nama, email, nomor WhatsApp, serta keterangan tentang toko; logo merupakan aset proyek.
- Harga disimpan sebagai nilai integer rupiah atau decimal tanpa pecahan, tidak dihitung memakai floating point. Harga normal harus positif. Diskon, jika diisi, harus positif dan lebih rendah dari harga normal. Berlakukan aturan di server.
- Nama produk wajib dengan batas panjang; slug dibuat otomatis dan tetap stabil saat nama diperbarui. Slug harus unik.
- Validasi isi foto sebagai JPEG, PNG, atau WebP, maksimum 5 MB. Gunakan nama file yang dibuat server dan simpan hanya sebagai aset publik non-eksekusi. SVG tidak diterima sebagai foto unggahan admin.
- Simpan metadata setelah unggah berhasil. Saat mengganti atau menghapus produk, bersihkan foto lama setelah perubahan database berhasil. Jangan menghapus file yang masih digunakan atau aset logo.
- Escape nama dan keterangan saat ditampilkan. Lindungi perubahan data dengan middleware autentikasi dan CSRF. Batasi percobaan login, regenerate session setelah login, dan hash kata sandi menggunakan fasilitas Laravel.
- Validasi email dan normalisasi nomor WhatsApp sebelum penyimpanan. Jika kontak belum dikonfigurasi, jangan membuat tautan ke nomor/alamat palsu; tampilkan keterangan kontak belum tersedia.

## Identitas visual dan konten

- Gunakan logo asli `IMG_2701.PNG` dan nama “Radeya Glass & Aluminium”. Pertahankan rasio gambar dan jangan memotong tulisan pada logo.
- Pilihan asisten berdasarkan logo: warna utama biru `#0055C7`, navy `#08245B`, latar putih `#FFFFFF`, latar sekunder biru sangat muda `#F3F7FC`, dan teks gelap `#20252D`. Detail nuansa dapat disesuaikan untuk kontras saat implementasi.
- Tema bersih dan modern, sesuai identitas kaca dan aluminium. Gunakan ruang yang cukup di sekitar foto, harga yang jelas, serta tombol WhatsApp yang mudah ditemukan. Hindari klaim tentang layanan, lokasi, pengalaman, atau sertifikasi yang belum diberikan.
- Layout responsif, foto produk dominan, harga terbaca jelas, fokus keyboard terlihat, dan kontras teks memadai. Pertahankan warna merek sambil memilih pasangan warna teks/latar yang terbaca.
- Nama toko bersumber dari logo; detail katalog, alamat Gmail, nomor WhatsApp, dan keterangan bisnis tetap menunggu pengisian melalui pengaturan admin. Jangan mengarang alamat atau kontak.
- Jika konten asli belum diberikan, tampilkan placeholder yang jelas. Data demo hanya lewat seeder opsional, bukan produk palsu yang otomatis dianggap sebagai katalog nyata.
- Pilihan detail layout, tipografi, komponen, model/controller, dan susunan file diserahkan kepada implementer setelah rancangan disetujui.

## Kriteria penerimaan

1. Pengunjung dapat membuka katalog, detail, kontak, dan tentang toko dari HP maupun desktop.
2. Harga normal dan harga diskon ditampilkan sesuai aturan; diskon tidak wajib.
3. Tombol WhatsApp menuju nomor toko dan membawa produk serta tautan yang sesuai.
4. Admin dapat login, menambah produk dengan foto, mengedit informasi/foto, dan menghapus produk; perubahan bertahan setelah halaman dimuat ulang.
5. Pengunjung tanpa login tidak dapat mengakses dashboard atau mengubah produk, termasuk melalui request langsung.
6. Harga tidak valid dan file yang tidak didukung ditolak di server dengan pesan yang jelas.
7. Logo yang diberikan digunakan dan warna/tema mengikuti identitas biru/navy logo.
8. Dokumentasi lokal menjelaskan runtime PHP, instalasi, `.env`, konfigurasi MySQL, migrasi, storage link, pembuatan admin, serta cara menjalankan aplikasi dan pemeriksaan.
9. Tidak ada tautan atau kontrol admin di halaman konsumen; URL admin terpisah tetap dilindungi autentikasi server.

## Yang masih diperlukan

- Rancangan perlu ditinjau pengguna sebelum implementasi. Detail kontak dan isi tentang toko dapat diisi kemudian melalui pengaturan, sehingga tidak harus menunda pekerjaan aplikasi.

## Catatan implementasi

CSS/JavaScript native dan build aset Node tanpa dependency menggantikan Tailwind/Vite karena cache npm tidak lengkap dalam lingkungan offline; Laravel/Blade/MySQL tetap menjadi arsitektur utama. Catatan verifikasi dan minor review berada di ledger rencana implementasi.

## Koreksi nama toko

Pengguna menetapkan nama toko menjadi **Radeya Glass & Aluminium**. Gunakan nama ini pada teks tampilan, judul, dan konfigurasi awal. Header publik mengambil nama tersimpan dari pengaturan.
