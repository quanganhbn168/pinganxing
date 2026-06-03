<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\SlugGenerator;
use Illuminate\Database\Seeder;

class ProductCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Camera quan sát',
            'Đầu ghi hình',
            'Thiết bị mạng',
            'Máy bán hàng / POS',
            'Máy in',
            'Máy quét mã vạch / PDA',
            'Vật tư mã vạch',
            'Phần mềm',
            'Kiểm soát ra vào',
            'Lưu trữ',
            'Thiết bị hiển thị & âm thanh',
            'Phụ kiện',
        ];

        $slugGenerator = app(SlugGenerator::class);

        foreach ($categories as $index => $name) {
            $category = Category::query()->firstOrNew(['name' => $name]);

            $category->fill([
                'parent_id' => null,
                'status' => true,
                'is_home' => true,
                'is_menu' => true,
                'is_footer' => true,
                'position' => $index + 1,
                'meta_title' => $name,
            ]);

            $category->save();

            $slugGenerator->syncModel($category, $name, optional($category->slugData)->id);
        }
    }
}
