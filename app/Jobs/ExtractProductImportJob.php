<?php

namespace App\Jobs;

use App\Models\ProductImportBatch;
use App\Services\ProductImport\SpreadsheetExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtractProductImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProductImportBatch $batch)
    {
    }

    public function handle(SpreadsheetExtractor $extractor): void
    {
        $extractor->extract($this->batch);
    }
}
