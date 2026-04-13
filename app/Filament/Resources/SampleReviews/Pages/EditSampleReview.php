<?php

namespace App\Filament\Resources\SampleReviews\Pages;

use App\Filament\Resources\SampleReviews\SampleReviewResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSampleReview extends EditRecord
{
    protected static string $resource = SampleReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
