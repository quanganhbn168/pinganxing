<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\RichEditor;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });
        // Share general settings globally to all views
        try {
            \Illuminate\Support\Facades\View::share('setting', app(\App\Settings\GeneralSettings::class));
        } catch (\Exception $e) {
            // Ignore during setup/migrations when table doesn't exist
        }

        RichEditor::configureUsing(function (RichEditor $builder) {
            $builder->plugins([
                SourceCodePlugin::make(),
            ])->toolbarButtons([
                'attachFiles',
                'blockquote',
                'bold',
                'bulletList',
                'codeBlock',
                'h2',
                'h3',
                'italic',
                'link',
                'orderedList',
                'redo',
                'strike',
                'underline',
                'undo',
                'sourceCode',
            ]);
        });
    }
}
