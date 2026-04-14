<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Sản Phẩm & Phần Cứng', Product::count())
                ->description('Tổng số lượng sản phẩm')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
                
            Stat::make('Dự Án Đã Triển Khai', Project::count())
                ->description('Hệ thống đối tác & khách hàng')
                ->descriptionIcon('heroicon-m-building-library')
                ->chart([2, 5, 3, 8, 4, 9, 6])
                ->color('info'),
                
            Stat::make('Tin Tức & Sự Kiện', Post::count())
                ->description('Bài viết trên hệ thống')
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([1, 4, 2, 5, 3, 6, 4])
                ->color('warning'),
        ];
    }
}
