<?php

return [
    'required' => ':Attribute wajib diisi.',
    'string' => ':Attribute harus berupa teks.',
    'email' => ':Attribute harus berupa alamat email yang valid.',
    'unique' => ':Attribute sudah digunakan.',
    'integer' => ':Attribute harus berupa angka rupiah tanpa pecahan.',
    'regex' => 'Format :attribute tidak valid.',
    'lt' => ['numeric' => ':Attribute harus lebih rendah dari harga normal.'],
    'min' => ['numeric' => ':Attribute minimal :min.', 'string' => ':Attribute minimal :min karakter.'],
    'max' => ['numeric' => ':Attribute maksimal :max.', 'string' => ':Attribute maksimal :max karakter.', 'file' => ':Attribute maksimal 5 MB.'],
    'file' => ':Attribute harus berupa file.',
    'image' => ':Attribute harus berupa gambar JPG, PNG, atau WebP.',
    'mimes' => ':Attribute harus berupa JPG, PNG, atau WebP.',
    'extensions' => 'Ekstensi :attribute harus JPG, PNG, atau WebP.',
    'uploaded' => 'Foto gagal diunggah. Pilih file maksimal 5 MB.',
    'attributes' => ['name' => 'Nama', 'description' => 'Keterangan produk', 'price' => 'Harga normal', 'discount_price' => 'Harga diskon', 'image' => 'Foto produk', 'email' => 'Email', 'password' => 'Kata sandi', 'whatsapp' => 'Nomor WhatsApp', 'about' => 'Tentang toko'],
];
