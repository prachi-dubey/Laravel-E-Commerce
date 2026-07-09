<?php

namespace Tests\Unit\Services;

use App\Interfaces\ProductAuditLogRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    public function test_create_product_logs_audit_entry(): void
    {
        $product = new Product(['id' => 1, 'name' => 'Test', 'price' => 10, 'stock' => 5]);
        $product->id = 1;
        $user = User::factory()->admin()->make(['id' => 1]);

        $productRepo = Mockery::mock(ProductRepositoryInterface::class);
        $productRepo->shouldReceive('create')->once()->andReturn($product);

        $auditRepo = Mockery::mock(ProductAuditLogRepositoryInterface::class);
        $auditRepo->shouldReceive('log')->once()->with(
            1,
            1,
            'created',
            null,
            Mockery::type('array')
        );

        $service = new ProductService($productRepo, $auditRepo);
        $result = $service->create(['name' => 'Test', 'price' => 10, 'stock' => 5], null, $user);

        $this->assertSame($product, $result);
    }
}
