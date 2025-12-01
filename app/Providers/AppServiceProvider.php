<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;        
use Illuminate\Support\Facades\Gate;
use App\Models\MenuItem;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            // Hàm hasRole này có sẵn nhờ trait HasRoles anh đã thêm vào Model
            if (method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
                return true;
            }
        });
        View::composer('*', function ($view) {
            $headerMenu = MenuItem::where('parent_id', 0)
            ->orderBy('position')
            ->with([
                'children' => function($q) {
                    $q->with('page', 'category', 'fieldCategory', 'projectCategory', 'postCategory'); // Load cho con
                }, 
                'page', 
                'category', 
                'fieldCategory', 
                'projectCategory', 
                'postCategory' // Load cho cha
            ]) 
            ->get();

            $view->with('headerMenu', $headerMenu);
        });
        Paginator::useBootstrapFour();

        
    }
}
