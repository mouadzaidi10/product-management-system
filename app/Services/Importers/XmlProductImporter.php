<?php

namespace App\Services\Importers;

class XmlProductImporter implements ProductImporterInterface
{
    /**
     * Create a new class instance.
     */

    public function import(): array
    {
        // Example XML processing (parsing a sample XML file)
        $filePath = storage_path('app/products.xml');

        if (!file_exists($filePath)) {
            return [];
        }

        $xml = simplexml_load_file($filePath);
        $products = [];

        foreach ($xml->product as $product) {
            $products[] = [
                'name' => (string) $product->name,
                'sku' => (string) $product->sku,
                'status' => (string) $product->status,
                'price' => (float) $product->price,
                'currency' => (string) $product->currency,
            ];
        }

        return $products;
    }

}
