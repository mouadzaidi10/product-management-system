<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Http;

class ImportProductsCommandTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    #[Test]
    public function it_imports_products_from_csv()
    {
        // Prepare a sample CSV file
        $csvPath = storage_path('app/products.csv');
        file_put_contents($csvPath, "sku,name,price,currency\nTEST123,Test Product,100,USD");

        $this->artisan('import:products')
            ->expectsOutput('Fetching products from external API...')
            ->expectsOutput('Products imported successfully.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('products', ['sku' => 'TEST123']);
    }


    #[Test]
    public function it_fetches_products_from_api()
    {
        // Mock API response
        Http::fake([
            'https://5fc7a13cf3c77600165d89a8.mockapi.io/*' => Http::response([
                [
                    'id' => '1',
                    'name' => 'API Product',
                    'price' => 50,
                    'variations' => [
                        ['id' => '100', 'color' => 'red', 'quantity' => 10],
                    ],
                ],
            ]),
        ]);

        $this->artisan('import:products')
            ->expectsOutput('Fetching products from external API...')
            ->expectsOutput('API data imported successfully.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('products', ['name' => 'API Product']);
        $this->assertDatabaseHas('product_variations', ['color' => 'red']);
    }


    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
