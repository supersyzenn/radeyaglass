<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreSetting;
use App\Support\Seo;
use Illuminate\Contracts\View\View;

class CatalogController extends Controller
{
    public function index(): View
    {
        $store = StoreSetting::current();
        $products = Product::latest('id')->paginate(12);
        return view('catalog.index', ['store' => $store, 'products' => $products, 'seo' => Seo::page('home', $store, number: $products->currentPage())]);
    }

    public function show(Product $product): View
    {
        $store = StoreSetting::current();
        return view('catalog.show', ['store' => $store, 'product' => $product, 'seo' => Seo::page('product', $store, $product)]);
    }

    public function contact(): View
    {
        $store = StoreSetting::current();
        return view('catalog.contact', ['store' => $store, 'seo' => Seo::page('contact', $store)]);
    }

    public function about(): View
    {
        $store = StoreSetting::current();
        return view('catalog.about', ['store' => $store, 'seo' => Seo::page('about', $store)]);
    }
}
