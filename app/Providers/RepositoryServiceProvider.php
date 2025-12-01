<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import tất cả các Contract và Service cần thiết
use App\Contracts\MediaServiceContract;
use App\Services\MediaService;
// ... sau này sẽ thêm các service khác vào đây

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Đây sẽ là nơi tập trung TẤT CẢ các binding của chúng ta
        $this->app->bind(
            MediaServiceContract::class,
            MediaService::class
        );

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}