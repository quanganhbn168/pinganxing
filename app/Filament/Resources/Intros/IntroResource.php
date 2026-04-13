<?php

namespace App\Filament\Resources\Intros;

use App\Filament\Resources\Intros\Pages\CreateIntro;
use App\Filament\Resources\Intros\Pages\EditIntro;
use App\Filament\Resources\Intros\Pages\ListIntros;
use App\Filament\Resources\Intros\Schemas\IntroForm;
use App\Filament\Resources\Intros\Tables\IntrosTable;
use App\Models\Intro;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IntroResource extends Resource
{
    protected static ?string $model = Intro::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Nội dung Trang chủ';
    }

    public static function getModelLabel(): string
    {
        return 'Giới thiệu';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Giới thiệu';
    }

    public static function form(Schema $schema): Schema
    {
        return IntroForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IntrosTable::configure($table);
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
            'index' => ListIntros::route('/'),
            'create' => CreateIntro::route('/create'),
            'edit' => EditIntro::route('/{record}/edit'),
        ];
    }
}
