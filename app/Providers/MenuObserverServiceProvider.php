<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use App\Models\PostCategory;
use App\Models\FieldCategory;
use App\Models\ProjectCategory;
use App\Observers\MenuCacheObserver;

class MenuObserverServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Category::observe(MenuCacheObserver::class);
        PostCategory::observe(MenuCacheObserver::class);
        FieldCategory::observe(MenuCacheObserver::class);
        ProjectCategory::observe(MenuCacheObserver::class);
    }
}
