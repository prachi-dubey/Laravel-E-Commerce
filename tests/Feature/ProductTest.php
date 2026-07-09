<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function test_anyone_can_list_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['products', 'pagination'],
            ]);
    }

    public function test_anyone_can_view_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_admin_can_create_product(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->postJson('/api/v1/products', [
            'name' => 'New Product',
            'description' => 'A test product',
            'price' => 29.99,
            'stock' => 50,
            'image' => UploadedFile::fake()->create('product.jpg', 100, 'image/jpeg'),
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', ['name' => 'New Product']);
        $this->assertDatabaseHas('product_audit_logs', ['action' => 'created']);
    }

    public function test_customer_cannot_create_product(): void
    {
        $this->actingAsCustomer();

        $response = $this->postJson('/api/v1/products', [
            'name' => 'New Product',
            'price' => 29.99,
            'stock' => 50,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_product(): void
    {
        $this->actingAsAdmin();
        $product = Product::factory()->create();

        $response = $this->putJson("/api/v1/products/{$product->id}", [
            'name' => 'Updated Name',
            'price' => 39.99,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('product_audit_logs', ['action' => 'updated']);
    }

    public function test_admin_can_delete_product(): void
    {
        $this->actingAsAdmin();
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/products/{$product->id}");

        $response->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertDatabaseHas('product_audit_logs', ['action' => 'deleted']);
    }
}
