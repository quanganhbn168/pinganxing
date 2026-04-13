<?php

namespace App\Filament\Resources\SampleReviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SampleReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rating')
                    ->label('Số sao')
                    ->numeric()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '5', '4' => 'success',
                        '3' => 'warning',
                        '2', '1' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('content')
                    ->label('Nội dung mẫu')
                    ->searchable()
                    ->wrap(),
                \Filament\Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Kích hoạt')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('rating', 'desc')
            ->reorderable('sort_order')
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
