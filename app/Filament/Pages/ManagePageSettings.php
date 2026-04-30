<?php

namespace App\Filament\Pages;

use App\Settings\PageSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Pages\SettingsPage;
use UnitEnum;

class ManagePageSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Cấu hình';
    protected static ?string $navigationLabel = 'Cài đặt trang';
    protected static ?string $title = 'Cài đặt trang danh mục';
    protected static ?int $navigationSort = 12;

    protected static string $settings = PageSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('PageSettings')
                    ->tabs([
                        $this->pageTab('Sản phẩm', 'heroicon-o-cube', 'products'),
                        $this->pageTab('Dự án', 'heroicon-o-building-office', 'projects'),
                        $this->pageTab('Dịch vụ', 'heroicon-o-wrench-screwdriver', 'services'),
                        $this->pageTab('Lĩnh vực', 'heroicon-o-squares-2x2', 'fields'),
                        $this->pageTab('Tin tức', 'heroicon-o-newspaper', 'posts'),
                        $this->pageTab('Tuyển dụng', 'heroicon-o-briefcase', 'careers'),
                        $this->pageTab('Liên hệ', 'heroicon-o-phone', 'contact'),
                        $this->pageTab('Đại lý', 'heroicon-o-users', 'agency'),
                        $this->pageTab('Tư vấn', 'heroicon-o-chat-bubble-bottom-center-text', 'consulting'),
                    ])
                    ->columnSpanFull()
            ]);
    }

    private function pageTab(string $label, string $icon, string $prefix): Tab
    {
        return Tab::make($label)
            ->icon($icon)
            ->schema([
                Section::make('Leaderboard đầu trang')
                    ->description('Banner đầu trang theo mẫu: title, subline, description, tối đa 2 nút và hàng số liệu icon.')
                    ->columns(2)
                    ->schema([
                        Textarea::make("{$prefix}_title")
                            ->label('Title')
                            ->rows(2)
                            ->required(),
                        TextInput::make("{$prefix}_leaderboard_subline")
                            ->label('Subline / Badge')
                            ->placeholder('VD: Giải pháp công nghệ toàn diện'),
                        TextInput::make("{$prefix}_headline")
                            ->label('Headline / Slogan cũ')
                            ->helperText('Dùng làm fallback nếu description leaderboard để trống.')
                            ->columnSpanFull(),
                        Textarea::make("{$prefix}_leaderboard_description")
                            ->label('Description trên leaderboard')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Đoạn mô tả hiển thị dưới title trên banner.'),
                        CuratorPicker::make("{$prefix}_banner")
                            ->label('Ảnh leaderboard')
                            ->acceptedFileTypes(['image/*'])
                            ->columnSpanFull(),
                        Repeater::make("{$prefix}_leaderboard_actions")
                            ->label('Action buttons')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Nhãn nút')
                                    ->placeholder('Tư vấn giải pháp')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('Link')
                                    ->placeholder('/lien-he'),
                                Select::make('icon')
                                    ->label('Icon FontAwesome Free')
                                    ->options($this->fontAwesomeIconOptions())
                                    ->searchable()
                                    ->placeholder('Chọn icon cho nút'),
                                Select::make('style')
                                    ->label('Kiểu nút')
                                    ->options([
                                        'primary' => 'Primary xanh',
                                        'secondary' => 'Secondary trắng',
                                    ])
                                    ->default('primary')
                                    ->required(),
                            ])
                            ->columns(4)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(2)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm nút'),
                        Repeater::make("{$prefix}_leaderboard_stats")
                            ->label('Hàng số liệu dưới banner')
                            ->schema([
                                Select::make('icon')
                                    ->label('Icon FontAwesome Free')
                                    ->options($this->fontAwesomeIconOptions())
                                    ->searchable()
                                    ->placeholder('Chọn icon minh họa')
                                    ->required(),
                                TextInput::make('value')
                                    ->label('Số liệu')
                                    ->placeholder('500+')
                                    ->required(),
                                TextInput::make('label')
                                    ->label('Nhãn')
                                    ->placeholder('Khách hàng tin dùng')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->maxItems(4)
                            ->columnSpanFull()
                            ->addActionLabel('+ Thêm số liệu'),
                    ]),

                Textarea::make("{$prefix}_description")
                    ->label('Mô tả SEO')
                    ->rows(2)
                    ->helperText('Mô tả ngắn hiển thị trên Google'),

                Section::make('Khối Call-To-Action (CTA)')
                    ->schema([
                        TextInput::make("{$prefix}_cta_title")
                            ->label('Tiêu đề CTA')
                            ->placeholder('VD: Bạn chưa tìm thấy ngành nghề của mình?'),
                        Textarea::make("{$prefix}_cta_description")
                            ->label('Mô tả CTA')
                            ->rows(3)
                            ->placeholder('VD: Hệ thống của chúng tôi sở hữu lõi công nghệ linh hoạt...'),
                        TextInput::make("{$prefix}_cta_link")
                            ->label('Link Nút CTA')
                            ->placeholder('URL khi click, để trống sẽ dùng mặc định'),
                    ]),

                RichEditor::make("{$prefix}_content")
                    ->label('Nội dung trang')
                    ->columnSpanFull()
                    ->json(false)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('page-content'),
            ]);
    }

    private function fontAwesomeIconOptions(): array
    {
        return [
            'Kinh doanh & khách hàng' => [
                'fas fa-users' => 'Khách hàng / Người dùng',
                'fas fa-user-tie' => 'Chuyên gia / Tư vấn',
                'fas fa-handshake' => 'Hợp tác / Đối tác',
                'fas fa-building' => 'Doanh nghiệp / Tòa nhà',
                'fas fa-store' => 'Cửa hàng / Điểm bán',
                'fas fa-briefcase' => 'Kinh doanh / Dự án',
                'fas fa-award' => 'Giải thưởng / Chứng nhận',
                'fas fa-trophy' => 'Thành tựu',
            ],
            'Dự án & triển khai' => [
                'fas fa-diagram-project' => 'Sơ đồ dự án',
                'fas fa-list-check' => 'Checklist / Hoàn tất',
                'fas fa-check-circle' => 'Đã hoàn thành',
                'fas fa-clipboard-check' => 'Nghiệm thu',
                'fas fa-route' => 'Lộ trình',
                'fas fa-location-dot' => 'Địa điểm',
                'fas fa-map-location-dot' => 'Khu vực triển khai',
                'fas fa-layer-group' => 'Nhiều phân hệ',
            ],
            'Tăng trưởng & số liệu' => [
                'fas fa-chart-line' => 'Tăng trưởng',
                'fas fa-chart-column' => 'Biểu đồ cột',
                'fas fa-arrow-trend-up' => 'Xu hướng tăng',
                'fas fa-percent' => 'Tỷ lệ phần trăm',
                'fas fa-coins' => 'Chi phí / Doanh thu',
                'fas fa-sack-dollar' => 'Tài chính',
                'fas fa-gauge-high' => 'Hiệu suất',
                'fas fa-bullseye' => 'Mục tiêu',
            ],
            'Công nghệ & hệ thống' => [
                'fas fa-laptop-code' => 'Phần mềm',
                'fas fa-microchip' => 'Công nghệ',
                'fas fa-server' => 'Máy chủ',
                'fas fa-database' => 'Dữ liệu',
                'fas fa-cloud' => 'Cloud',
                'fas fa-network-wired' => 'Mạng hệ thống',
                'fas fa-shield-halved' => 'Bảo mật',
                'fas fa-gear' => 'Cấu hình / Vận hành',
            ],
            'Hỗ trợ & dịch vụ' => [
                'fas fa-headset' => 'Hỗ trợ',
                'fas fa-phone' => 'Điện thoại',
                'fas fa-comments' => 'Tư vấn / Chat',
                'fas fa-envelope' => 'Email',
                'fas fa-screwdriver-wrench' => 'Bảo trì',
                'fas fa-life-ring' => 'Hỗ trợ kỹ thuật',
                'fas fa-people-arrows' => 'Đồng hành',
                'fas fa-thumbs-up' => 'Hài lòng',
            ],
            'Thời gian & quy mô' => [
                'fas fa-clock' => 'Thời gian',
                'fas fa-calendar-check' => 'Năm / Lịch triển khai',
                'fas fa-hourglass-half' => 'Thời lượng',
                'fas fa-globe' => 'Toàn quốc / Toàn cầu',
                'fas fa-city' => 'Thành phố',
                'fas fa-industry' => 'Nhà máy / Ngành nghề',
                'fas fa-warehouse' => 'Kho vận',
                'fas fa-rocket' => 'Khởi động nhanh',
            ],
            'Điều hướng nút' => [
                'fas fa-arrow-right' => 'Mũi tên phải',
                'fas fa-chevron-right' => 'Chevron phải',
                'fas fa-paper-plane' => 'Gửi yêu cầu',
                'fas fa-circle-info' => 'Tìm hiểu thêm',
                'fas fa-download' => 'Tải xuống',
                'fas fa-file-lines' => 'Tài liệu',
                'fas fa-cart-shopping' => 'Mua hàng',
                'fas fa-magnifying-glass' => 'Xem / Tìm kiếm',
            ],
        ];
    }
}
