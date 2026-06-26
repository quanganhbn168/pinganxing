<?php

namespace App\Filament\Widgets;

use App\Models\AgencyRequest;
use App\Models\Contact;
use App\Models\ConsultingRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Yêu cầu khách hàng 7 ngày gần đây';

    protected ?string $description = 'Liên hệ, tư vấn và đăng ký đại lý theo ngày';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $labels = [];
        $contacts = [];
        $consulting = [];
        $agency = [];

        foreach (range(6, 0) as $daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            $labels[] = $date->format('d/m');
            $contacts[] = Contact::whereDate('created_at', $date)->count();
            $consulting[] = ConsultingRequest::whereDate('created_at', $date)->count();
            $agency[] = AgencyRequest::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Liên hệ',
                    'data' => $contacts,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => '#2563eb',
                ],
                [
                    'label' => 'Tư vấn',
                    'data' => $consulting,
                    'borderColor' => '#16a34a',
                    'backgroundColor' => '#16a34a',
                ],
                [
                    'label' => 'Đại lý',
                    'data' => $agency,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
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
