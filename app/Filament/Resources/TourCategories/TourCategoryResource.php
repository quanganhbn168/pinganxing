<?php

namespace App\Filament\Resources\TourCategories;

use App\Filament\Resources\TourCategories\Pages\ManageTourCategories;
use App\Models\TourCategory;
use App\Traits\HasSeo;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TourCategoryResource extends Resource
{
    protected static ?string $model = TourCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->components([
                Section::make('Thông tin danh mục tour')
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'md' => 2,
                        ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Tên danh mục')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                TextInput::make('slug')
                                    ->label('Đường dẫn')
                                    ->required()
                                    ->unique(TourCategory::class, 'slug', ignoreRecord: true),

                                Select::make('parent_id')
                                    ->label('Danh mục cha')
                                    ->options(fn () => TourCategory::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),

                                Textarea::make('description')
                                    ->label('Mô tả ngắn')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('Nội dung')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('tour-categories/content')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                Grid::make(1)
                    ->schema([
                        Section::make('Hình ảnh')
                            ->schema([
                                CuratorPicker::make('image_id')
                                    ->label('Ảnh đại diện')
                                    ->columnSpanFull(),

                                CuratorPicker::make('banner_id')
                                    ->label('Banner')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Cài đặt')
                            ->schema([
                                Toggle::make('status')
                                    ->label('Hiển thị')
                                    ->default(true),

                                Toggle::make('is_home')
                                    ->label('Hiện ở điểm đến nổi bật')
                                    ->default(false),

                                TextInput::make('position')
                                    ->label('Vị trí')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        HasSeo::seoSection(),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                CuratorColumn::make('image_id')
                    ->label('Ảnh')
                    ->size(50),
                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->searchable(),
                ToggleColumn::make('is_home')
                    ->label('Nổi bật'),
                ToggleColumn::make('status')
                    ->label('Hiển thị'),
                TextColumn::make('position')
                    ->label('Vị trí')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTourCategories::route('/'),
        ];
    }
}
