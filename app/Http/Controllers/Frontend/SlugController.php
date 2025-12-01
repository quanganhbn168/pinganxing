<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Slug;
use App\Models\Category;
use Illuminate\Http\Request;
class SlugController extends Controller
{
    /**
     * Xử lý một slug đến từ URL.
     *
     * @param string $slug
     * @param Request $request 
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function handle($slug, Request $request)
    {
        $slugInstance = Slug::where('slug', $slug)->firstOrFail();
        $model = $slugInstance->sluggable;
        if (!$model) {
            abort(404);
        }
        $controllerAction = match (get_class($model)) {
            \App\Models\Product::class => [\App\Http\Controllers\Frontend\ProductController::class, 'show'],
            \App\Models\FieldCategory::class => [\App\Http\Controllers\Frontend\FieldController::class, 'byCategory'],
            \App\Models\Field::class => [\App\Http\Controllers\Frontend\FieldController::class, 'detail'],
            \App\Models\ServiceCategory::class => [\App\Http\Controllers\Frontend\ServiceController::class, 'byCategory'],
            \App\Models\Service::class => [\App\Http\Controllers\Frontend\ServiceController::class, 'detail'],
            \App\Models\Project::class => [\App\Http\Controllers\Frontend\ProjectController::class, 'detail'],
            \App\Models\Category::class => [\App\Http\Controllers\Frontend\ProductController::class, 'byCategory'],
            \App\Models\Intro::class => [\App\Http\Controllers\Frontend\IntroController::class, 'getBySlug'], 
            \App\Models\PostCategory::class => [\App\Http\Controllers\Frontend\PostController::class, 'postByCate'],   
            \App\Models\Post::class => [\App\Http\Controllers\Frontend\PostController::class, 'detail'],   
            default => null,
        };
        if ($controllerAction) {
            $controllerInstance = app($controllerAction[0]);
            $method = $controllerAction[1];
            if ($model instanceof Category) {
                return $controllerInstance->{$method}($model, $request);
            }
            return $controllerInstance->{$method}($model);
        }
        abort(500, 'Controller action not defined for this model type.');
    }
}