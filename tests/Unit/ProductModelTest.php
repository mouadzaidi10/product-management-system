<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductModelTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_product()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST123',
            'price' => 100.50,
            'currency' => 'USD',
        ]);

        $this->assertDatabaseHas('products', ['sku' => 'TEST123']);
        $this->assertEquals('Test Product', $product->name);
    }


    #[Test]
    public function it_can_soft_delete_a_product()
    {
        $product = Product::factory()->create();
        $product->delete();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_example(): void
    {
        $this->assertTrue(true);
    }
}
