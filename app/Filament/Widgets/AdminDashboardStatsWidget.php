<?php

namespace App\Filament\Widgets;

use App\Models\AgencyRequest;
use App\Models\Contact;
use App\Models\ConsultingRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminDashboardStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        $newContacts = Contact::where('status', false)->count();
        $pendingConsulting = ConsultingRequest::whereIn('status', ['new', 'pending', 'in_progress'])->count();
        $pendingAgency = AgencyRequest::whereIn('status', ['new', 'processing'])->count();
        $pendingOrders = Order::whereIn('status', ['pending', 'processing'])->count();

        return [
            Stat::make('Đơn hàng cần xử lý', number_format($pendingOrders))
                ->description('Tổng đơn mới và đang xử lý')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->chart($this->dailyCounts(Order::class))
                ->color($pendingOrders > 0 ? 'warning' : 'success'),

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

            Stat::make('Sản phẩm đang bán', number_format(Product::where('status', true)->count()))
                ->description('Tổng sản phẩm bật hiển thị')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Khách hàng', number_format(User::count()))
                ->description('Thành viên website')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),

            Stat::make('Doanh thu hôm nay', number_format((float) Order::whereDate('created_at', $today)->sum('total_price')) . 'đ')
                ->description('Tính theo đơn phát sinh trong ngày')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Đơn hôm nay', number_format(Order::whereDate('created_at', $today)->count()))
                ->description('Số đơn mới trong ngày')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $model
     * @return array<int>
     */
    private function dailyCounts(string $model): array
    {
        return collect(range(6, 0))
            ->map(fn (int $daysAgo): int => $model::whereDate('created_at', Carbon::today()->subDays($daysAgo))->count())
            ->all();
    }
}
