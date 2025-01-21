<?php

namespace App\Services\Importers;

use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\ProductVariation;

class JsonProductImporter implements ProductImporterInterface
{
    /**
     * Create a new class instance.
     */

    public function import(): array
    {
        $filePath = storage_path('app/products.json');

        if (!file_exists($filePath)) {
            return [];
        }

        $jsonContent = file_get_contents($filePath);
        $products = json_decode($jsonContent, true);
        $importedProducts = [];

        if (!is_array($products)) {
            return [];
        }

        foreach ($products as $product) {
            $productModel = Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'name' => $product['name'],
                    'status' => $product['status'],
                    'price' => $product['price'],
                    'currency' => $product['currency'],
                ]
            );

            // Process variations
            if (!empty($product['variations'])) {
                foreach ($product['variations'] as $variation) {
                    ProductVariation::updateOrCreate(
                        ['id' => $variation['id']],
                        [
                            'product_id' => $productModel->id,
                            'color' => $variation['color'],
                            'size' => $variation['size'],
                            'quantity' => $variation['quantity'],
                            'availability' => $variation['quantity'] > 0,
                        ]
                    );
                }
            }

            $importedProducts[] = $productModel->toArray();
        }

        return $importedProducts;
    }
}
