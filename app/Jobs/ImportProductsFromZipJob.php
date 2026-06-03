<?php

namespace App\Jobs;

use App\Services\ProductImport\ZipProductImporter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ImportProductsFromZipJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1800;

    public int $tries = 1;

    public function __construct(
        public string $sessionId,
        public string $brand,
        public bool $onlyHasImage = false,
        public bool $onlyHasSpecs = false,
    ) {
    }

    public function handle(ZipProductImporter $importer): void
    {
        $importer->import(
            sessionId: $this->sessionId,
            brand: $this->brand,
            onlyHasImage: $this->onlyHasImage,
            onlyHasSpecs: $this->onlyHasSpecs,
        );
    }

    public function failed(Throwable $exception): void
    {
        app(ZipProductImporter::class)->putStatus($this->sessionId, [
            'state' => 'failed',
            'message' => $exception->getMessage(),
            'processed' => 0,
            'total' => 0,
            'result' => null,
        ]);
    }
}
