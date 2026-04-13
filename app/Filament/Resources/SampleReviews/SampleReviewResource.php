<?php

namespace App\Filament\Resources\SampleReviews;

use App\Filament\Resources\SampleReviews\Pages\CreateSampleReview;
use App\Filament\Resources\SampleReviews\Pages\EditSampleReview;
use App\Filament\Resources\SampleReviews\Pages\ListSampleReviews;
use App\Filament\Resources\SampleReviews\Schemas\SampleReviewForm;
use App\Filament\Resources\SampleReviews\Tables\SampleReviewsTable;
use App\Models\SampleReview;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SampleReviewResource extends Resource
{
    protected static ?string $model = SampleReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;
    protected static string|UnitEnum|null $navigationGroup = 'Quản trị Web';
    protected static ?string $modelLabel = 'Mẫu Đánh Giá';
    protected static ?string $pluralModelLabel = 'Mẫu Đánh Giá Khách Hàng';

    protected static ?string $recordTitleAttribute = 'content';

    public static function form(Schema $schema): Schema
    {
        return SampleReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SampleReviewsTable::configure($table);
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
            'index' => ListSampleReviews::route('/'),
            'create' => CreateSampleReview::route('/create'),
            'edit' => EditSampleReview::route('/{record}/edit'),
        ];
    }
}
