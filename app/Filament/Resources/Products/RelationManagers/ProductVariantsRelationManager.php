<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Attribute;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Biến thể sản phẩm';

    protected static ?string $modelLabel = 'Biến thể';

    /**
     * Form cho EDIT 1 biến thể (slide-over)
     */
    public function form(Schema $schema): Schema
    {
        $product = $this->getOwnerRecord();
        $categoryId = $product->category_id;

        $attributes = Attribute::whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))
            ->where('is_variant_defining', true)
            ->with('values')
            ->take(3)
            ->get();

        $attributeFields = $attributes->map(function (Attribute $attr) {
            return \Filament\Forms\Components\Select::make("options.{$attr->name}")
                ->label($attr->name)
                ->options($attr->values->pluck('value', 'value')->toArray())
                ->searchable()
                ->required();
        })->toArray();

        return $schema
            ->components([
                Section::make('Thuộc tính')
                    ->schema($attributeFields)
                    ->columns(count($attributeFields) >= 2 ? 2 : 1),

                Section::make('Giá & Kho')
                    ->schema([
                        TextInput::make('sku')
                            ->label('Mã SKU')
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        TextInput::make('price')
                            ->label('Giá bán')
                            ->prefix('₫')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->required(),
                        TextInput::make('compare_at_price')
                            ->label('Giá gốc (gạch)')
                            ->prefix('₫')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric(),
                        TextInput::make('stock')
                            ->label('Tồn kho')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Cấu hình')
                    ->schema([
                        Toggle::make('is_default')
                            ->label('Biến thể mặc định'),
                        CuratorPicker::make('image_id')
                            ->label('Ảnh biến thể'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('options')
                    ->label('Thuộc tính')
                    ->getStateUsing(function ($record) {
                        $options = $record->options;
                        if (empty($options) || ! is_array($options)) {
                            return '—';
                        }
                        return collect($options)
                            ->map(fn ($v, $k) => "{$k}: {$v}")
                            ->join(' · ');
                    })
                    ->badge()
                    ->separator(' · ')
                    ->color('info'),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('compare_at_price')
                    ->label('Giá gốc')
                    ->money('VND')
                    ->color('danger'),
                TextColumn::make('stock')
                    ->label('Kho')
                    ->numeric()
                    ->alignCenter(),
                IconColumn::make('is_default')
                    ->label('Mặc định')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->headerActions([
                // ═══════════════════════════════════
                // SINH TỔ HỢP BIẾN THỂ TỰ ĐỘNG
                // ═══════════════════════════════════
                $this->makeGenerateAction(),
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_edit')
                        ->label('Sửa hàng loạt')
                        ->icon('heroicon-o-pencil-square')
                        ->slideOver()
                        ->schema([
                            Section::make('Cập nhật giá & kho')
                                ->description('Chỉ những trường có giá trị mới sẽ được cập nhật. Để trống = giữ nguyên.')
                                ->schema([
                                    TextInput::make('bulk_price')
                                        ->label('Giá bán mới')
                                        ->prefix('₫')
                                        ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                        ->stripCharacters('.')
                                        ->numeric(),
                                    TextInput::make('bulk_compare_at_price')
                                        ->label('Giá gốc mới')
                                        ->prefix('₫')
                                        ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                        ->stripCharacters('.')
                                        ->numeric(),
                                    TextInput::make('bulk_stock')
                                        ->label('Tồn kho mới')
                                        ->numeric(),
                                ])
                                ->columns(3),
                        ])
                        ->modalHeading('Sửa hàng loạt biến thể')
                        ->modalSubmitActionLabel('Cập nhật')
                        ->action(function ($records, array $data) {
                            $updates = [];
                            if (filled($data['bulk_price'])) {
                                $updates['price'] = $data['bulk_price'];
                            }
                            if (filled($data['bulk_compare_at_price'])) {
                                $updates['compare_at_price'] = $data['bulk_compare_at_price'];
                            }
                            if (filled($data['bulk_stock'])) {
                                $updates['stock'] = $data['bulk_stock'];
                            }

                            if (empty($updates)) {
                                Notification::make()
                                    ->title('Chưa nhập giá trị nào!')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $count = 0;
                            foreach ($records as $record) {
                                $record->update($updates);
                                $count++;
                            }

                            Notification::make()
                                ->title("Đã cập nhật {$count} biến thể!")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Action "Sinh biến thể" — chọn multi giá trị cho mỗi thuộc tính (tối đa 3),
     * hệ thống tự tạo tất cả tổ hợp.
     */
    protected function makeGenerateAction(): Action
    {
        $product = $this->getOwnerRecord();
        $categoryId = $product->category_id;

        $attributes = Attribute::whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))
            ->where('is_variant_defining', true)
            ->with('values')
            ->take(3)
            ->get();

        $checkboxFields = $attributes->map(function (Attribute $attr) {
            return CheckboxList::make("attr_{$attr->id}")
                ->label($attr->name)
                ->options($attr->values->pluck('value', 'value')->toArray())
                ->columns(3)
                ->required();
        })->toArray();

        return Action::make('generate')
            ->label('Sinh biến thể')
            ->icon('heroicon-o-sparkles')
            ->color('primary')
            ->slideOver()
            ->schema([
                Section::make('Chọn giá trị thuộc tính')
                    ->description('Chọn các giá trị cho từng thuộc tính. Hệ thống sẽ tự tạo tất cả tổ hợp biến thể.')
                    ->schema($checkboxFields),

                Section::make('Giá chung (áp cho tất cả)')
                    ->description('Bạn có thể sửa giá riêng cho từng biến thể sau.')
                    ->schema([
                        TextInput::make('default_price')
                            ->label('Giá bán mặc định')
                            ->prefix('₫')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->stripCharacters('.')
                            ->numeric()
                            ->required(),
                        TextInput::make('default_stock')
                            ->label('Tồn kho mặc định')
                            ->numeric()
                            ->required()
                            ->default(0),
                    ])
                    ->columns(2),
            ])
            ->modalHeading('Sinh biến thể tự động')
            ->modalSubmitActionLabel('Tạo biến thể')
            ->action(function (array $data) use ($product, $attributes) {
                // Thu thập giá trị đã chọn cho từng thuộc tính
                $selectedValues = [];
                foreach ($attributes as $attr) {
                    $key = "attr_{$attr->id}";
                    if (! empty($data[$key])) {
                        $selectedValues[$attr->name] = $data[$key];
                    }
                }

                if (empty($selectedValues)) {
                    Notification::make()
                        ->title('Chưa chọn giá trị nào!')
                        ->danger()
                        ->send();
                    return;
                }

                // Tính tổ hợp (cartesian product)
                $combinations = $this->cartesian($selectedValues);

                $created = 0;
                foreach ($combinations as $combo) {
                    // Kiểm tra trùng lặp
                    $exists = $product->variants()
                        ->where('options', json_encode($combo))
                        ->exists();

                    if (! $exists) {
                        $product->variants()->create([
                            'options'          => $combo,
                            'price'            => $data['default_price'] ?? 0,
                            'stock'            => $data['default_stock'] ?? 0,
                            'is_default'       => $created === 0,
                        ]);
                        $created++;
                    }
                }

                Notification::make()
                    ->title("Đã tạo {$created} biến thể!")
                    ->success()
                    ->send();
            });
    }

    /**
     * Tính tổ hợp Cartesian product.
     * Input:  ['Màu' => ['Đỏ', 'Xanh'], 'Size' => ['S', 'M']]
     * Output: [
     *   ['Màu' => 'Đỏ', 'Size' => 'S'],
     *   ['Màu' => 'Đỏ', 'Size' => 'M'],
     *   ['Màu' => 'Xanh', 'Size' => 'S'],
     *   ['Màu' => 'Xanh', 'Size' => 'M'],
     * ]
     */
    protected function cartesian(array $input): array
    {
        $result = [[]];

        foreach ($input as $key => $values) {
            $temp = [];
            foreach ($result as $existing) {
                foreach ($values as $value) {
                    $temp[] = $existing + [$key => $value];
                }
            }
            $result = $temp;
        }

        return $result;
    }
}
