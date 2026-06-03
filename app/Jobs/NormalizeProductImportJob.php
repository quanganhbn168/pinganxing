<?php

namespace App\Jobs;

use App\Models\ProductImportBatch;
use App\Services\ProductImport\RowNormalizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class NormalizeProductImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProductImportBatch $batch)
    {
    }

    public function handle(RowNormalizer $normalizer): void
    {
        $normalizer->normalize($this->batch);
    }
}
