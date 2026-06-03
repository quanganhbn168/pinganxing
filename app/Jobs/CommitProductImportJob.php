<?php

namespace App\Jobs;

use App\Models\ProductImportBatch;
use App\Services\ProductImport\ProductCommitter;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CommitProductImportJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProductImportBatch $batch,
        public bool $includeNeedsReview = false,
    ) {
    }

    public function handle(ProductCommitter $committer): void
    {
        $committer->commit($this->batch, $this->includeNeedsReview);
    }
}
