<?php

namespace App\Filament\Resources\SampleReviews\Pages;

use App\Filament\Resources\SampleReviews\SampleReviewResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSampleReviews extends ListRecords
{
    protected static string $resource = SampleReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
