<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Tests\TestCase;

class CartTest extends TestCase
{
    public function test_customer_can_view_empty_cart(): void
    {
        $this->actingAsCustomer();

        $response = $this->getJson('/api/v1/cart');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'items', 'total_amount']]);
    }

    public function test_customer_can_add_item_to_cart(): void
    {
        $this->actingAsCustomer();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_customer_can_update_cart_item(): void
    {
        $user = $this->actingAsCustomer();
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::create(['user_id' => $user->id]);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->putJson("/api/v1/cart/items/{$item->id}", [
            'quantity' => 3,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('cart_items', ['id' => $item->id, 'quantity' => 3]);
    }

    public function test_customer_can_remove_cart_item(): void
    {
        $user = $this->actingAsCustomer();
        $product = Product::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $item = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->deleteJson("/api/v1/cart/items/{$item->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_admin_cannot_access_cart(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/v1/cart');

        $response->assertStatus(403);
    }
}
