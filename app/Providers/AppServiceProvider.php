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
        // Register Observers
        \App\Models\WorkOrder::observe(\App\Observers\WorkOrderObserver::class);
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);

        Gate::before(function ($user, $ability) {
            // Hàm hasRole này có sẵn nhờ trait HasRoles anh đã thêm vào Model
            if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
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
        // Share data specific for footer
        View::composer('partials.frontend.footer', function ($view) {
            $footerPolicies = \App\Models\Post::whereHas('category', function($q) {
                $q->where('slug', 'chinh-sach');
            })->where('status', 1)->latest()->get();
            
            $view->with('footerPolicies', $footerPolicies);
        });

        Paginator::useBootstrapFour();

        
    }
}
