<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::latest('id')->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $path = $request->file('image')->store('products', 'public');
        abort_unless($path, 500, 'Foto belum berhasil disimpan. Silakan coba kembali.');
        try {
            DB::transaction(fn () => Product::create([...$request->safe()->except('image'), 'image_path' => $path]));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', ['product' => $product]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $oldPath = $product->image_path;
        $newPath = null;
        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('products', 'public');
            abort_unless($newPath, 500, 'Foto belum berhasil disimpan. Silakan coba kembali.');
            $data['image_path'] = $newPath;
        }
        try {
            DB::transaction(fn () => $product->update($data));
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }
        if ($newPath) {
            $this->deleteUnusedPhoto($oldPath);
        }

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $path = $product->image_path;
        DB::transaction(fn () => $product->delete());
        $this->deleteUnusedPhoto($path);

        return redirect()->route('admin.products.index')->with('status', 'Produk berhasil dihapus.');
    }

    private function deleteUnusedPhoto(string $path): void
    {
        if (str_starts_with($path, 'products/') && ! Product::where('image_path', $path)->exists()) {
            Storage::disk('public')->delete($path);
        }
    }
}
