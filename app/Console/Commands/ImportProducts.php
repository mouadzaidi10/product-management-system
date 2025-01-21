<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\ProductImportManager;
use App\Services\Importers\CsvProductImporter;
use App\Services\Importers\ApiProductImporter;
use App\Services\Importers\XmlProductImporter;
use App\Services\Importers\JsonProductImporter;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {source}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from a given data source (csv, api, xml, json)';

    private ProductImportManager $importManager;

    public function __construct(ProductImportManager $importManager)
    {
        parent::__construct();
        $this->importManager = $importManager;
    }

    /**
     * Execute the console command.
     */


    public function handle()
    {
        $source = $this->argument('source');

        switch ($source) {
            case 'csv':
                $this->importManager->setImporter(new CsvProductImporter());
                break;
            case 'api':
                $this->importManager->setImporter(new ApiProductImporter());
                break;
            case 'xml':
                $this->importManager->setImporter(new XmlProductImporter());
                break;
            case 'json':
                $this->importManager->setImporter(new JsonProductImporter());
                break;
            default:
                $this->error('Invalid data source. Use "csv", "api", "xml", or "json".');
                return;
        }

        $products = $this->importManager->importProducts();

        if (empty($products)) {
            $this->warn('No products found for import.');
            return;
        }

        // Insert products into the database
        foreach ($products as $product) {
            \App\Models\Product::updateOrCreate(
                ['sku' => $product['sku']],
                $product
            );
        }

        $this->info(count($products) . ' products imported successfully.');
    }

}
