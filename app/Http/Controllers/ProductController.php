<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;

class ProductController extends Controller
{
    /**
     * Menampilkan semua product.
     */
    public function index(): View
    {
        $products = Product::latest()->paginate(10);

        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form tambah product.
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Menyimpan product baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($image = $this->storeProductImage($request)) {
            $validated['image'] = $image;
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail product.
     */
    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Mengupdate product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $previousImage = $product->image;
        if ($image = $this->storeProductImage($request)) {
            $validated['image'] = $image;
        } else {
            unset($validated['image']);
        }

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        if ($product->image !== $previousImage) {
            $this->deleteProductImage($previousImage);
        }

        return redirect()->route('products.index')->with('success', 'Product berhasil diperbarui.');
    }

    /**
     * Menghapus product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $image = $product->image;
        $product->delete();

        $this->deleteProductImage($image);

        return redirect()->route('products.index')->with('success', 'Product berhasil dihapus.');
    }

    private function storeProductImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $path = $request->file('image')->store('products', 'public');
        if ($path === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        return $path;
    }

    private function deleteProductImage(?string $path): void
    {
        if ($path && str_starts_with($path, 'products/') && ! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Gambar produk gagal dihapus.');
        }
    }
}
