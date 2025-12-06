<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GlobalBulkActionController extends Controller
{
    // KHAI BÁO CÁC MODEL ĐƯỢC PHÉP XỬ LÝ Ở ĐÂY
    protected $mapping = [
        'product'          => \App\Models\Product::class,
        'category'         => \App\Models\Category::class,
        'post'             => \App\Models\Post::class,
        'post_category'    => \App\Models\PostCategory::class,
        'slide'            => \App\Models\Slide::class,
        'brand'            => \App\Models\Brand::class,
        'testimonial'      => \App\Models\Testimonial::class,
        'field'            => \App\Models\Field::class,
        'field_category'   => \App\Models\FieldCategory::class,
        'project'          => \App\Models\Project::class,
        'project_category' => \App\Models\ProjectCategory::class,
        'intro'            => \App\Models\Intro::class,
        'career'            => \App\Models\Career::class,
        'media_file' => \App\Models\MediaFile::class,
        'career_application' => \App\Models\CareerApplication::class,
        'user' => \App\Models\User::class,
        'material' => \App\Models\Material::class,
        // Thêm model mới vào đây là xong
    ];

    public function handle(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'action' => 'required|string',
            'model'  => 'required|string',
        ]);

        $modelKey = $request->input('model');
        $action   = $request->input('action');
        $ids      = $request->input('ids');

        // 1. Kiểm tra Model có hợp lệ không
        if (!array_key_exists($modelKey, $this->mapping)) {
            return back()->withErrors(['message' => 'Model không hợp lệ hoặc chưa được đăng ký.']);
        }

        $modelClass = $this->mapping[$modelKey];
        $count = count($ids);

        // 2. Xử lý hành động
        switch ($action) {
            case 'delete':
                // Nếu model có cột deleted_at (SoftDelete) thì nó sẽ xóa mềm, còn không là xóa cứng
                // Nếu muốn kích hoạt Observer (để xóa ảnh), hãy dùng vòng lặp. 
                // Còn để tối ưu tốc độ thì dùng whereIn -> delete()
                
                // Cách 1: Nhanh (Fastest)
                $modelClass::whereIn('id', $ids)->delete();
                
                $msg = "Đã xóa $count mục thành công.";
                break;

            case 'active':
                // Ví dụ logic kích hoạt hàng loạt
                if (Schema::hasColumn(app($modelClass)->getTable(), 'status')) {
                    $modelClass::whereIn('id', $ids)->update(['status' => 1]);
                    $msg = "Đã kích hoạt $count mục.";
                }
                break;
                
            case 'inactive':
                if (Schema::hasColumn(app($modelClass)->getTable(), 'status')) {
                    $modelClass::whereIn('id', $ids)->update(['status' => 0]);
                    $msg = "Đã ẩn $count mục.";
                }
                break;

            default:
                return back()->withErrors(['message' => 'Hành động chưa được hỗ trợ.']);
        }

        return back()->with('success', $msg ?? 'Thao tác thành công.');
    }
}