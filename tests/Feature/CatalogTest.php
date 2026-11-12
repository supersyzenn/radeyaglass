<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_has_store_brand_and_no_admin_navigation(): void
    {
        $this->get('/')->assertOk()->assertSee('Radeya Glass & Aluminium')->assertDontSee('/admin');
        $this->actingAs(User::factory()->create())->get('/')->assertOk()->assertDontSee('/admin');
    }

    public function test_product_detail_shows_original_and_discounted_rupiah_prices(): void
    {
        $product = $this->product(['discount_price' => 199000]);
        $this->get('/produk/'.$product->slug)->assertOk()
            ->assertSee('Rp250.000')->assertSee('Rp199.000')->assertSee('<del', false);
    }

    public function test_product_without_discount_has_no_struck_out_price(): void
    {
        $product = $this->product();
        $this->get('/produk/'.$product->slug)->assertOk()->assertSee('Rp250.000')->assertDontSee('<del', false);
    }

    public function test_missing_product_returns_not_found(): void
    {
        $this->get('/produk/tidak-ada')->assertNotFound();
    }

    public function test_blank_contacts_do_not_link_to_fake_destinations(): void
    {
        $this->get('/kontak')->assertOk()->assertDontSee('wa.me/')->assertDontSee('mailto:');
        $this->get('/tentang')->assertOk()->assertSee('Radeya Glass & Aluminium');
    }

    public function test_whatsapp_message_contains_product_name_and_detail_url(): void
    {
        StoreSetting::current()->update(['whatsapp' => '6281234567890', 'email' => 'toko@example.com']);
        $product = $this->product(['name' => 'Kaca & Aluminium']);
        $response = $this->get('/produk/'.$product->slug)->assertOk();
        $response->assertSee('https://wa.me/6281234567890?text=', false)
            ->assertSee(rawurlencode('Kaca & Aluminium'), false)
            ->assertSee(rawurlencode('/produk/'.$product->slug), false);
        $this->get('/kontak')->assertSee('mailto:toko@example.com', false);
    }

    public function test_duplicate_names_have_unique_slugs_and_renaming_preserves_url(): void
    {
        $first = $this->product();
        $second = $this->product();
        $this->assertNotSame($first->slug, $second->slug);
        $slug = $first->slug;
        $first->update(['name' => 'Nama diperbarui']);
        $this->assertSame($slug, $first->fresh()->slug);
        $this->get('/produk/'.$slug)->assertOk()->assertSee('Nama diperbarui');
    }

    public function test_product_description_is_escaped(): void
    {
        $product = $this->product(['description' => '<script>alert(1)</script>']);
        $this->get('/produk/'.$product->slug)->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_transliterated_names_fit_the_slug_database_column(): void
    {
        $product = $this->product(['name' => str_repeat('ß', 160)]);
        $this->assertLessThanOrEqual(200, strlen($product->slug));
    }

    public function test_default_seeder_does_not_create_a_default_admin(): void
    {
        $this->seed();
        $this->assertDatabaseCount('users', 0);
        $this->get('/')->assertOk()->assertSee('Radeya Glass & Aluminium');
    }

    private function product(array $attributes = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Pintu Aluminium', 'description' => 'Keterangan produk',
            'image_path' => 'products/example.png', 'price' => 250000, 'discount_price' => null,
        ], $attributes));
    }
}
