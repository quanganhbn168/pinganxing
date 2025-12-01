<?php
namespace App\Services;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
class MenuBuilderService
{
    public function getHeaderMenu()
    {
        Cache::forget('header_menu_structure');
        return Cache::rememberForever('header_menu_structure', function () {
            $menuConfig = config('menu_top', []);
            return $this->buildMenu($menuConfig);
        });
    }
    protected function buildMenu(array $menuItems)
    {
        $builtMenu = [];
        foreach ($menuItems as $item) {
            if (isset($item['route']) && Route::has($item['route'])) {
                $item['url'] = route($item['route']);
            } else {
                $item['url'] = url($item['url'] ?? '#');
            }
            if (isset($item['dynamic_children'])) {
                $item['children'] = array_merge($item['children'] ?? [], $this->fetchDynamicChildren($item['dynamic_children']));
            }
            if (!empty($item['children'])) {
                $item['children'] = $this->buildMenu($item['children']);
            }
            $builtMenu[] = $item;
        }
        return $builtMenu;
    }
    protected function fetchDynamicChildren(array $config)
    {
        $modelClass = $config['model'];
        if (!class_exists($modelClass)) {
            return [];
        }
        $children = collect();
        if (isset($config['method']) && method_exists($modelClass, $config['method'])) {
            $method = $config['method'];
            $children = $modelClass::$method();
        } else {
            $standaloneModels = [
                \App\Models\Project::class,
            ];
            if (in_array($modelClass, $standaloneModels)) {
                $children = $modelClass::where('status', 1)->get();
            } else {
                $columnName = 'parent_id';
                $modelColumnMap = [
                    \App\Models\Product::class => 'category_id',
                    \App\Models\Service::class => 'service_category_id',
                ];
                if (array_key_exists($modelClass, $modelColumnMap)) {
                    $columnName = $modelColumnMap[$modelClass];
                }
                $parentId = $config['parent_id'] ?? null;
                $children = $modelClass::where($columnName, $parentId)->where('status', 1)->get();
            }
        }
        $routeName = $config['route_name'] ?? null;
        return $children->map(function ($child) use ($modelClass, $routeName, $config) {
            $url = '#';
            if ($routeName && Route::has($routeName) && isset($child->slug)) {
                $url = route($routeName, $child->slug);
            } elseif (isset($child->slug)) {
                $url = url($child->slug);
            }
            $grandchildren = [];
            $hierarchicalModels = [
                \App\Models\Category::class,
                \App\Models\ServiceCategory::class,
                \App\Models\PostCategory::class,
                \App\Models\ProjectCategory::class,
            ];
            if (in_array($modelClass, $hierarchicalModels) && !isset($config['method'])) {
                $grandChildrenConfig = [
                    'model' => $modelClass,
                    'parent_id' => $child->id,
                    'route_name' => $routeName
                ];
                $grandchildren = $this->fetchDynamicChildren($grandChildrenConfig);
            }
            return [
                'title' => $child->title ?? $child->name,
                'url' => $url,
                'children' => $grandchildren
            ];
        })->toArray();
    }
    public function getMenuFooter()
    {
        return Cache::rememberForever('footer_menu_structure', function () {
            $menuConfig = config('menu_footer', []);
            $builtMenu = [];
            foreach ($menuConfig as $column) {
                $builtItems = [];
                if (!empty($column['items'])) {
                    foreach ($column['items'] as $item) {
                        if (isset($item['route']) && Route::has($item['route'])) {
                            $item['url'] = route($item['route']);
                        } else {
                            $item['url'] = url($item['url'] ?? '#');
                        }
                        $builtItems[] = $item;
                    }
                }
                $column['items'] = $builtItems;
                $builtMenu[] = $column;
            }
            return $builtMenu;
        });
    }
}