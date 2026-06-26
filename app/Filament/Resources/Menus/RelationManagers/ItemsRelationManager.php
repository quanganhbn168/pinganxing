<?php

namespace App\Filament\Resources\Menus\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Support\SystemRouteScanner;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Tiêu đề hiển thị')
                    ->required(),
                    
                Select::make('type')
                    ->label('Loại liên kết')
                    ->required()
                    ->options([
                        'system_route' => 'Trang hệ thống',
                        'page' => 'Trang đơn lẻ',
                        'post_category' => 'Chuyên mục Tin tức',
                        'post' => 'Bài viết Đơn lẻ',
                        'project_category' => 'Danh mục Dự án',
                        'project' => 'Dự án Đơn lẻ',
                        'field_category' => 'Danh mục Lĩnh vực',
                        'field' => 'Lĩnh vực Đơn lẻ',
                        'service_category' => 'Danh mục Dịch vụ',
                        'service' => 'Dịch vụ Đơn lẻ',
                        'custom' => 'Link tự do',
                    ])
                    ->default('system_route')
                    ->live(),

                Select::make('url')
                    ->label('Chọn trang')
                    ->options(fn () => SystemRouteScanner::getOptions())
                    ->searchable()
                    ->required()
                    ->visible(fn (callable $get) => $get('type') === 'system_route'),
                    
                Select::make('reference_id')
                    ->label('Liên kết chỉ định (Data Source)')
                    ->options(function (callable $get) {
                        return match ($get('type')) {
                            'page' => \App\Models\Page::pluck('title', 'id'),
                            'post_category' => \App\Models\PostCategory::pluck('name', 'id'),
                            'post' => \App\Models\Post::pluck('title', 'id'),
                            'project_category' => \App\Models\ProjectCategory::pluck('name', 'id'),
                            'project' => \App\Models\Project::pluck('name', 'id'),
                            'field_category' => \App\Models\FieldCategory::pluck('name', 'id'),
                            'field' => \App\Models\Field::pluck('name', 'id'),
                            'service_category' => \App\Models\ServiceCategory::pluck('name', 'id'),
                            'service' => \App\Models\Service::pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->searchable()
                    ->preload()
                    ->visible(fn (callable $get) => !in_array($get('type'), ['custom', 'system_route', null])),
                    
                TextInput::make('url')
                    ->label('Đường dẫn URL')
                    ->placeholder('https://example.com hoặc /duong-dan-bat-ky')
                    ->visible(fn (callable $get) => $get('type') === 'custom'),
                    
                TextInput::make('position')
                    ->label('Thứ tự sắp xếp')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('reference_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('url')
                    ->searchable(),
                TextColumn::make('parent_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('position')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->slideOver(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
