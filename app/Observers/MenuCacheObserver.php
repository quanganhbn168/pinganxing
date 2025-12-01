<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class MenuCacheObserver
{
    protected function clearCache($model): void
    {
        $table = $model->getTable(); // ví dụ: categories, post_categories...
        Cache::forget("header_menu_structure:{$table}");
    }

    public function saved($model): void        { $this->clearCache($model); }
    public function deleted($model): void      { $this->clearCache($model); }
    public function restored($model): void     { $this->clearCache($model); }
    public function forceDeleted($model): void { $this->clearCache($model); }
}
