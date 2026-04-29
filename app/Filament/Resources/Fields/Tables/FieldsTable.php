<?php

namespace App\Filament\Resources\Fields\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Awcodes\Curator\Components\Tables\CuratorColumn;
class FieldsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->size(40),
                TextColumn::make('name')
                    ->label('Tên lĩnh vực')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Kích hoạt')
                    ->boolean(),
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
