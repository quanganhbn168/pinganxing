<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;
use Awcodes\Curator\CuratorPlugin;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Forms\Components\RichEditor;
use Awcodes\RicherEditor\Plugins\SourceCodePlugin;
use Awcodes\Curator\Config\CurationManager;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        CurationManager::configure()->presets([
            CurationPreset::make('Ảnh chuẩn SEO (1200x630)')
                ->width(1200)
                ->height(630)
                ->format('webp')
                ->quality(85),
        ]);

        RichEditor::configureUsing(function (RichEditor $builder) {
            $builder->plugins([
                SourceCodePlugin::make(),
                AttachCuratorMediaPlugin::make(),
            ])->toolbarButtons([
                'attachCuratorMedia',
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

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Quản lý Hàng hóa'),
                NavigationGroup::make('Nội dung Trang chủ'),
                NavigationGroup::make('Lĩnh vực & Dịch vụ'),
                NavigationGroup::make('Dự án & Đối tác'),
                NavigationGroup::make('Tin tức & Sự kiện'),
                NavigationGroup::make('Giao tiếp Khách hàng'),
                NavigationGroup::make('Hệ thống & Cấu hình'),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                CuratorPlugin::make()
                    ->label('Media')
                    ->pluralLabel('Media')
                    ->navigationGroup('Hệ thống & Cấu hình')
                    ->navigationIcon('heroicon-o-photo'),
                FilamentShieldPlugin::make()
                    ->navigationGroup('Hệ thống & Cấu hình'),
            ]);
    }
}
