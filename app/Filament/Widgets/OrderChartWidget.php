<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Đơn hàng 7 ngày gần đây';

    protected ?string $description = 'Số đơn và doanh thu theo ngày';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $labels = [];
        $orders = [];
        $revenue = [];

        foreach (range(6, 0) as $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $labels[] = $date->format('d/m');
            $orders[] = Order::whereDate('created_at', $date)->count();
            $revenue[] = (int) round(Order::whereDate('created_at', $date)->sum('total_price') / 1000);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Số đơn',
                    'data' => $orders,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => '#2563eb',
                ],
                [
                    'label' => 'Doanh thu (nghìn đ)',
                    'data' => $revenue,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => '#16a34a',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
