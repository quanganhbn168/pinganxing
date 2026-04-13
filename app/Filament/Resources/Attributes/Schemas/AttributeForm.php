<?php

namespace App\Filament\Resources\Attributes\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class AttributeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin thuộc tính')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên thuộc tính')
                            ->placeholder('VD: Màu sắc, RAM, Kích thước')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->label('Kiểu hiển thị')
                            ->options([
                                'button' => 'Nút bấm (Button)',
                                'dropdown' => 'Danh sách (Dropdown)',
                                'color_swatch' => 'Mảng màu (Color)',
                            ])
                            ->default('button')
                            ->required(),
                        Toggle::make('is_variant_defining')
                            ->label('Dùng để tạo biến thể (Pricing)')
                            ->helperText('Bật nếu mảng này cấu thành giá riêng biệt (VD: RAM). Tắt nếu chỉ là thông số (Kích thước tủ).')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Các Danh mục áp dụng')
                    ->description('Xuất hiện khi đăng sản phẩm thuộc danh mục dưới đây:')
                    ->schema([
                        Select::make('categories')
                            ->label('Chọn danh mục')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                    ]),

                Section::make('Giá trị chuẩn bị sẵn (Presets)')
                    ->schema([
                        TagsInput::make('attribute_values')
                            ->label('Danh sách giá trị')
                            ->placeholder('Gõ giá trị rồi nhấn Enter hoặc dấu phẩy...')
                            ->splitKeys([',', 'Tab'])
                            ->helperText('VD: Đỏ, Đen, Trắng, 8GB, 128GB — nhấn Enter hoặc dấu phẩy để thêm.')
                            ->afterStateHydrated(function ($component, ?Model $record) {
                                if ($record) {
                                    $component->state(
                                        $record->values->pluck('value')->toArray()
                                    );
                                }
                            })
                            ->dehydrated(false)
                            ->saveRelationshipsUsing(function (?Model $record, $state) {
                                if (! $record) {
                                    return;
                                }

                                $newValues = collect($state ?? [])->filter()->values();
                                $existing = $record->values()->pluck('value', 'id');

                                // Xóa các giá trị đã bị bỏ
                                $toDelete = $existing->filter(fn ($v) => ! $newValues->contains($v));
                                if ($toDelete->isNotEmpty()) {
                                    $record->values()->whereIn('id', $toDelete->keys())->delete();
                                }

                                // Thêm các giá trị mới
                                $existingValues = $existing->values();
                                $newValues->each(function ($value) use ($record, $existingValues) {
                                    if (! $existingValues->contains($value)) {
                                        $record->values()->create(['value' => $value]);
                                    }
                                });
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

