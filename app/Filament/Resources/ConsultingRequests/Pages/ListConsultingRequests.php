<?php

namespace App\Filament\Resources\ConsultingRequests\Pages;

use App\Filament\Resources\ConsultingRequests\ConsultingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsultingRequests extends ListRecords
{
    protected static string $resource = ConsultingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
