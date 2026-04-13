<?php

namespace App\Filament\Resources\Fields;

use App\Filament\Resources\Fields\Pages\CreateField;
use App\Filament\Resources\Fields\Pages\EditField;
use App\Filament\Resources\Fields\Pages\ListFields;
use App\Filament\Resources\Fields\Schemas\FieldForm;
use App\Filament\Resources\Fields\Tables\FieldsTable;
use App\Models\Field;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    public static function getNavigationGroup(): ?string
    {
        return 'Lĩnh vực & Dịch vụ';
    }

    public static function getModelLabel(): string
    {
        return 'Lĩnh vực Kinh doanh';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Lĩnh vực Kinh doanh';
    }

    public static function form(Schema $schema): Schema
    {
        return FieldForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FieldsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFields::route('/'),
            'create' => CreateField::route('/create'),
            'edit' => EditField::route('/{record}/edit'),
        ];
    }
}
