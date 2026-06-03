<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\AdminDashboard;
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
use App\Settings\GeneralSettings;
use Awcodes\Curator\Models\Media;

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
                AdminDashboard::class,
            ])
            ->favicon(fn () => static::getSettingMediaUrl('favicon') ?? asset('favicon.ico'))
            ->brandLogo(fn () => static::getSettingMediaUrl('logo') ?? asset('images/logo.png'))
            ->brandLogoHeight('3rem')
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
                    ->navigationIcon('heroicon-o-photo')
                    ->navigationSort(3),
                FilamentShieldPlugin::make()
                    ->navigationGroup('Hệ thống & Cấu hình'),
            ]);
    }

    protected static function getSettingMediaUrl(string $key): ?string
    {
        try {
            $setting = app(GeneralSettings::class);
            $val = $setting->{$key};
            if (empty($val)) return null;

            if (is_string($val) && str_starts_with($val, '[') && str_ends_with($val, ']')) {
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $val = $decoded;
                }
            }
            $id = is_array($val) ? ($val[0] ?? null) : $val;
            
            if (is_numeric($id)) {
                $media = Media::find((int) $id);
                return $media ? url($media->url) : null;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
