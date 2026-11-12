<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\Photo;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase, Photo;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guests_cannot_read_private_forms_or_mutate_products(): void
    {
        $product = $this->existingProduct();
        foreach (['/admin/produk', '/admin/produk/create', '/admin/produk/'.$product->id.'/edit'] as $url) {
            $this->get($url)->assertRedirect('/admin/login');
        }
        $this->post('/admin/produk', $this->payload())->assertRedirect('/admin/login');
        $this->patch('/admin/produk/'.$product->id, ['name' => 'Hacked'])->assertRedirect('/admin/login');
        $this->delete('/admin/produk/'.$product->id)->assertRedirect('/admin/login');
        $this->assertDatabaseCount('products', 1);
        $this->assertSame('Produk lama', $product->fresh()->name);
    }

    public function test_admin_can_create_and_view_product_with_persistent_photo(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin/produk/create')->assertOk()->assertSee('Tambah Produk');
        $this->post('/admin/produk', $this->payload())->assertRedirect('/admin/produk')->assertSessionHasNoErrors();
        $product = Product::firstOrFail();
        $this->assertSame('Pintu Aluminium', $product->name);
        $this->assertSame(199000, $product->discount_price);
        Storage::disk('public')->assertExists($product->image_path);
        $this->get('/admin/produk')->assertOk()->assertSee('Pintu Aluminium');
        $this->get('/admin/produk/'.$product->id.'/edit')->assertOk()->assertSee('Pintu Aluminium');
        $this->get('/produk/'.$product->slug)->assertOk()->assertSee('Rp199.000');
    }

    public function test_edit_without_new_photo_keeps_photo_and_can_clear_discount(): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->existingProduct();
        $this->patch('/admin/produk/'.$product->id, ['name' => 'Nama baru', 'description' => 'Baru', 'price' => 300000, 'discount_price' => ''])
            ->assertRedirect('/admin/produk')->assertSessionHasNoErrors();
        $this->assertSame('products/old.png', $product->fresh()->image_path);
        $this->assertNull($product->fresh()->discount_price);
        Storage::disk('public')->assertExists('products/old.png');
    }

    public function test_replacing_and_deleting_product_cleans_up_old_photos(): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->existingProduct();
        $this->patch('/admin/produk/'.$product->id, $this->payload())->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing('products/old.png');
        $newPath = $product->fresh()->image_path;
        Storage::disk('public')->assertExists($newPath);
        $this->delete('/admin/produk/'.$product->id)->assertRedirect('/admin/produk');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_shared_photo_is_not_deleted_while_another_product_uses_it(): void
    {
        $this->actingAs(User::factory()->create());
        $first = $this->existingProduct();
        $second = $this->existingProduct();
        $this->delete('/admin/produk/'.$first->id)->assertRedirect('/admin/produk');
        Storage::disk('public')->assertExists($second->image_path);
    }

    #[DataProvider('invalidPrices')]
    public function test_invalid_prices_are_rejected_without_changing_existing_data(array $overrides, string $field): void
    {
        $this->actingAs(User::factory()->create());
        $product = $this->existingProduct();
        $this->patch('/admin/produk/'.$product->id, array_merge($this->payload(), $overrides))->assertSessionHasErrors($field);
        $this->assertSame('Produk lama', $product->fresh()->name);
        Storage::disk('public')->assertExists('products/old.png');
        $this->assertCount(1, Storage::disk('public')->allFiles('products'));
    }

    public static function invalidPrices(): array
    {
        return [
            'normal zero' => [['price' => 0], 'price'],
            'normal negative' => [['price' => -1], 'price'],
            'normal fraction' => [['price' => '250000.5'], 'price'],
            'normal too large' => [['price' => '1000000000000'], 'price'],
            'discount same' => [['discount_price' => 250000], 'discount_price'],
            'discount greater' => [['discount_price' => 300000], 'discount_price'],
            'discount zero' => [['discount_price' => 0], 'discount_price'],
            'discount negative' => [['discount_price' => -1], 'discount_price'],
            'discount fraction' => [['discount_price' => '199000.5'], 'discount_price'],
        ];
    }

    public function test_invalid_photo_content_or_extension_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        $largePhoto = $this->photo();
        $largeContent = file_get_contents($largePhoto->getRealPath()).str_repeat('x', 5 * 1024 * 1024);
        $invalid = [
            $this->upload('foto.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            $this->upload('foto.png', 'not an image'),
            $this->photo('foto.php'),
            $this->upload('besar.png', $largeContent),
        ];
        foreach ($invalid as $photo) {
            $this->post('/admin/produk', array_merge($this->payload(), ['image' => $photo]))->assertSessionHasErrors('image');
        }
        $this->assertDatabaseCount('products', 0);
        $this->assertCount(0, Storage::disk('public')->allFiles());
    }

    public function test_create_requires_photo(): void
    {
        $this->actingAs(User::factory()->create());
        $data = $this->payload();
        unset($data['image']);
        $this->post('/admin/produk', $data)->assertSessionHasErrors('image');
    }

    private function payload(): array
    {
        return ['name' => 'Pintu Aluminium', 'description' => 'Keterangan produk', 'price' => 250000, 'discount_price' => 199000, 'image' => $this->photo()];
    }

    private function existingProduct(): Product
    {
        Storage::disk('public')->put('products/old.png', 'existing photo');
        return Product::create(['name' => 'Produk lama', 'description' => 'Lama', 'price' => 250000, 'discount_price' => 199000, 'image_path' => 'products/old.png']);
    }
}
