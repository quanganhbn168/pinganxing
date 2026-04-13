<?php

namespace App\Filament\Resources\ConsultingRequests;

use App\Filament\Resources\ConsultingRequests\Pages\CreateConsultingRequest;
use App\Filament\Resources\ConsultingRequests\Pages\EditConsultingRequest;
use App\Filament\Resources\ConsultingRequests\Pages\ListConsultingRequests;
use App\Filament\Resources\ConsultingRequests\Schemas\ConsultingRequestForm;
use App\Filament\Resources\ConsultingRequests\Tables\ConsultingRequestsTable;
use App\Models\ConsultingRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConsultingRequestResource extends Resource
{
    protected static ?string $model = ConsultingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    public static function getNavigationGroup(): ?string
    {
        return 'Giao tiếp Khách hàng';
    }

    public static function getModelLabel(): string
    {
        return 'Yêu cầu Tư vấn';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Yêu cầu Tư vấn';
    }

    public static function form(Schema $schema): Schema
    {
        return ConsultingRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsultingRequestsTable::configure($table);
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
            'index' => ListConsultingRequests::route('/'),
            'create' => CreateConsultingRequest::route('/create'),
            'edit' => EditConsultingRequest::route('/{record}/edit'),
        ];
    }
}
