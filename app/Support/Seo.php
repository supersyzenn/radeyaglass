<?php

namespace App\Support;

use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Support\Str;

class Seo
{
    public static function baseUrl(): string
    {
        $url = preg_replace('/[?#].*$/', '', trim((string) config('app.url')));

        return rtrim($url, '/');
    }

    public static function indexable(): bool
    {
        $base = self::baseUrl();
        if (! app()->environment('production') || ! filter_var(config('seo.indexable'), FILTER_VALIDATE_BOOL)
            || ! filter_var($base, FILTER_VALIDATE_URL) || ! in_array(parse_url($base, PHP_URL_SCHEME), ['http', 'https'], true)) {
            return false;
        }
        $host = strtolower(trim((string) parse_url($base, PHP_URL_HOST), '[]'));
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return str_contains($host, '.') && ! preg_match('/(?:^|\.)(?:localhost|local|test|invalid|example)$/', $host);
    }

    public static function page(string $page, StoreSetting $store, ?Product $product = null, int $number = 1): array
    {
        $base = self::baseUrl();
        $name = $store->name;
        $path = match ($page) {
            'product' => route('catalog.show', $product->slug, false),
            'contact' => route('store.contact', [], false),
            'about' => route('store.about', [], false),
            default => '/',
        };
        $canonical = $base.$path.($page === 'home' && $number > 1 ? '?page='.$number : '');
        $title = match ($page) {
            'product' => $product->name.' | '.$name,
            'contact' => 'Kontak & WhatsApp | '.$name,
            'about' => 'Tentang Toko | '.$name,
            default => $name.' | Katalog Kaca & Aluminium'.($number > 1 ? ' — Halaman '.$number : ''),
        };
        $description = match ($page) {
            'product' => $product->name.' di '.$name.'. '.$product->description,
            'contact' => 'Hubungi '.$name.' melalui WhatsApp atau email untuk informasi produk, konsultasi, dan pemesanan kaca serta aluminium.',
            'about' => 'Kenali '.$name.'. '.($store->about ?: 'Jelajahi katalog kaca dan aluminium, lalu diskusikan kebutuhan Anda langsung dengan toko.'),
            default => 'Jelajahi katalog '.$name.'. Lihat foto, keterangan, dan harga produk kaca serta aluminium. Konsultasi dan pemesanan melalui WhatsApp.',
        };
        $description = Str::limit(Str::squish(strip_tags($description)), 160, '…');
        $image = $product ? $base.'/storage/'.ltrim($product->image_path, '/') : $base.'/images/radeya-logo.png';
        $organization = ['@type' => 'Organization', '@id' => $base.'/#organization', 'name' => $name, 'url' => $base.'/', 'logo' => $base.'/images/radeya-logo.png'];
        if ($store->email) $organization['email'] = $store->email;
        if ($store->whatsapp) $organization['telephone'] = '+'.$store->whatsapp;
        $graph = [
            $organization,
            ['@type' => 'WebSite', '@id' => $base.'/#website', 'name' => $name, 'url' => $base.'/', 'inLanguage' => 'id-ID', 'publisher' => ['@id' => $base.'/#organization']],
        ];
        if ($page !== 'home') {
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Produk', 'item' => $base.'/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => match ($page) { 'product' => $product->name, 'contact' => 'Kontak', default => 'Tentang Toko' }, 'item' => $canonical],
            ]];
        }
        if ($product) {
            $graph[] = ['@type' => 'Product', 'name' => $product->name, 'description' => Str::squish(strip_tags($product->description)), 'image' => [$image], 'url' => $canonical,
                'offers' => ['@type' => 'Offer', 'url' => $canonical, 'priceCurrency' => 'IDR', 'price' => (string) ($product->discount_price ?? $product->price), 'seller' => ['@id' => $base.'/#organization']],
            ];
        }

        return [
            'title' => $title, 'description' => $description, 'canonical' => $canonical, 'image' => $image, 'name' => $name,
            'robots' => self::indexable() ? 'index, follow, max-image-preview:large' : 'noindex, nofollow',
            'schema' => ['@context' => 'https://schema.org', '@graph' => $graph],
        ];
    }
}
