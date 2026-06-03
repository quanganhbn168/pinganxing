<?php

namespace App\Console\Commands;

use App\Models\ProductImportBatch;
use App\Services\ProductImport\ProductCommitter;
use Illuminate\Console\Command;

class ProductImportCommit extends Command
{
    protected $signature = 'product-import:commit
        {batch : Product import batch id}
        {--include-needs-review : Also commit rows marked needs_review}';

    protected $description = 'Commit staged product import rows into products';

    public function handle(ProductCommitter $committer): int
    {
        $batch = ProductImportBatch::query()->findOrFail((int) $this->argument('batch'));

        $committer->commit($batch, (bool) $this->option('include-needs-review'));

        $batch->refresh();
        $this->info("Done. Imported: {$batch->imported_rows}, failed: {$batch->failed_rows}, status: {$batch->status}");

        return $batch->failed_rows > 0 ? self::FAILURE : self::SUCCESS;
    }
}
