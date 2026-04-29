<?php

namespace App\Filament\Resources\Careers\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CareerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->components([
                Section::make('Thông tin tuyển dụng')
                    ->schema([
                        TextInput::make('name')
                            ->label('Vị trí tuyển dụng')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required()
                            ->maxLength(255),

                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextInput::make('salary')
                                    ->label('Mức lương')
                                    ->placeholder('Ví dụ: 15 - 25 triệu, Thỏa thuận'),

                                TextInput::make('quantity')
                                    ->label('Số lượng tuyển')
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                TextInput::make('education')
                                    ->label('Trình độ')
                                    ->placeholder('Ví dụ: Đại học, Cao đẳng, Không yêu cầu'),

                                TextInput::make('location')
                                    ->label('Địa điểm làm việc')
                                    ->placeholder('Ví dụ: Hà Nội, Hồ Chí Minh, Remote'),

                                TextInput::make('type')
                                    ->label('Hình thức làm việc')
                                    ->required()
                                    ->default('Full-time')
                                    ->placeholder('Ví dụ: Full-time, Part-time, Remote'),

                                DatePicker::make('deadline')
                                    ->label('Hạn ứng tuyển')
                                    ->native(false)
                                    ->displayFormat('d/m/Y'),
                            ])
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Mô tả công việc')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('requirement')
                            ->label('Yêu cầu ứng viên')
                            ->rows(5)
                            ->columnSpanFull(),

                        Textarea::make('benefit')
                            ->label('Quyền lợi')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make('Ảnh đại diện')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Ảnh tuyển dụng'),                            ])
                            ->columns(1),

                        Section::make('Hiển thị')
                            ->schema([
                                Toggle::make('status')
                                    ->label('Kích hoạt')
                                    ->default(true)
                                    ->required(),

                                Toggle::make('is_home')
                                    ->label('Hiển thị trang chủ')
                                    ->default(false)
                                    ->required(),
                            ])
                            ->columns(1),

                        Section::make('Sắp xếp')
                            ->schema([
                                TextInput::make('position')
                                    ->label('Vị trí sắp xếp')
                                    ->helperText('Dùng để kéo thả sắp xếp ở bảng danh sách.')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }
}
