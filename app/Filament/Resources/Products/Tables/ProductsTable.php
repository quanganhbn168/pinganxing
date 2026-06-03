<?php

namespace App\Filament\Resources\Products\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                TextColumn::make('code')
                    ->label('Mã SP')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('brand.name')
                    ->label('Thương hiệu')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tags.name')
                    ->label('Thẻ')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label('Giá bán')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('price_discount')
                    ->label('Giá KM')
                    ->money('VND')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('stock')
                    ->label('Tồn kho')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                ToggleColumn::make('status')
                    ->label('Kích hoạt')
                    ->alignCenter(),
                IconColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_on_sale')
                    ->label('Giảm giá')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('brand_id')
                    ->label('Thương hiệu')
                    ->relationship('brand', 'name')
                    ->preload()
                    ->searchable(),
                SelectFilter::make('tags')
                    ->label('Thẻ')
                    ->relationship('tags', 'name', modifyQueryUsing: fn ($query) => $query->ordered())
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
    BulkActionGroup::make([
        BulkAction::make('update_category')
            ->label('Cập nhật danh mục')
            ->icon('heroicon-o-folder')
            ->color('info')
            ->form([
                Select::make('category_id')
                    ->label('Danh mục mới')
                    ->options(
                        Category::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->requiresConfirmation()
            ->modalHeading('Cập nhật danh mục hàng loạt')
            ->modalDescription('Các sản phẩm đã chọn sẽ được chuyển sang danh mục mới.')
            ->modalSubmitActionLabel('Cập nhật')
            ->action(function (Collection $records, array $data): void {
                Product::query()
                    ->whereKey($records->modelKeys())
                    ->update([
                        'category_id' => $data['category_id'],
                    ]);
            })
            ->deselectRecordsAfterCompletion(),

        DeleteBulkAction::make(),
    ]),
]);
    }
}
