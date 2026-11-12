<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = ['name', 'email', 'whatsapp', 'about'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['name' => 'Radeya Glass & Aluminium']);
    }

    public function whatsappUrl(?Product $product = null): ?string
    {
        if (! $this->whatsapp) {
            return null;
        }

        $message = $product
            ? 'Halo, saya tertarik dengan produk '.$product->name.'. '.route('catalog.show', $product->slug)
            : 'Halo, saya ingin bertanya tentang produk '.$this->name.'.';

        return 'https://wa.me/'.$this->whatsapp.'?text='.rawurlencode($message);
    }
}
