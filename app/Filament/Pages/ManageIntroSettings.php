<?php

namespace App\Filament\Pages;

use App\Settings\IntroSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;
use UnitEnum;

class ManageIntroSettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';
    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống & Cấu hình';
    protected static ?string $navigationLabel = 'Về chúng tôi';
    protected static ?string $title = 'Cài đặt trang Về chúng tôi';
    protected static ?int $navigationSort = 11;

    protected static string $settings = IntroSettings::class;

    public function form(Schema $schema): Schema
    {
        $iconOptions = [
            'shield-check'   => 'Uy tín / Bảo mật',
            'heart'          => 'Tận tâm / Yêu thương',
            'rocket-launch'  => 'Đổi mới / Tốc độ',
            'light-bulb'     => 'Sáng tạo',
            'star'           => 'Nổi bật',
            'trophy'         => 'Thành tích',
            'users'          => 'Đội ngũ / Khách hàng',
            'globe-alt'      => 'Toàn cầu',
            'clock'          => 'Kinh nghiệm / Thời gian',
            'briefcase'      => 'Dự án / Kinh doanh',
            'building-office' => 'Công ty',
            'check-circle'   => 'Hoàn thành',
            'chart-bar'      => 'Tăng trưởng',
            'currency-dollar' => 'Tiết kiệm',
            'cog-6-tooth'    => 'Công nghệ',
            'academic-cap'   => 'Chuyên môn',
            'hand-thumb-up'  => 'Chất lượng',
        ];

        return $schema->components([
            Tabs::make('IntroSettings')
                ->tabs([

                    // ══════════════════════════════════════════════
                    // TAB 1: TỔNG QUAN TRANG
                    // ══════════════════════════════════════════════
                    Tab::make('Tổng quan trang')
                        ->icon('heroicon-o-home')
                        ->schema([
                            Section::make('Hero — Đầu trang')
                                ->description('Tiêu đề lớn và banner hiển thị đầu trang Về chúng tôi')
                                ->columns(1)
                                ->schema([
                                    TextInput::make('page_title')
                                        ->label('Tiêu đề trang')
                                        ->placeholder('Về Chúng Tôi')
                                        ->required(),
                                    TextInput::make('page_subtitle')
                                        ->label('Phụ đề / Slogan')
                                        ->placeholder('Đối tác chuyển đổi số tin cậy...'),
                                    CuratorPicker::make('page_banner_id')
                                        ->label('Ảnh banner đầu trang')
                                        ->acceptedFileTypes(['image/*']),
                                ]),

                            Section::make('CTA — Kêu gọi hành động cuối trang')
                                ->description('Block nổi bật ở cuối trang, kêu gọi liên hệ')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('cta_title')
                                        ->label('Tiêu đề CTA')
                                        ->placeholder('Sẵn sàng chuyển đổi số cùng chúng tôi?')
                                        ->columnSpanFull(),
                                    TextInput::make('cta_subtitle')
                                        ->label('Phụ đề CTA')
                                        ->placeholder('Liên hệ ngay để được tư vấn...')
                                        ->columnSpanFull(),
                                    TextInput::make('cta_button_label')
                                        ->label('Nhãn nút CTA')
                                        ->placeholder('Liên hệ tư vấn'),
                                ]),
                        ]),

                    // ══════════════════════════════════════════════
                    // TAB 2: CÂU CHUYỆN CÔNG TY
                    // ══════════════════════════════════════════════
                    Tab::make('Câu chuyện & Sứ mệnh')
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Section::make('Câu chuyện công ty')
                                ->description('Block trình bày lịch sử, quá trình hình thành — có ảnh minh họa')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('story_title')
                                        ->label('Tiêu đề section')
                                        ->placeholder('Câu chuyện của chúng tôi')
                                        ->columnSpanFull(),
                                    CuratorPicker::make('story_image_id')
                                        ->label('Ảnh minh họa')
                                        ->acceptedFileTypes(['image/*']),
                                    TextInput::make('founded_year')
                                        ->label('Năm thành lập')
                                        ->numeric()
                                        ->minValue(1900)
                                        ->maxValue((int) date('Y'))
                                        ->placeholder('2014'),
                                    TextInput::make('video_url')
                                        ->label('Link Video (YouTube/Vimeo)')
                                        ->placeholder('https://www.youtube.com/watch?v=...')
                                        ->url(),
                                    CuratorPicker::make('video_thumbnail_id')
                                        ->label('Thumbnail Video')
                                        ->description('Nếu để trống sẽ dùng ảnh minh họa câu chuyện')
                                        ->acceptedFileTypes(['image/*']),
                                    RichEditor::make('story_description')
                                        ->label('Nội dung câu chuyện')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Sứ mệnh & Tầm nhìn')
                                ->description('Hai block song song — Sứ mệnh và Tầm nhìn')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('mission_title')
                                        ->label('Tiêu đề: Sứ mệnh')
                                        ->placeholder('Sứ mệnh của chúng tôi'),
                                    TextInput::make('vision_title')
                                        ->label('Tiêu đề: Tầm nhìn')
                                        ->placeholder('Tầm nhìn 2030'),
                                    Textarea::make('mission_description')
                                        ->label('Nội dung Sứ mệnh')
                                        ->rows(4),
                                    Textarea::make('vision_description')
                                        ->label('Nội dung Tầm nhìn')
                                        ->rows(4),
                                ]),
                        ]),

                    // ══════════════════════════════════════════════
                    // TAB 3: LỊCH SỬ PHÁT TRIỂN (TIMELINE)
                    // ══════════════════════════════════════════════
                    Tab::make('Lịch sử phát triển')
                        ->icon('heroicon-o-clock')
                        ->schema([
                            Section::make('Các mốc lịch sử')
                                ->description('Hiển thị dạng timeline dọc trên trang Về chúng tôi')
                                ->schema([
                                    Repeater::make('timeline')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('year')
                                                ->label('Năm')
                                                ->placeholder('2014')
                                                ->required(),
                                            TextInput::make('title')
                                                ->label('Tiêu đề mốc')
                                                ->placeholder('Thành lập công ty')
                                                ->required(),
                                            RichEditor::make('description')
                                                ->label('Mô tả ngắn')
                                                ->columnSpanFull(),
                                            CuratorPicker::make('image_id')
                                                ->label('Ảnh minh họa (tuỳ chọn)')
                                                ->acceptedFileTypes(['image/*']),
                                        ])
                                        ->columns(2)
                                        ->reorderable()
                                        ->collapsible()
                                        ->defaultItems(4)
                                        ->maxItems(20)
                                        ->columnSpanFull()
                                        ->addActionLabel('+ Thêm mốc lịch sử'),
                                ]),
                        ]),

                    // ══════════════════════════════════════════════
                    // TAB 4: SỐ LIỆU & GIÁ TRỊ
                    // ══════════════════════════════════════════════
                    Tab::make('Số liệu & Giá trị')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Section::make('Số liệu nổi bật (Stats)')
                                ->description('Các con số ấn tượng: năm kinh nghiệm, khách hàng, dự án...')
                                ->schema([
                                    Repeater::make('stats')
                                        ->label('')
                                        ->schema([
                                            Select::make('icon')
                                                ->label('Icon')
                                                ->options($iconOptions)
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('value')
                                                ->label('Con số')
                                                ->placeholder('500+')
                                                ->required(),
                                            TextInput::make('label')
                                                ->label('Nhãn')
                                                ->placeholder('Khách hàng tin dùng')
                                                ->required(),
                                            TextInput::make('suffix')
                                                ->label('Đơn vị (tuỳ chọn)')
                                                ->placeholder('dự án'),
                                        ])
                                        ->columns(4)
                                        ->reorderable()
                                        ->collapsible()
                                        ->defaultItems(4)
                                        ->maxItems(8)
                                        ->columnSpanFull()
                                        ->addActionLabel('+ Thêm chỉ số'),
                                ]),

                            Section::make('Giá trị cốt lõi (Core Values)')
                                ->description('Các giá trị định hướng của công ty — hiển thị dạng card icon')
                                ->schema([
                                    Repeater::make('core_values')
                                        ->label('')
                                        ->schema([
                                            Select::make('icon')
                                                ->label('Icon')
                                                ->options($iconOptions)
                                                ->searchable()
                                                ->required(),
                                            TextInput::make('title')
                                                ->label('Tên giá trị')
                                                ->placeholder('Uy tín')
                                                ->required(),
                                            RichEditor::make('description')
                                                ->label('Mô tả ngắn')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(3)
                                        ->reorderable()
                                        ->collapsible()
                                        ->defaultItems(4)
                                        ->maxItems(8)
                                        ->columnSpanFull()
                                        ->addActionLabel('+ Thêm giá trị'),
                                ]),
                        ]),

                    // ══════════════════════════════════════════════
                    // TAB 5: NỘI DUNG TÙY CHỈNH (BUILDER)
                    // ══════════════════════════════════════════════
                    Tab::make('Nội dung tùy chỉnh')
                        ->icon('heroicon-o-puzzle-piece')
                        ->schema([
                            Section::make('Khối nội dung linh hoạt')
                                ->description('Kéo thả các khối nội dung để xây dựng phần bổ sung cho trang Về chúng tôi.')
                                ->schema([
                                    Repeater::make('custom_blocks')
                                        ->label('')
                                        ->schema([
                                            Select::make('type')
                                                ->label('Loại khối')
                                                ->options([
                                                    'text_block'       => 'Đoạn văn bản',
                                                    'image_text_block' => 'Ảnh kèm văn bản',
                                                    'video_block'      => 'Video YouTube',
                                                ])
                                                ->required()
                                                ->live()
                                                ->columnSpanFull(),

                                            // Trường cho text_block
                                            RichEditor::make('content')
                                                ->label('Nội dung')
                                                ->columnSpanFull()
                                                ->visible(fn ($get) => in_array($get('type'), ['text_block', 'image_text_block'])),

                                            // Trường cho image_text_block
                                            CuratorPicker::make('image_id')
                                                ->label('Hình ảnh')
                                                ->acceptedFileTypes(['image/*'])
                                                ->visible(fn ($get) => $get('type') === 'image_text_block'),

                                            Select::make('image_position')
                                                ->label('Vị trí ảnh')
                                                ->options([
                                                    'left'  => '← Ảnh bên trái',
                                                    'right' => 'Ảnh bên phải →',
                                                ])
                                                ->default('left')
                                                ->visible(fn ($get) => $get('type') === 'image_text_block'),

                                            // Trường cho video_block
                                            TextInput::make('video_url')
                                                ->label('Link YouTube')
                                                ->url()
                                                ->placeholder('https://www.youtube.com/watch?v=...')
                                                ->visible(fn ($get) => $get('type') === 'video_block')
                                                ->columnSpanFull(),

                                            TextInput::make('title')
                                                ->label('Tiêu đề khối (tuỳ chọn)')
                                                ->visible(fn ($get) => in_array($get('type'), ['image_text_block', 'video_block']))
                                                ->columnSpanFull(),

                                            Textarea::make('description')
                                                ->label('Mô tả ngắn')
                                                ->rows(2)
                                                ->visible(fn ($get) => $get('type') === 'video_block')
                                                ->columnSpanFull(),
                                        ])
                                        ->columns(2)
                                        ->reorderable()
                                        ->collapsible()
                                        ->columnSpanFull()
                                        ->addActionLabel('+ Thêm khối nội dung')
                                        ->itemLabel(fn (array $state): ?string => match($state['type'] ?? null) {
                                            'text_block'       => 'Đoạn văn bản',
                                            'image_text_block' => 'Ảnh kèm văn bản',
                                            'video_block'      => 'Video YouTube',
                                            default            => 'Khối mới',
                                        }),
                                ]),
                        ]),

                ])
                ->columnSpanFull(),
        ]);
    }
}
