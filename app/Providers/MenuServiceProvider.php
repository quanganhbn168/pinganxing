<?php

namespace App\Providers;

use App\Models\Menu;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Chia sẻ dữ liệu menu đa cấp ra frontend layout.
     * - headerMenu: lấy menu đầu tiên có location=header
     * - footerCol2Menu / footerCol3Menu: lấy theo menu_id đã cấu hình trong Settings
     */
    public function boot(): void
    {
        View::composer('layouts.master', function ($view) {
            $headerMenu = cache()->remember('menu:header', 60 * 5, function () {
                $menu = Menu::where('location', 'header')
                    ->where('is_active', true)
                    ->first();

                if (!$menu) return collect();

                return $menu->items()
                    ->where(function ($q) {
                        $q->whereNull('parent_id')->orWhere('parent_id', 0);
                    })
                    ->with('children')
                    ->orderBy('position')
                    ->get();
            });

            // Footer menus lấy theo ID đã cấu hình trong Settings
            $settings = app(GeneralSettings::class);

            $footerCol2Menu = cache()->remember('menu:footer_col2', 60 * 5, function () use ($settings) {
                if (empty($settings->footer_col_2_menu_id)) return collect();

                $menu = Menu::find($settings->footer_col_2_menu_id);
                if (!$menu) return collect();

                return $menu->items()
                    ->where(function ($q) {
                        $q->whereNull('parent_id')->orWhere('parent_id', 0);
                    })
                    ->with('children')
                    ->orderBy('position')
                    ->get();
            });

            $footerCol3Menu = cache()->remember('menu:footer_col3', 60 * 5, function () use ($settings) {
                if (empty($settings->footer_col_3_menu_id)) return collect();

                $menu = Menu::find($settings->footer_col_3_menu_id);
                if (!$menu) return collect();

                return $menu->items()
                    ->where(function ($q) {
                        $q->whereNull('parent_id')->orWhere('parent_id', 0);
                    })
                    ->with('children')
                    ->orderBy('position')
                    ->get();
            });

            $view->with(compact('headerMenu', 'footerCol2Menu', 'footerCol3Menu'));
        });
    }
}

