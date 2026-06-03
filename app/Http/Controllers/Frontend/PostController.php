<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Slug;
use App\Helpers\TocHelper;
use Illuminate\Http\Request;
use App\Models\PostCategory;
use App\Settings\PageSettings;
use Awcodes\Curator\Models\Media;

class PostController extends Controller
{
    /**
     * Resolve slug cho prefix /tin-tuc/{slug}.
     */
    public function resolveBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->whereIn('sluggable_type', [PostCategory::class, Post::class])
            ->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof PostCategory => $this->postByCate($model),
            $model instanceof Post         => $this->detail($model),
            default => abort(404),
        };
    }

    public function postBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', Post::class)
            ->firstOrFail();

        return $this->detail($slugData->sluggable);
    }

    public function categoryBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', PostCategory::class)
            ->firstOrFail();

        return $this->postByCate($slugData->sluggable);
    }

    public function index(Request $request)
    {
        $pageSettings = app(PageSettings::class);

        // Lấy title/subtitle/breadcrumb từ PageSettings
        $pageTitle    = $pageSettings->posts_title    ?: 'Tin tức';
        $pageSubtitle = $pageSettings->posts_headline  ?: 'Cập nhật thông tin mới nhất về công nghệ và doanh nghiệp';
        $breadcrumbs  = [['label' => $pageTitle]];
        $postsBannerUrl = $this->resolveMediaUrl($pageSettings->posts_banner);

        $keyword = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category');
        $sort = $request->input('sort', 'latest');

        // 1. Danh mục Root
        $postCategories = PostCategory::where(function ($query) {
                $query->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->where('status', 1)
            ->withCount(['posts' => fn ($query) => $query->where('status', 1)])
            ->get();

        // 2. Gán bài viết theo cây danh mục
        foreach ($postCategories as $category) {
            $childIds = PostCategory::getTreeIds($category->id);
            $catPosts = Post::whereIn('post_category_id', $childIds)
                ->where('status', 1)
                ->latest()
                ->take(9)
                ->get();
            $category->setRelation('posts', $catPosts);
        }

        // 3. Bài nổi bật
        $featuredPost = Post::where('status', 1)
            ->where('is_featured', 1)
            ->with(['image', 'category'])
            ->latest('updated_at')
            ->first();

        // 4. Danh sách bài (loại trừ bài nổi bật)
        $postsQuery = Post::where('status', 1)->with(['image', 'category']);
        if ($keyword !== '') {
            $postsQuery->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('content', 'LIKE', "%{$keyword}%");
            });
        }
        if ($categoryId && $categoryId !== 'all') {
            $categoryIds = PostCategory::getTreeIds((int) $categoryId);
            $postsQuery->whereIn('post_category_id', $categoryIds);
        }
        match ($sort) {
            'oldest' => $postsQuery->oldest(),
            'featured' => $postsQuery->orderByDesc('is_featured')->latest(),
            default => $postsQuery->latest(),
        };
        if ($featuredPost) {
            $postsQuery->where('id', '!=', $featuredPost->id);
        }
        $posts = $postsQuery->paginate(8)->withQueryString();

        $heroPosts = Post::where('status', 1)
            ->with(['image', 'category'])
            ->when($featuredPost, fn ($query) => $query->where('id', '!=', $featuredPost->id))
            ->latest()
            ->take(4)
            ->get();

        $popularPosts = Post::where('status', 1)
            ->with(['image', 'category'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('frontend.post.index', compact(
            'pageSettings', 'pageTitle', 'pageSubtitle', 'breadcrumbs',
            'postCategories', 'posts', 'featuredPost', 'heroPosts',
            'popularPosts', 'keyword', 'categoryId', 'sort', 'postsBannerUrl'
        ));
    }

    private function resolveMediaUrl(mixed $settingValue): ?string
    {
        if (empty($settingValue)) {
            return null;
        }

        if (is_string($settingValue) && str_starts_with($settingValue, '[') && str_ends_with($settingValue, ']')) {
            $decoded = json_decode($settingValue, true);
            if (is_array($decoded)) {
                $settingValue = $decoded;
            }
        }

        $id = is_array($settingValue) ? ($settingValue[0] ?? null) : $settingValue;
        $media = is_numeric($id) ? Media::find((int) $id) : null;

        return $media?->url ? url($media->url) : null;
    }

    public function detail(Post $post)
    {
        $post->load(['category', 'image', 'banner']);
        $pageSettings = app(PageSettings::class);
        
        // Lấy danh mục cha để làm menu (nếu cần)
        $allCategories = PostCategory::select("name","id")->where('parent_id', 0)
            ->orWhereNull('parent_id') // Handle cả trường hợp null cho chắc
            ->where('status', 1)
            ->get();

        // Lấy bài viết liên quan (cùng danh mục)
        $relatedPosts = Post::with(['image', 'banner', 'category'])
            ->where('status', 1)
            ->where('post_category_id', $post->post_category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(6)
            ->get();
        
        $processedData = TocHelper::process($post->content);
        $contentHtml = $processedData['html'];
        $tocList = $processedData['toc'];
        return view('frontend.post.detail', compact(
            "post",
            "allCategories",
            "relatedPosts",
            "contentHtml",
            "tocList",
            "pageSettings",
        ));
    }

    public function postByCate(PostCategory $postCategory)
    {
        $allCategories = PostCategory::where('parent_id', 0)
            ->orWhereNull('parent_id')
            ->where('status', 1)
            ->get();

        // [FIX]: Thay thế đoạn if/else cũ bằng hàm getTreeIds mạnh mẽ hơn
        // Hàm này lấy được cả cháu, chắt... chứ không chỉ mỗi con trực tiếp
        $categoryIds = PostCategory::getTreeIds($postCategory->id);

        // Query bài viết
        $posts = Post::whereIn('post_category_id', $categoryIds)
            ->where('status', 1)
            ->latest()
            ->paginate(10);

        $featuredPosts = Post::where('status', 1)
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('frontend.post.postByCate', [
            'category' => $postCategory,
            'posts' => $posts,
            'allCategories' => $allCategories,
            'featuredPosts' => $featuredPosts,
        ]);
    }
}
