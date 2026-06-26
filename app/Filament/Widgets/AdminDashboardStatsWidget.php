<?php

namespace App\Filament\Widgets;

use App\Models\AgencyRequest;
use App\Models\Contact;
use App\Models\ConsultingRequest;
use App\Models\Newsletter;
use App\Models\Service;
use App\Models\Tour;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminDashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $newContacts = Contact::where('status', false)->count();
        $pendingConsulting = ConsultingRequest::whereIn('status', ['new', 'pending', 'in_progress'])->count();
        $pendingAgency = AgencyRequest::whereIn('status', ['new', 'processing'])->count();
        $newsletterCount = Newsletter::count();

        return [
            Stat::make('Liên hệ mới', number_format($newContacts))
                ->description('Khách chưa được xử lý')
                ->descriptionIcon('heroicon-m-envelope')
                ->chart($this->dailyCounts(Contact::class))
                ->color($newContacts > 0 ? 'danger' : 'success'),

            Stat::make('Tư vấn triển khai', number_format($pendingConsulting))
                ->description('Yêu cầu cần theo dõi')
                ->descriptionIcon('heroicon-m-phone')
                ->color($pendingConsulting > 0 ? 'info' : 'gray'),

            Stat::make('Đăng ký đại lý', number_format($pendingAgency))
                ->description('Hồ sơ đối tác đang mở')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($pendingAgency > 0 ? 'primary' : 'gray'),

            Stat::make('Tour đang hiển thị', number_format(Tour::where('status', true)->count()))
                ->description('Tổng tour bật hiển thị')
                ->descriptionIcon('heroicon-m-map')
                ->chart($this->dailyCounts(Tour::class))
                ->color('success'),

            Stat::make('Dịch vụ đang hiển thị', number_format(Service::where('status', true)->count()))
                ->description('Tổng dịch vụ bật hiển thị')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),

            Stat::make('Khách hàng', number_format(User::count()))
                ->description('Thành viên website')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Đăng ký nhận tin', number_format($newsletterCount))
                ->description('Email khách để lại')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('warning'),
        ];
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $model
     * @return array<int>
     */
    private function dailyCounts(string $model): array
    {
        return collect(range(6, 0))
            ->map(fn (int $daysAgo): int => $model::whereDate('created_at', now()->subDays($daysAgo)->toDateString())->count())
            ->all();
    }
}
