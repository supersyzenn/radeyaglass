<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_identifies_store_with_canonical_and_structured_data(): void
    {
        config(['app.url' => 'https://catalog.example.com']);
        $document = $this->document($this->get('/')->assertOk()->getContent());
        $this->assertStringContainsString('Radeya Glass & Aluminium', $document->evaluate('string(//title)'));
        $this->assertSame('https://catalog.example.com/', $document->evaluate('string(//link[@rel="canonical"]/@href)'));
        $this->assertSame('Radeya Glass & Aluminium', $document->evaluate('string(//meta[@property="og:site_name"]/@content)'));
        $graph = $this->schema($document)['@graph'];
        $website = array_values(array_filter($graph, fn ($item) => $item['@type'] === 'WebSite'))[0];
        $this->assertSame('Radeya Glass & Aluminium', $website['name']);
        $this->assertSame('https://catalog.example.com/', $website['url']);
    }

    public function test_local_pages_do_not_allow_indexing_even_when_flag_is_true(): void
    {
        config(['app.url' => 'https://catalog.example.com', 'seo.indexable' => true]);
        $this->app['env'] = 'local';
        $document = $this->document($this->get('/')->getContent());
        $this->assertSame('noindex, nofollow', $document->evaluate('string(//meta[@name="robots"]/@content)'));
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');
    }

    public function test_production_public_domain_can_be_explicitly_indexed(): void
    {
        config(['app.url' => 'https://catalog.example.com', 'seo.indexable' => true]);
        $this->app['env'] = 'production';
        $document = $this->document($this->get('/')->getContent());
        $this->assertStringStartsWith('index, follow', $document->evaluate('string(//meta[@name="robots"]/@content)'));
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: https://catalog.example.com/sitemap.xml');
    }

    public function test_loopback_url_is_never_indexable(): void
    {
        config(['app.url' => 'http://127.0.0.1:8000', 'seo.indexable' => true]);
        $this->app['env'] = 'production';
        $document = $this->document($this->get('/')->getContent());
        $this->assertSame('noindex, nofollow', $document->evaluate('string(//meta[@name="robots"]/@content)'));
    }

    public function test_product_metadata_matches_actual_discount_and_safely_encodes_content(): void
    {
        config(['app.url' => 'https://catalog.example.com']);
        $product = $this->product(['name' => 'Kaca & Aluminium </script><script>alert(1)</script>', 'description' => 'Detail </script><script>alert(1)</script>', 'discount_price' => 199000]);
        $response = $this->get('/produk/'.$product->slug.'?utm_source=test')->assertOk();
        $document = $this->document($response->getContent());
        $this->assertSame('https://catalog.example.com/produk/'.$product->slug, $document->evaluate('string(//link[@rel="canonical"]/@href)'));
        $graph = $this->schema($document)['@graph'];
        $item = array_values(array_filter($graph, fn ($node) => $node['@type'] === 'Product'))[0];
        $this->assertSame('199000', $item['offers']['price']);
        $this->assertSame('IDR', $item['offers']['priceCurrency']);
        $this->assertArrayNotHasKey('availability', $item['offers']);
        $response->assertSee('Rp199.000')->assertDontSee('<script>alert(1)</script>', false);
        $product->update(['discount_price' => null]);
        $graph = $this->schema($this->document($this->get('/produk/'.$product->slug)->getContent()))['@graph'];
        $item = array_values(array_filter($graph, fn ($node) => $node['@type'] === 'Product'))[0];
        $this->assertSame('250000', $item['offers']['price']);
    }

    public function test_contact_and_about_have_distinct_descriptions(): void
    {
        $contact = $this->document($this->get('/kontak')->getContent());
        $about = $this->document($this->get('/tentang')->getContent());
        $description = 'string(//meta[@name="description"]/@content)';
        $this->assertNotSame('', $contact->evaluate($description));
        $this->assertNotSame($contact->evaluate($description), $about->evaluate($description));
    }

    public function test_pagination_and_host_headers_cannot_poison_canonical(): void
    {
        config(['app.url' => 'https://catalog.example.com']);
        for ($i = 0; $i < 13; $i++) $this->product(['name' => 'Produk '.$i]);
        $document = $this->document($this->get('http://untrusted.example/?page=2&utm_source=test')->getContent());
        $this->assertSame('https://catalog.example.com/?page=2', $document->evaluate('string(//link[@rel="canonical"]/@href)'));
    }

    public function test_sitemap_is_valid_xml_contains_public_pages_and_products_but_no_admin(): void
    {
        config(['app.url' => 'https://catalog.example.com']);
        $product = $this->product();
        $response = $this->get('/sitemap.xml')->assertOk();
        $xml = simplexml_load_string($response->streamedContent());
        $this->assertNotFalse($xml);
        $locations = array_map('strval', iterator_to_array($xml->xpath('//*[local-name()="loc"]')));
        $this->assertContains('https://catalog.example.com/', $locations);
        $this->assertContains('https://catalog.example.com/kontak', $locations);
        $this->assertContains('https://catalog.example.com/tentang', $locations);
        $this->assertContains('https://catalog.example.com/produk/'.$product->slug, $locations);
        foreach ($locations as $url) $this->assertStringNotContainsString('/admin', $url);
    }

    private function product(array $overrides = []): Product
    {
        return Product::create(array_merge(['name' => 'Pintu Aluminium', 'description' => 'Keterangan produk', 'image_path' => 'products/example.png', 'price' => 250000, 'discount_price' => null], $overrides));
    }

    private function document(string $html): \DOMXPath
    {
        $document = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return new \DOMXPath($document);
    }

    private function schema(\DOMXPath $document): array
    {
        return json_decode($document->evaluate('string(//script[@type="application/ld+json"])'), true, 512, JSON_THROW_ON_ERROR);
    }
}
