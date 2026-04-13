<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\RichEditor;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        \Awcodes\Curator\Facades\Glide::serverConfig([
            'driver' => extension_loaded('imagick') ? 'imagick' : 'gd',
            'cache' => storage_path('app/public/.cache'),
        ]);
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

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
