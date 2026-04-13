@foreach($items as $item)
<li data-id="{{ $item->id }}" class="mb-node">
    <div class="handle mb-header">
        <div class="mb-title-wrap">
            <x-filament::icon icon="heroicon-o-bars-3" class="mb-icon" />
            @if($item->icon)<i class="{{ $item->icon }} text-gray-400 text-xs"></i>@endif
            <span class="mb-title">{{ $item->title }}</span>
            <x-filament::badge color="{{ $item->type === 'system_route' ? 'primary' : ($item->type === 'custom' ? 'warning' : 'info') }}" size="sm">
                {{ match($item->type) {
                    'system_route' => 'Hệ thống',
                    'custom' => 'URL',
                    'category' => 'Danh mục',
                    'post_category' => 'Chuyên mục',
                    'project_category' => 'Nhóm DA',
                    'field_category' => 'Lĩnh vực',
                    default => $item->type,
                } }}
            </x-filament::badge>
        </div>
        <div class="mb-actions">
            {{ ($this->addChildAction)(['item' => $item->id]) }}
            @if(!empty($item->parent_id) && $item->parent_id != 0)
                {{ ($this->moveLeftAction)(['item' => $item->id]) }}
            @endif
            {{ ($this->moveRightAction)(['item' => $item->id]) }}
            {{ ($this->editAction)(['item' => $item->id]) }}
            {{ ($this->deleteAction)(['item' => $item->id]) }}
        </div>
    </div>
    
    {{-- Nested children container (luôn render để drag-drop vào được) --}}
    <ul class="nested-sortable mb-tree mb-children" @if(!$item->childs || $item->childs->count() === 0) style="display:none; min-height: 30px;" @endif>
        @if($item->childs && $item->childs->count() > 0)
            @include('livewire.menu-builder-node', ['items' => $item->childs])
        @endif
    </ul>
</li>
@endforeach
