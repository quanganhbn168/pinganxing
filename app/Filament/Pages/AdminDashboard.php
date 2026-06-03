<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminDashboardStatsWidget;
use App\Filament\Widgets\AdminQuickActionsWidget;
use App\Filament\Widgets\AdminRecentRequestsWidget;
use App\Filament\Widgets\ContactChartWidget;
use App\Filament\Widgets\OrderChartWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;

class AdminDashboard extends Dashboard
{
    protected static bool $isDiscovered = false;

    protected static string $routePath = '/';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard quản trị';

    protected static ?int $navigationSort = -10;

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard quản trị';
    }

    /**
     * @return array<class-string<Widget>|WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            AdminDashboardStatsWidget::class,
            ContactChartWidget::class,
            OrderChartWidget::class,
            AdminRecentRequestsWidget::class,
            AdminQuickActionsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}
