<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 📌 Step 1: Import from CSV
        $filePath = storage_path('app/products.csv');

        if (!file_exists($filePath)) {
            $this->error("CSV file not found at: $filePath");
            return;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // First row is treated as the header

        $newSkus = []; // Store new SKUs from the file
        foreach ($csv->getRecords() as $record) {
            $product = Product::updateOrCreate(
                ['sku' => $record['sku'] ?? 'SKU_' . uniqid()], // Generate SKU if missing
                [
                    'name' => $record['name'] ?? 'Unnamed Product',
                    'status' => $record['status'] ?? 'unknown',
                    'variations' => $record['variations'] ?? '',
                    'price' => $record['price'] ?? 0,
                    'currency' => $record['currency'] ?? 'USD',
                ]
            );
            $newSkus[] = $product->sku;
        }

        // 📌 Step 2: Soft Delete Outdated Products
        Product::whereNotIn('sku', $newSkus)
            ->whereNull('deleted_at') // Ensure only non-deleted products are considered
            ->update([
                'deleted_at' => now(),
                'status' => 'deleted_due_to_sync' // Add a hint to indicate why it was deleted
        ]);

        // 📌 Step 3: Fetch Data from External API
        $this->info("Fetching products from external API...");
        $response = Http::withoutVerifying()->get('https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products');

        if ($response->successful()) {
            $apiProducts = $response->json();

            foreach ($apiProducts as $record) {
                $product = Product::updateOrCreate(
                    ['sku' => $record['id'] ?? 'SKU_' . uniqid()], // Use `id` as `sku`
                    [
                        'name' => $record['name'] ?? 'Unnamed Product',
                        'status' => 'active', // Default status
                        'price' => $record['price'] ?? 0,
                        'currency' => 'USD', // Default currency
                    ]
                );

                // Insert Variations into `product_variations` Table
                if (!empty($record['variations'])) {
                    foreach ($record['variations'] as $variation) {
                        ProductVariation::updateOrCreate(
                            ['id' => $variation['id']],
                            [
                                'product_id' => $product->id, // Link to the product
                                'color' => $variation['color'] ?? 'unknown',
                                'size' => $variation['material'] ?? 'N/A',
                                'quantity' => $variation['quantity'] ?? 0,
                                'availability' => $variation['quantity'] > 0,
                            ]
                        );
                    }
                }
            }

            $this->info("API data imported successfully.");
        } else {
            $this->error("Failed to fetch API data. Status Code: " . $response->status());
        }

        $this->info("Products imported successfully.");
    }
}
