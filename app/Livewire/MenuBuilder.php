<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\ProjectCategory;
use App\Models\FieldCategory;
use App\Support\SystemRouteScanner;

class MenuBuilder extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected string $view = 'livewire.menu-builder';
    protected int | string | array $columnSpan = 'full';

    public ?Menu $record = null;


    public function updateTree($tree)
    {
        $this->saveTree($tree, 0);
        $this->clearMenuCache();

        Notification::make()
            ->title('Đã lưu thứ tự menu')
            ->success()
            ->send();
    }

    public function moveRightAction(): Action
    {
        return Action::make('moveRight')
            ->icon('heroicon-m-arrow-right')
            ->color('gray')
            ->iconButton()
            ->tooltip('Thụt vào làm mục con')
            ->action(fn (array $arguments) => $this->moveRight($arguments['item']));
    }

    public function moveLeftAction(): Action
    {
        return Action::make('moveLeft')
            ->icon('heroicon-m-arrow-left')
            ->color('gray')
            ->iconButton()
            ->tooltip('Đưa ra ngoài một cấp')
            ->action(fn (array $arguments) => $this->moveLeft($arguments['item']));
    }

    public function moveRight($itemId)
    {
        $item = MenuItem::find($itemId);
        if (!$item) return;

        $previousSibling = MenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->where('position', '<', $item->position)
            ->orderBy('position', 'desc')
            ->first();

        if ($previousSibling) {
            $item->parent_id = $previousSibling->id;
            $item->position = MenuItem::where('menu_id', $item->menu_id)->where('parent_id', $previousSibling->id)->max('position') + 1;
            $item->save();
            $this->clearMenuCache();
        }
    }

    public function moveLeft($itemId)
    {
        $item = MenuItem::find($itemId);
        if (!$item || empty($item->parent_id)) return;

        $parent = MenuItem::find($item->parent_id);
        if ($parent) {
            $item->parent_id = $parent->parent_id ?: 0;
            $item->position = $parent->position + 1;
            $item->save();
            
            MenuItem::where('menu_id', $item->menu_id)
                ->where('parent_id', $item->parent_id)
                ->where('id', '!=', $item->id)
                ->where('position', '>=', $item->position)
                ->increment('position');

            $this->clearMenuCache();
        }
    }

    private function clearMenuCache(): void
    {
        cache()->forget('menu:header');
        cache()->forget('menu:footer');
        cache()->forget('menu:footer_col2');
        cache()->forget('menu:footer_col3');
    }

    private function saveTree($items, $parentId)
    {
        foreach ($items as $index => $item) {
            if (isset($item['id'])) {
                MenuItem::where('id', $item['id'])->update([
                    'parent_id' => $parentId ?: 0,
                    'position' => $index,
                ]);

                if (isset($item['children']) && is_array($item['children'])) {
                    $this->saveTree($item['children'], $item['id']);
                }
            }
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Tiêu đề hiển thị')
                ->required(),

            TextInput::make('icon')
                ->label('Icon (Font Awesome)')
                ->placeholder('fas fa-home, fas fa-phone, ...')
                ->helperText('Tên class icon, VD: fas fa-home'),
                
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
                ->label('Liên kết chỉ định')
                ->options(function (callable $get) {
                    return match ($get('type')) {
                        'category' => Category::pluck('name', 'id'),
                        'product' => Product::pluck('name', 'id'),
                        'post_category' => PostCategory::pluck('name', 'id'),
                        'post' => Post::pluck('title', 'id'),
                        'project_category' => ProjectCategory::pluck('name', 'id'),
                        'field_category' => FieldCategory::pluck('name', 'id'),
                        default => [],
                    };
                })
                ->searchable()
                ->preload()
                ->visible(fn (callable $get) => !in_array($get('type'), ['custom', 'system_route', null])),
                
            TextInput::make('url')
                ->label('Đường dẫn URL')
                ->placeholder('https://example.com hoặc /duong-dan')
                ->visible(fn (callable $get) => $get('type') === 'custom'),

            Select::make('target')
                ->label('Cách mở')
                ->options([
                    '_self' => 'Cùng tab (mặc định)',
                    '_blank' => 'Tab mới',
                ])
                ->default('_self'),
        ];
    }

    public function createAction(): Action
    {
        return CreateAction::make('create')
            ->label('Thêm mục mới')
            ->icon('heroicon-o-plus')
            ->color('primary')
            ->model(MenuItem::class)
            ->form($this->getFormSchema())
            ->mutateFormDataUsing(function (array $data): array {
                $data['menu_id'] = $this->record->id;
                $data['parent_id'] = 0;
                $data['position'] = MenuItem::where('menu_id', $this->record->id)->max('position') + 1;
                return $data;
            })
            ->after(fn () => $this->clearMenuCache());
    }

    public function addChildAction(): Action
    {
        return CreateAction::make('addChild')
            ->label('Thêm con')
            ->icon('heroicon-o-plus-circle')
            ->color('success')
            ->button()
            ->size('sm')
            ->model(MenuItem::class)
            ->form($this->getFormSchema())
            ->mutateFormDataUsing(function (array $data, array $arguments): array {
                $parentId = $arguments['item'] ?? 0;
                $data['menu_id'] = $this->record->id;
                $data['parent_id'] = $parentId;
                $data['position'] = MenuItem::where('menu_id', $this->record->id)
                    ->where('parent_id', $parentId)
                    ->max('position') + 1;
                return $data;
            })
            ->after(fn () => $this->clearMenuCache());
    }

    public function editAction(): Action
    {
        return EditAction::make('edit')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->button()
            ->size('sm')
            ->model(MenuItem::class)
            ->record(fn (array $arguments) => MenuItem::find($arguments['item']))
            ->form($this->getFormSchema())
            ->after(fn () => $this->clearMenuCache());
    }

    public function deleteAction(): Action
    {
        return DeleteAction::make('delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->button()
            ->size('sm')
            ->model(MenuItem::class)
            ->record(fn (array $arguments) => MenuItem::find($arguments['item']))
            ->after(fn () => $this->clearMenuCache());
    }

    public function getItems()
    {
        if (!$this->record) return collect();
        return $this->record->items()
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->with('childs')
            ->orderBy('position')
            ->get();
    }
}
