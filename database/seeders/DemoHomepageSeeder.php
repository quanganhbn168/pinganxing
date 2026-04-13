<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Slide;
use App\Enums\SliderType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DemoHomepageSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. SERVICES
        // service_categories has NO slug, NO is_home
        $sCat = ServiceCategory::firstOrCreate(
            ['name' => 'Giải pháp hệ thống'], 
            ['status' => 1]
        );
        $sCat->save();

        $services = ['Triển khai ERP', 'Bảo mật dữ liệu', 'Cloud Computing', 'Quản trị nhân sự', 'Tích hợp phần cứng', 'AI & Data'];
        foreach($services as $s) {
            $service = Service::firstOrCreate(
                ['name' => $s],
                [
                    'service_category_id' => $sCat->id,
                    'description' => 'Giải pháp tối ưu hóa vận hành cho doanh nghiệp với tiêu chuẩn quốc tế.',
                    'content' => 'Nội dung chi tiết...',
                    'status' => 1,
                ]
            );
            $service->is_home = 1;
            $service->save();
        }

        // 2. PROJECTS
        // project_categories has NO slug, HAS is_home
        $pCat = ProjectCategory::firstOrCreate(
            ['name' => 'Dự án Tiêu biểu'], 
            ['status' => 1]
        );
        $pCat->is_home = 1;
        $pCat->save();

        for($i=1; $i<=8; $i++) {
            $project = Project::firstOrCreate(
                ['name' => "Dự án hệ thống Khách sạn $i"],
                [
                    'project_category_id' => $pCat->id,
                    'description' => 'Triển khai thành công hệ thống khối phần mềm quản lý cho đối tác chiến lược trong năm 2025 với hệ sinh thái thiết bị POS.',
                    'status' => 1,
                ]
            );
            $project->is_home = 1;
            $project->save();
        }

        // 3. PRODUCTS
        // categories has NO slug, HAS is_home
        $cCat = Category::firstOrCreate(
            ['name' => 'Giải pháp phần cứng POS'], 
            ['status' => 1]
        );
        $cCat->is_home = 1;
        $cCat->save();

        for($i=1; $i<=8; $i++) {
            // products has NO slug
            $product = Product::firstOrCreate(
                ['name' => "Máy POS Cảm Ứng CNET-$i"],
                [
                    'code' => "POS-CNET-$i",
                    'category_id' => $cCat->id,
                    'price' => rand(10, 50) * 1000000,
                    'price_discount' => rand(1, 9) * 1000000,
                    'stock' => 100,
                    'status' => 1,
                    'is_home' => 1,
                    'is_on_sale' => ($i % 2 == 0) ? 1 : 0
                ]
            );
            $product->is_home = 1;
            $product->save();
        }

        // 4. POSTS
        // post_categories has NO slug, HAS is_home
        $poCat = PostCategory::firstOrCreate(
            ['name' => 'Tin Công Nghệ ERP'], 
            ['status' => 1]
        );
        $poCat->is_home = 1;
        $poCat->save();

        $posts = [
            'Xu hướng AI trong quản trị ERP',
            'Bảo mật dữ liệu đám mây: Thách thức',
            'CNETPos nâng cấp hạ tầng server',
            'Cách tối ưu quy trình bán lẻ POS'
        ];
        foreach($posts as $p) {
            $post = Post::firstOrCreate(
                ['title' => $p],
                [
                    'post_category_id' => $poCat->id,
                    'description' => 'Nội dung tóm tắt...',
                    'content' => 'Nội dung bài viết chi tiết...',
                    'status' => 1,
                ]
            );
            $post->is_home = 1;
            $post->save();
        }

        // 5. SLIDES
        $typeVal = \App\Enums\SliderType::HOME;
        $slide1 = Slide::firstOrCreate(
            ['title' => 'Kiến Trúc ERP Tương Lai'],
            [
                'link' => '/lien-he',
                'type' => $typeVal,
                'status' => 1,
            ]
        );
        $slide1->is_home = 1;
        $slide1->save();

        $slide2 = Slide::firstOrCreate(
            ['title' => 'Tin Cậy. Bảo Mật. Uy Tín.'],
            [
                'link' => '/lien-he',
                'type' => $typeVal,
                'status' => 1,
            ]
        );
        $slide2->is_home = 1;
        $slide2->save();
        
        // 6. FIELDS (Lĩnh vực ứng dụng)
        $fields = ['Bán lẻ & Phân phối', 'Sản xuất công nghiệp', 'F&B - Quản lý chuỗi', 'Khách sạn - Resort', 'Y tế - Bệnh viện', 'Tài chính - Kế toán', 'Giáo dục - Đào tạo', 'Logistics - Vận tải'];
        foreach($fields as $k => $f) {
            \App\Models\FieldCategory::firstOrCreate(
                ['name' => $f],
                [
                    'status' => 1,
                    'parent_id' => null,
                ]
            );
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
