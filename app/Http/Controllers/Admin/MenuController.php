<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Page; 
use App\Models\Category;
use App\Models\FieldCategory;
use App\Models\ProjectCategory;
use App\Models\PostCategory;

class MenuController extends Controller
{
    public function index()
    {
        // 1. Lấy dữ liệu nguồn cho cột trái
        $categories = Category::all();
        $fieldCategories = FieldCategory::all();
        $projectCategories = ProjectCategory::all();
        $postCategories = PostCategory::all();

        // 2. Định nghĩa các Route hệ thống (Hardcode các route quan trọng trong web.php)
        $systemRoutes = [
            'home' => 'Trang chủ',
            'contact.show' => 'Liên hệ',
            'products.index' => 'Tất cả sản phẩm',
            'frontend.posts.index' => 'Tin tức / Blog',
            'frontend.intro.index' => 'Giới thiệu chung',
            'frontend.projects.index' => 'Dự án',
            'frontend.fields.index' => 'Lĩnh vực',
        ];

        // 3. Lấy menu hiện tại (Chỉ lấy cha, con load đệ quy sau)
        $menuItems = MenuItem::where('parent_id', 0)->orderBy('position')->get();

        return view('admin.menus.index', compact('categories', 'systemRoutes', 'menuItems','fieldCategories','projectCategories','postCategories'));
    }

    // AJAX: Thêm item vào menu
    public function store(Request $request)
    {
        // Tạo item mới, mặc định nằm cuối cùng, cấp cao nhất
        $item = MenuItem::create([
            'title' => $request->title,
            'type' => $request->type, 
            'reference_id' => $request->reference_id,
            'url' => $request->url, 
            'parent_id' => 0,
            'position' => 9999
        ]);

        // Trả về HTML item để JS append vào cột phải ngay lập tức
        return response()->json([
            'status' => 'success',
            'html' => view('admin.menus.item', ['item' => $item])->render()
        ]);
    }

    // AJAX: Lưu sắp xếp & Cập nhật Tên/Link
    public function updateTree(Request $request)
    {
        $data = $request->menu; // Mảng JSON cấu trúc cây
        $this->saveTreeRecursive($data, 0);
        return response()->json(['status' => 'success']);
    }

    // AJAX: Xóa item
    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->children()->delete(); // Xóa con trước
        $item->delete();
        return response()->json(['status' => 'success']);
    }

    // Hàm đệ quy update
    private function saveTreeRecursive($items, $parentId)
    {
        foreach ($items as $index => $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            if ($menuItem) {
                // Update vị trí cha con
                $updateData = [
                    'parent_id' => $parentId,
                    'position' => $index,
                ];

                // Update tiêu đề (nếu người dùng sửa label)
                if (isset($itemData['title'])) {
                    $updateData['title'] = $itemData['title'];
                }

                // Update URL (chỉ áp dụng cho custom link)
                if (isset($itemData['url']) && $menuItem->type == 'custom') {
                    $updateData['url'] = $itemData['url'];
                }

                $menuItem->update($updateData);

                // Tiếp tục xử lý con nếu có
                if (isset($itemData['children'])) {
                    $this->saveTreeRecursive($itemData['children'], $menuItem->id);
                }
            }
        }
    }
}