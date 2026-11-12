<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Seo;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $text = Seo::indexable()
            ? "User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".Seo::baseUrl()."/sitemap.xml\n"
            : "User-agent: *\nDisallow: /\n";

        return response($text)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(): StreamedResponse
    {
        return response()->stream(function (): void {
            $base = Seo::baseUrl();
            $escape = fn (string $text): string => htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach (['/', '/kontak', '/tentang'] as $path) {
                echo '<url><loc>'.$escape($base.$path).'</loc></url>';
            }
            foreach (Product::query()->select('slug', 'updated_at')->orderBy('id')->cursor() as $product) {
                echo '<url><loc>'.$escape($base.route('catalog.show', $product->slug, false)).'</loc><lastmod>'.$escape($product->updated_at->toAtomString()).'</lastmod></url>';
            }
            echo '</urlset>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8', 'X-Robots-Tag' => 'noindex']);
    }
}
