<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ManageHomeSettings;
use App\Filament\Pages\ManagePageSettings;
use App\Filament\Pages\ManageSettings;
use App\Filament\Resources\Brands\BrandResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Contacts\ContactResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Widgets\Widget;

class AdminQuickActionsWidget extends Widget
{
    protected static ?int $sort = 5;

    protected string $view = 'filament.widgets.admin-quick-actions-widget';

    protected int|string|array $columnSpan = 1;

    protected function getViewData(): array
    {
        return [
            'actions' => [
                ['label' => 'Thêm sản phẩm', 'description' => 'Tạo sản phẩm hoặc phần cứng mới', 'icon' => 'heroicon-o-cube', 'url' => ProductResource::getUrl('create')],
                ['label' => 'Danh mục', 'description' => 'Chuẩn hóa cây danh mục sản phẩm', 'icon' => 'heroicon-o-folder', 'url' => CategoryResource::getUrl('index')],
                ['label' => 'Thương hiệu', 'description' => 'Quản lý hãng và logo', 'icon' => 'heroicon-o-tag', 'url' => BrandResource::getUrl('index')],
                ['label' => 'Dịch vụ', 'description' => 'Cập nhật dịch vụ triển khai', 'icon' => 'heroicon-o-wrench-screwdriver', 'url' => ServiceResource::getUrl('index')],
                ['label' => 'Dự án', 'description' => 'Hồ sơ dự án và case study', 'icon' => 'heroicon-o-star', 'url' => ProjectResource::getUrl('index')],
                ['label' => 'Liên hệ', 'description' => 'Xử lý khách gửi form', 'icon' => 'heroicon-o-envelope', 'url' => ContactResource::getUrl('index')],
                ['label' => 'Menu website', 'description' => 'Header, footer và điều hướng', 'icon' => 'heroicon-o-bars-3-bottom-left', 'url' => MenuResource::getUrl('index')],
                ['label' => 'Trang chủ', 'description' => 'Cấu hình nội dung trang chủ', 'icon' => 'heroicon-o-home', 'url' => ManageHomeSettings::getUrl()],
                ['label' => 'Cài đặt trang', 'description' => 'Banner, CTA và mô tả từng trang', 'icon' => 'heroicon-o-document-text', 'url' => ManagePageSettings::getUrl()],
                ['label' => 'Cài đặt chung', 'description' => 'Logo, hotline và thông tin công ty', 'icon' => 'heroicon-o-cog-6-tooth', 'url' => ManageSettings::getUrl()],
            ],
        ];
    }
}
