<?php

namespace App\Filament\Resources\Careers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                \Awcodes\Curator\Components\Tables\CuratorColumn::make('image')
                    ->label('Ảnh')
                    ->circular()
                    ->size(40),
                TextColumn::make('salary')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('education')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('deadline')
                    ->date()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),
                IconColumn::make('is_home')
                    ->boolean(),
                TextColumn::make('position')
                    ->numeric()
                    ->sortable(),
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
