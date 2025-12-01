<?php
namespace App\Services;
use App\Models\Post;
use App\Models\PostCategory;
use App\Contracts\MediaServiceContract;
use Illuminate\Support\Arr; 
use Illuminate\Http\Request;

/**
 * Quản lý logic nghiệp vụ cho Posts.
 * Service này không phụ thuộc vào HTTP Request.
 */
class PostService
{
    protected MediaServiceContract $mediaService;
    /**
     * Cấu hình cho ảnh đại diện (main image).
     */
    private const MAIN_IMAGE_CONFIG = [
        'main' => ['width' => 1024],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];
    /**
     * Cấu hình cho ảnh banner (1920x700).
     */
    private const BANNER_IMAGE_CONFIG = [
        'main' => ['width' => 1920, 'height' => 700, 'fit' => true],
        'variants' => ['thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true]],
        'quality' => 85,
        'format' => 'webp'
    ];
    public function __construct(MediaServiceContract $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * Danh sách bài viết có lọc + phân trang.
     * Trả về [LengthAwarePaginator $posts, array $filterCategories]
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $posts = Post::with('category')
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim((string) $request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('title', 'LIKE', "%{$kw}%")
                       ->orWhere('slug', 'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('category_id'), fn($q) =>
                $q->where('post_category_id', (int) $request->get('category_id')))
            ->when($request->filled('status') || $request->get('status') === '0', fn($q) =>
                $q->where('status', (int) $request->get('status')))
            ->when($request->filled('is_home') || $request->get('is_home') === '0', fn($q) =>
                $q->where('is_home', (int) $request->get('is_home')))
            ->latest('id')
            ->paginate($perPage);

        $filterCategories = $this->getFilterCategories();

        return [$posts, $filterCategories];
    }

    /** Dùng cho filter dropdown ở index */
    public function getFilterCategories(): array
    {
        return PostCategory::orderBy('name')->pluck('name', 'id')->toArray();
    }

    /** Giữ lại nếu nơi khác còn dùng */
    public function getAll(): LengthAwarePaginator
    {
        return Post::with('category')->latest()->paginate(20);
    }
    /**
     * Lấy cây danh mục (dùng cho menu hoặc select box).
     */
    public function getCategoryTree()
    {
        return PostCategory::with('childrenRecursive')
            ->where('parent_id', 0)
            ->get();
    }
    /**
     * Tạo Post mới từ mảng dữ liệu.
     */
    public function create(array $data): Post
    {
        $postData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $post = Post::create($postData);
        $this->mediaService->updateMedia(
            $post,
            $data['image_original_path'] ?? null,
            'posts', 
            self::MAIN_IMAGE_CONFIG,
            fn($imgData) => $post->setMainImage($imgData), 
            null, 
            'ảnh đại diện' 
        );
        $this->mediaService->updateMedia(
            $post,
            $data['banner_original_path'] ?? null,
            'posts/banner', 
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $post->setBannerImage($imgData), 
            null, 
            'ảnh banner' 
        );
        return $post;
    }
    /**
     * Cập nhật Post từ mảng dữ liệu.
     */
    public function update(Post $post, array $data): Post
    {
        $postData = Arr::except($data, ['image_original_path', 'banner_original_path']);
        $post->update($postData);
        $this->mediaService->updateMedia(
            $post,
            $data['image_original_path'] ?? null,
            'posts',
            self::MAIN_IMAGE_CONFIG,
            fn($imgData) => $post->setMainImage($imgData),
            fn() => $post->mainImage(), 
            'ảnh đại diện'
        );
        $this->mediaService->updateMedia(
            $post,
            $data['banner_original_path'] ?? null,
            'posts/banner',
            self::BANNER_IMAGE_CONFIG,
            fn($imgData) => $post->setBannerImage($imgData),
            fn() => $post->bannerImage(), 
            'ảnh banner'
        );
        return $post;
    }
    /**
     * Xóa Post và các ảnh liên quan.
     */
    public function delete(Post $post): void
    {
        $images = $post->images()->get(); 
        foreach ($images as $image) {
            $this->mediaService->deleteProcessedImages($image);
            $image->delete(); 
        }
        $post->delete();
    }
}