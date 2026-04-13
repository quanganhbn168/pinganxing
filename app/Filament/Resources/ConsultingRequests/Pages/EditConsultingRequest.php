<?php

namespace App\Filament\Resources\ConsultingRequests\Pages;

use App\Filament\Resources\ConsultingRequests\ConsultingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConsultingRequest extends EditRecord
{
    protected static string $resource = ConsultingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
