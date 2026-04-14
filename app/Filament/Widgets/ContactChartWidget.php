<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContactChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Biểu đồ liên hệ mới';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = Contact::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Khách liên hệ',
                    'data' => $data,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
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
