<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_product_image_without_entering_a_filename(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('products.store'), [
                'name' => 'Produk Gambar',
                'price' => 5000,
                'stock' => 10,
                'image' => $this->fakeImage('nama-asli.png'),
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Produk Gambar')->firstOrFail();

        $this->assertStringStartsWith('products/', $product->image);
        $this->assertNotSame('products/nama-asli.png', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_editing_a_product_without_a_new_upload_keeps_its_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $image = $this->fakeImage('produk.png')->store('products', 'public');
        $product = Product::create([
            'name' => 'Produk',
            'price' => 5000,
            'stock' => 10,
            'image' => $image,
        ]);

        $this->actingAs($admin)
            ->put(route('products.update', $product), [
                'name' => 'Produk Diperbarui',
                'price' => 6000,
                'stock' => 8,
            ])
            ->assertRedirect(route('products.index'));

        $this->assertSame($image, $product->fresh()->image);
        Storage::disk('public')->assertExists($image);
    }

    public function test_product_image_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'Produk Invalid',
                'price' => 5000,
                'stock' => 10,
                'image' => UploadedFile::fake()->create('dokumen.txt', 10, 'text/plain'),
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('products', ['name' => 'Produk Invalid']);
    }

    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l7sAAAAASUVORK5CYII=')
        );
    }
}
