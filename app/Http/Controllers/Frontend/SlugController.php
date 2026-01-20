<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slug;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Models\Intro;
use App\Models\Career;
use Illuminate\Http\Request;

class SlugController extends Controller
{
    /**
     * Xử lý route động cho các slug đa hình (Polymorphic Slugs).
     */
    public function handle(Request $request, $slug)
    {
        $slugData = Slug::where('slug', $slug)->first();

        if (!$slugData || !$slugData->sluggable) {
            abort(404);
        }

        $model = $slugData->sluggable;

        // Sử dụng match cho code ngắn gọn và dễ quản lý hơn
        return match (true) {
            $model instanceof Post         => app(PostController::class)->detail($model),
            $model instanceof PostCategory => app(PostController::class)->postByCate($model),
            $model instanceof Product      => app(ProductController::class)->show($model),
            $model instanceof Category     => app(ProductController::class)->byCategory($model, $request),
            $model instanceof Project      => app(ProjectController::class)->detail($model),
            $model instanceof Service      => app(ServiceController::class)->detail($model),
            $model instanceof ServiceCategory => app(ServiceController::class)->byCategory($model),
            $model instanceof Field        => app(FieldController::class)->detail($model),
            $model instanceof FieldCategory => app(FieldController::class)->byCategory($model),
            $model instanceof Intro        => app(IntroController::class)->getBySlug($model),
            $model instanceof Career       => app(CareerController::class)->show($model),
            default                        => abort(404),
        };
    }
}
