<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->defaultSort('position')
            ->columns([
                TextColumn::make('position')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('parent.name')
                    ->label('Danh mục cha')
                    ->default('—')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('services_count')
                    ->label('Dịch vụ')
                    ->counts('services')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                ToggleColumn::make('status')
                    ->label('Kích hoạt')
                    ->alignCenter(),
                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, $record) {
                        if ($record->services()->exists()) {
                            Notification::make()->warning()->title('Không thể xóa!')->body('Danh mục đang chứa dịch vụ.')->send();
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
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            $prevented = 0;
                            foreach ($records as $record) {
                                if ($record->services()->exists() || $record->children()->exists()) {
                                    $prevented++;
                                } else {
                                    $record->delete();
                                }
                            }
                            if ($prevented > 0) {
                                Notification::make()
                                    ->warning()
                                    ->title('Đã bỏ qua một số mục!')
                                    ->body("Không thể xóa {$prevented} danh mục vì đang chứa dịch vụ hoặc thư mục con.")
                                    ->send();
                            } else {
                                Notification::make()->success()->title('Đã xóa thành công!')->send();
                            }
                        }),
                ]),
            ]);
    }
}
