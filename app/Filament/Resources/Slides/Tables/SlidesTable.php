<?php

namespace App\Filament\Resources\Slides\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SlidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->size(60),
                TextColumn::make('title')
                    ->label('Tiêu đề / Link')
                    ->searchable(['title', 'link'])
                    ->description(fn ($record): ?string => $record->link),
                TextColumn::make('type')
                    ->label('Loại hiển thị')
                    ->badge()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Thứ tự sắp xếp')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                \Filament\Tables\Columns\ToggleColumn::make('status')
                    ->label('Trạng thái'),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
