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
                        'category' => 'Danh mục Hàng hóa',
                        'product' => 'Sản phẩm',
                        'post_category' => 'Chuyên mục Tin tức',
                        'post' => 'Bài viết Đơn lẻ',
                        'project_category' => 'Nhóm Dự án',
                        'field_category' => 'Nhóm Lĩnh vực',
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
                            'category' => \App\Models\Category::pluck('name', 'id'),
                            'product' => \App\Models\Product::pluck('name', 'id'),
                            'post_category' => \App\Models\PostCategory::pluck('name', 'id'),
                            'post' => \App\Models\Post::pluck('title', 'id'),
                            'project_category' => \App\Models\ProjectCategory::pluck('name', 'id'),
                            'field_category' => \App\Models\FieldCategory::pluck('name', 'id'),
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
