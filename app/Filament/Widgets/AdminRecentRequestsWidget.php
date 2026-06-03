<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AgencyRequests\AgencyRequestResource;
use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\ConsultingRequests\ConsultingRequestResource;
use App\Models\AgencyRequest;
use App\Models\Contact;
use App\Models\ConsultingRequest;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AdminRecentRequestsWidget extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.admin-recent-requests-widget';

    protected int|string|array $columnSpan = 1;

    protected function getViewData(): array
    {
        return [
            'items' => $this->getItems(),
        ];
    }

    private function getItems(): Collection
    {
        $contacts = Contact::latest()
            ->take(5)
            ->get()
            ->map(fn (Contact $contact): array => [
                'type' => 'Liên hệ',
                'title' => $contact->name ?: 'Khách liên hệ',
                'description' => $contact->phone ?: ($contact->email ?: $contact->subject),
                'time' => $contact->created_at,
                'badge' => $contact->status ? 'Đã xử lý' : 'Mới',
                'badge_color' => $contact->status ? 'success' : 'danger',
                'url' => ContactResource::getUrl('edit', ['record' => $contact]),
            ]);

        $consulting = ConsultingRequest::latest()
            ->take(5)
            ->get()
            ->map(fn (ConsultingRequest $request): array => [
                'type' => 'Tư vấn',
                'title' => $request->company ?: $request->name,
                'description' => $request->phone ?: $request->email,
                'time' => $request->created_at,
                'badge' => $this->statusLabel($request->status),
                'badge_color' => in_array($request->status, ['done', 'completed'], true) ? 'success' : 'warning',
                'url' => ConsultingRequestResource::getUrl('edit', ['record' => $request]),
            ]);

        $agency = AgencyRequest::latest()
            ->take(5)
            ->get()
            ->map(fn (AgencyRequest $request): array => [
                'type' => 'Đại lý',
                'title' => $request->shop_name ?: $request->name,
                'description' => $request->area ?: $request->phone,
                'time' => $request->created_at,
                'badge' => $this->statusLabel($request->status),
                'badge_color' => in_array($request->status, ['approved', 'done'], true) ? 'success' : 'info',
                'url' => AgencyRequestResource::getUrl('edit', ['record' => $request]),
            ]);

        $orders = Order::latest()
            ->take(5)
            ->get()
            ->map(fn (Order $order): array => [
                'type' => 'Đơn hàng',
                'title' => '#' . ($order->code ?: $order->id),
                'description' => trim(($order->customer_name ?: 'Khách hàng') . ' · ' . number_format((float) $order->total_price) . 'đ'),
                'time' => $order->created_at,
                'badge' => $this->statusLabel($order->status),
                'badge_color' => in_array($order->status, ['completed'], true) ? 'success' : 'warning',
                'url' => null,
            ]);

        return $contacts
            ->concat($consulting)
            ->concat($agency)
            ->concat($orders)
            ->sortByDesc('time')
            ->take(10)
            ->values();
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'new' => 'Mới',
            'pending' => 'Chờ xử lý',
            'processing', 'in_progress' => 'Đang xử lý',
            'completed', 'done' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'approved' => 'Đã duyệt',
            default => $status ?: 'Đang cập nhật',
        };
    }
}
