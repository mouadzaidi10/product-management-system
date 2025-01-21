<?php

namespace App\Services;

use App\Services\Importers\ProductImporterInterface;

class ProductImportManager
{
    /**
     * Create a new class instance.
     */

    private ProductImporterInterface $importer;

    public function setImporter(ProductImporterInterface $importer)
    {
        $this->importer = $importer;
    }

    public function importProducts(): array
    {
        return $this->importer->import();
    }

    public function __construct()
    {
        //
    }
}
