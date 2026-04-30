<?php

namespace App\Filament\Resources\FieldCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class FieldCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->columns([
                CuratorColumn::make('image')
                    ->label('Ảnh / Icon')
                    ->size(40),
                TextColumn::make('name')
                ->label('Tên danh mục')
                    ->searchable(),
                TextColumn::make('parent.name')
                ->label('Danh mục gốc')
                ->default("-")
                ->badge()
                    ->searchable(),

                ToggleColumn::make('status')
                ->label('Kích hoạt'),

                ToggleColumn::make('is_home')
                ->label('Tiêu biểu'),

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
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        if ($record->fields()->exists()) {
                            Notification::make()->warning()->title('Không thể xóa!')->body('Danh mục đang chứa lĩnh vực.')->send();
                            $action->cancel();
                        }
                        if ($record->children()->exists()) {
                            Notification::make()->warning()->title('Không thể xóa!')->body('Danh mục này đang có thư mục con.')->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
