<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên Thẻ')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                ColorColumn::make('color')
                    ->label('Màu sắc'),
                TextColumn::make('products_count')
                    ->label('Sản phẩm')
                    ->counts('products')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('posts_count')
                    ->label('Bài viết')
                    ->counts('posts')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('projects_count')
                    ->label('Dự án')
                    ->counts('projects')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Mô tả')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label('Thứ tự')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
