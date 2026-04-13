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
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
