<?php

namespace App\Filament\Resources\AgencyRequests\Pages;

use App\Filament\Resources\AgencyRequests\AgencyRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgencyRequests extends ListRecords
{
    protected static string $resource = AgencyRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
