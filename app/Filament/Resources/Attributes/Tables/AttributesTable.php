<?php

namespace App\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Thuộc tính')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('type')
                    ->label('Kiểu hiển thị')
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_variant_defining')
                    ->label('Tạo biến thể')
                    ->boolean(),
                TextColumn::make('values_count')
                    ->label('Số giá trị')
                    ->counts('values')
                    ->badge(),
                TextColumn::make('categories.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color('success')
                    ->limitList(3),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
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
