<?php

namespace App\Filament\Resources\Posts\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->defaultSort('position')
->reorderable('position')
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->size(56)
                    ->circular(),

                TextColumn::make('title')
                    ->label('Tiêu đề')
                    ->searchable()
                    ->sortable()
                    ->limit(55)
                    ->weight('medium'),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->limit(30),

                TextColumn::make('tags.name')
                    ->label('Thẻ')
                    ->badge()
                    ->separator(',')
                    ->limitList(3)
                    ->toggleable(),

                ToggleColumn::make('status')
                    ->label('Kích hoạt')
                    ->sortable(),

                ToggleColumn::make('is_featured')
                    ->label('Nổi bật')
                    ->sortable(),

                ToggleColumn::make('is_home')
                    ->label('Trang chủ')
                    ->sortable(),

                ToggleColumn::make('is_menu')
                    ->label('Menu')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ToggleColumn::make('is_footer')
                    ->label('Footer')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                CuratorColumn::make('banner')
                    ->label('Banner')
                    ->size(56)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->searchable()
                    ->limit(45)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('post_category_id')
                    ->label('Danh mục')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable(),

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
