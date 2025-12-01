<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\MenuComposer;

class MenuViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Gắn composer này vào partials header (hoặc layout chính)
        View::composer([
            'partials.frontend.header',
        ], MenuComposer::class);
    }
}
