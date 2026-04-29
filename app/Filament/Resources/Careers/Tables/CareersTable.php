<?php

namespace App\Filament\Resources\Careers\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareersTable
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
                    ->label('Vị trí tuyển dụng')
                    ->searchable()
                    ->sortable()
                    ->limit(45)
                    ->weight('medium'),

                TextColumn::make('salary')
                    ->label('Mức lương')
                    ->searchable()
                    ->limit(25)
                    ->placeholder('Thỏa thuận'),

                TextColumn::make('quantity')
                    ->label('SL')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('location')
                    ->label('Địa điểm')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('type')
                    ->label('Hình thức')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('deadline')
                    ->label('Hạn ứng tuyển')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Không giới hạn'),

                IconColumn::make('status')
                    ->label('Kích hoạt')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_home')
                    ->label('Trang chủ')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->searchable()
                    ->limit(35)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('education')
                    ->label('Trình độ')
                    ->searchable()
                    ->limit(30)
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
            ->defaultSort('position')
            ->reorderable('position')
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
