<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Awcodes\Curator\Components\Tables\CuratorColumn;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh đại diện')
                    ->size(40),
                TextColumn::make('name')
                    ->label('Tên dự án')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags.name')
                    ->label('Thẻ')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('status')
                    ->label('Trạng thái'),
                ToggleColumn::make('is_home')
                    ->label('Trang chủ'),

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
                SelectFilter::make('tags')
                    ->label('Thẻ')
                    ->relationship('tags', 'name', modifyQueryUsing: fn ($query) => $query->ordered())
                    ->multiple()
                    ->preload()
                    ->searchable(),
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
