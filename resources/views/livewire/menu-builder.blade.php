<x-filament-widgets::widget>
    <div x-data="menuTree()" x-init="initSortable($el)">
        <style>
            .mb-tree { display: flex; flex-direction: column; gap: 0.5rem; list-style: none; padding: 0; margin: 0; min-height: 50px; }
            .mb-node { border: 1px solid #e5e7eb; border-radius: 0.75rem; background-color: #ffffff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); overflow: hidden; }
            .mb-header { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background-color: #ffffff; border-bottom: 1px solid #e5e7eb; cursor: grab; transition: background-color 0.2s; }
            .mb-node > .mb-header:last-child { border-bottom: none; }
            .mb-header:hover { background-color: #f9fafb; }
            .mb-header:active { cursor: grabbing; background-color: #f9fafb; }
            .mb-title-wrap { display: flex; align-items: center; gap: 0.75rem; }
            .mb-title { font-weight: 500; font-size: 0.875rem; color: #111827; }
            .mb-actions { display: flex; align-items: center; gap: 0.5rem; }
            .mb-children { padding: 0.5rem 0.5rem 0.5rem 2.5rem; list-style: none; }
            .mb-icon { width: 1.25rem; height: 1.25rem; color: #9ca3af; }
            
            /* Dark Mode Support */
            .dark .mb-node { border-color: rgba(255, 255, 255, 0.1); background-color: #18181b; } /* gray-900 / zinc-900 */
            .dark .mb-header { background-color: rgba(255, 255, 255, 0.02); border-bottom-color: rgba(255, 255, 255, 0.1); }
            .dark .mb-header:hover { background-color: rgba(255, 255, 255, 0.08); }
            .dark .mb-title { color: #f9fafb; }
            .dark .mb-icon { color: #6b7280; }
        </style>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
            {{ $this->createAction }}
        </div>

        <x-filament::section>
            @if($this->getItems()->count() === 0)
                <div style="text-align: center; padding: 2rem 0; color: #6b7280;">Chưa có phần tử nào. Hãy tạo nút menu đầu tiên!</div>
            @endif
            
            <ul id="menu-tree-root" class="nested-sortable mb-tree">
                @include('livewire.menu-builder-node', ['items' => $this->getItems()])
            </ul>
        </x-filament::section>

        <x-filament-actions::modals />

        @script
        <script>
            Alpine.data('menuTree', () => ({
                initSortable(el) {
                if (!window.Sortable) {
                    let script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
                    script.onload = () => this.setupWithHook(el);
                    document.head.appendChild(script);
                } else {
                    this.setupWithHook(el);
                }
            },
            setupWithHook(el) {
                this.setup(el);
                
                if (window.Livewire) {
                    Livewire.hook('commit', ({ succeed }) => {
                        succeed(() => {
                            setTimeout(() => { this.setup(el); }, 50);
                        });
                    });
                }
            },
            setup(el) {
                    // Remove previous instances if any
                    if(this.sortables) {
                        this.sortables.forEach(s => s.destroy());
                    }
                    this.sortables = [];

                    let containers = Array.from(el.querySelectorAll('.nested-sortable'));
                    containers.forEach((container) => {
                        let s = new Sortable(container, {
                            group: 'menuTree',
                            animation: 150,
                            fallbackOnBody: true,
                            swapThreshold: 0.65,
                            handle: '.handle',
                            onEnd: (evt) => {
                                this.saveTree(el);
                            }
                        });
                        this.sortables.push(s);
                    });
                },
                saveTree(el) {
                    let root = el.querySelector('#menu-tree-root');
                    let data = this.serialize(root);
                    $wire.updateTree(data);
                },
                serialize(sortableContainer) {
                    let serialized = [];
                    let children = sortableContainer.children;
                    for (let i = 0; i < children.length; i++) {
                        let li = children[i];
                        if (!li.dataset.id) continue;
                        
                        let id = li.dataset.id;
                        let childUl = li.querySelector('.nested-sortable');
                        let item = { id: id };
                        
                        if (childUl && childUl.children.length > 0) {
                            item.children = this.serialize(childUl);
                        }
                        serialized.push(item);
                    }
                    return serialized;
                }
            }));
        </script>
        @endscript
    </div>
</x-filament-widgets::widget>
