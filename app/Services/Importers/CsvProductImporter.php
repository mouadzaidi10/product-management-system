<?php

namespace App\Services\Importers;

use Illuminate\Support\Facades\Storage;

class CsvProductImporter implements ProductImporterInterface
{
    /**
     * Create a new class instance.
     */

    public function import(): array
    {
        $products = [];
        $filePath = storage_path('app/products.csv');

        if (!file_exists($filePath)) {
            return [];
        }

        $file = fopen($filePath, 'r');
        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $products[] = [
                'name' => $data[0],
                'sku' => $data[1],
                'status' => $data[2],
                'variations' => $data[3],
                'price' => (float) $data[4],
                'currency' => $data[5],
            ];
        }
        fclose($file);

        return $products;
    }
}
