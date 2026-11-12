<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'image_path', 'price', 'discount_price'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'discount_price' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $base = substr(Str::slug($product->name) ?: 'produk', 0, 180);
            $slug = $base;
            $suffix = 2;
            while (static::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }
            $product->slug = $slug;
        });
    }

    public static function disk(): string
    {
        return config('filesystems.products_disk', 'public');
    }

    public function imageUrl(): string
    {
        return Storage::disk(static::disk())->url($this->image_path);
    }

    public function formattedPrice(bool $original = false): string
    {
        return 'Rp'.number_format($original ? $this->price : ($this->discount_price ?? $this->price), 0, ',', '.');
    }
}
