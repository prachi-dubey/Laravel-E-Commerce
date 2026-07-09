<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_customer_can_place_order_from_cart(): void
    {
        $user = $this->actingAsCustomer();
        $product = Product::factory()->create(['stock' => 10, 'price' => 25.00]);
        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->postJson('/api/v1/orders');

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', OrderStatus::Pending->value);

        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
    }

    public function test_customer_can_view_own_orders(): void
    {
        $user = $this->actingAsCustomer();
        Order::create([
            'user_id' => $user->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 50.00,
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk()
            ->assertJsonCount(1, 'data.orders');
    }

    public function test_admin_can_view_all_orders(): void
    {
        $this->actingAsAdmin();
        $customer = $this->createCustomer();

        Order::create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 50.00,
        ]);

        Order::create([
            'user_id' => $customer->id,
            'status' => OrderStatus::Confirmed,
            'total_amount' => 75.00,
        ]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertOk()
            ->assertJsonCount(2, 'data.orders');
    }

    public function test_admin_can_update_order_status(): void
    {
        $this->actingAsAdmin();
        $order = Order::create([
            'user_id' => $this->createCustomer()->id,
            'status' => OrderStatus::Pending,
            'total_amount' => 50.00,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Confirmed->value,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', OrderStatus::Confirmed->value);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $this->actingAsAdmin();
        $order = Order::create([
            'user_id' => $this->createCustomer()->id,
            'status' => OrderStatus::Delivered,
            'total_amount' => 50.00,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Pending->value,
        ]);

        $response->assertStatus(422);
    }
}
