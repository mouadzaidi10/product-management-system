<?php

namespace App\Services\Importers;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\ProductVariation;

class ApiProductImporter implements ProductImporterInterface
{
    public function import(): array
    {
        $response = Http::withoutVerifying()->get('https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products');

        if (!$response->successful()) {
            return [];
        }

        $products = $response->json();
        $importedProducts = [];

        foreach ($products as $record) {
            $product = Product::updateOrCreate(
                ['sku' => $record['id'] ?? 'SKU_' . uniqid()], // Use API `id` as SKU
                [
                    'name' => $record['name'] ?? 'Unnamed Product',
                    'status' => 'active',
                    'price' => $record['price'] ?? 0,
                    'currency' => 'USD',
                ]
            );

            // Insert Variations into `product_variations` table
            if (!empty($record['variations'])) {
                foreach ($record['variations'] as $variation) {
                    ProductVariation::updateOrCreate(
                        ['id' => $variation['id']],
                        [
                            'product_id' => $product->id, // Link variation to the product
                            'color' => $variation['color'] ?? 'unknown',
                            'size' => $variation['material'] ?? 'N/A',
                            'quantity' => $variation['quantity'] ?? 0,
                            'availability' => $variation['quantity'] > 0,
                        ]
                    );
                }
            }

            $importedProducts[] = $product->toArray();
        }

        return $importedProducts;
    }
}
