<?php

namespace App\Filament\Resources\Tours\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ToursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('image_id')
                    ->label('Ảnh')
                    ->size(60),
                TextColumn::make('name')
                    ->label('Tên tour')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category.name')
                    ->label('Danh mục / Điểm đến')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Giá gốc')
                    ->money('VND')
                    ->sortable(),
                ToggleColumn::make('status')
                    ->label('Hiển thị'),
                ToggleColumn::make('is_home')
                    ->label('Nổi bật (Trang chủ)'),
                ToggleColumn::make('is_hot')
                    ->label('HOT'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
