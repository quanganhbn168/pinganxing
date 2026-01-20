<?php

namespace App\Services;

use App\Contracts\MediaServiceContract;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProjectService
{
    /** Ảnh đại diện */
    private const MAIN_IMAGE_CONFIG = [
        'main'     => ['width' => 1024, 'height' => 768, 'fit' => true],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
    ];

    /** Ảnh banner */
    private const BANNER_IMAGE_CONFIG = [
        'main'     => ['width' => 1920, 'height' => 700, 'fit' => true],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
    ];

    /** Gallery */
    private const GALLERY_IMAGE_CONFIG = [
        'main'     => ['width' => 1024],
        'variants' => [
            'thumbnail' => ['width' => 150, 'height' => 150, 'fit' => true],
        ],
        'quality'  => 85,
    ];

    public function __construct(
        protected MediaServiceContract $mediaService
    ) {}

    /**
     * Danh sách dự án + filter cơ bản cho trang index.
     * Trả về [$projects, ['categories' => [id => name]]]
     */
    public function list(Request $request): array
    {
        $perPage = (int) $request->integer('per_page', 20);

        $projects = Project::with('category')
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $kw = trim($request->get('keyword'));
                $q->where(function ($qq) use ($kw) {
                    $qq->where('name', 'LIKE', "%{$kw}%")
                       ->orWhere('slug', 'LIKE', "%{$kw}%");
                });
            })
            ->when($request->filled('project_category_id'), fn ($q) =>
                $q->where('project_category_id', $request->integer('project_category_id')))
            ->when($request->filled('status') || $request->get('status') === '0', fn ($q) =>
                $q->where('status', (int) $request->get('status')))
            ->when($request->filled('is_home') || $request->get('is_home') === '0', fn ($q) =>
                $q->where('is_home', (int) $request->get('is_home')))
            ->latest('id')
            ->paginate($perPage);

        $filterCategories = $this->getFilterCategories();

        return [$projects, $filterCategories];
    }

    /** Dùng cho select filter phẳng (id => name) */
    public function getFilterCategories(): array
    {
        return ProjectCategory::orderBy('name')->pluck('name', 'id')->toArray();
    }

    /** Dùng cho <x-form.category-tree> (cây danh mục) */
    public function getCategoryTree()
    {
        return ProjectCategory::with('childrenRecursive')
            ->where('parent_id', 0)
            ->get();
    }

    /**
     * Tạo mới Project + media (image/banner/gallery).
     * Nhận các field media:
     * - image_original_path
     * - banner_original_path
     * - gallery_original_paths (JSON array)
     */
    public function create(array $data): Project
    {
        // Map input paths to model columns
        $data['image'] = $data['image_original_path'] ?? null;
        $data['banner'] = $data['banner_original_path'] ?? null;

        $projectData = Arr::except($data, [
            'image_original_path',
            'banner_original_path',
            'gallery_original_paths',
        ]);

        // Tự tạo slug nếu để trống
        if (empty($projectData['slug']) && !empty($projectData['name'])) {
            $projectData['slug'] = Str::slug($projectData['name']);
        }

        $project = Project::create($projectData);

        // Gallery (vẫn dùng bảng images)
        $this->updateGallery($project, $data['gallery_original_paths'] ?? null);

        return $project;
    }

    /**
     * Cập nhật Project + media
     */
    public function update(Project $project, array $data): Project
    {
        // Map input paths to model columns
        if (array_key_exists('image_original_path', $data)) {
            $data['image'] = $data['image_original_path'];
        }
        if (array_key_exists('banner_original_path', $data)) {
            $data['banner'] = $data['banner_original_path'];
        }

        $projectData = Arr::except($data, [
            'image_original_path',
            'banner_original_path',
            'gallery_original_paths',
        ]);

        // Nếu slug trống nhưng có name → tự tạo
        if (empty($projectData['slug']) && !empty($projectData['name'])) {
            $projectData['slug'] = Str::slug($projectData['name']);
        }

        $project->update($projectData);

        // Gallery (nếu truyền JSON mới thì sync thay thế)
        $this->updateGallery($project, $data['gallery_original_paths'] ?? null);

        return $project;
    }

    /**
 * Đồng bộ gallery theo danh sách path (array hoặc JSON string).
 * - Nếu null/empty: giữ nguyên gallery hiện có.
 * - Nếu là JSON string: parse -> array.
 * - Nếu là Collection: toArray().
 * - Chỉ lấy các phần tử kiểu string, bỏ trống/invalid.
 */
private function updateGallery(Project $project, $pathsInput): void
{
    // Không truyền gì -> bỏ qua
    if ($pathsInput === null || $pathsInput === '') {
        return;
    }

    // Chuẩn hoá thành mảng $paths
    if (is_string($pathsInput)) {
        // Có thể là chuỗi JSON hoặc một path đơn lẻ
        $decoded = json_decode($pathsInput, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $paths = $decoded;
        } else {
            // Không phải JSON -> coi như 1 path
            $paths = [$pathsInput];
        }
    } elseif ($pathsInput instanceof \Illuminate\Support\Collection) {
        $paths = $pathsInput->toArray();
    } elseif (is_array($pathsInput)) {
        $paths = $pathsInput;
    } else {
        // Kiểu lạ -> bỏ qua
        return;
    }

    // Làm phẳng mảng & chỉ giữ string (phòng khi là mảng {original_path: ...})
    $flat = [];
    foreach ($paths as $item) {
        if (is_string($item) && $item !== '') {
            $flat[] = $item;
        } elseif (is_array($item)) {
            // thử các khoá hay dùng
            foreach (['original_path','path','url','main_path'] as $k) {
                if (!empty($item[$k]) && is_string($item[$k])) {
                    $flat[] = $item[$k];
                    break;
                }
            }
        }
    }

    // Nếu sau khi lọc không có gì -> xoá sạch gallery (tuỳ yêu cầu)
    // Ở đây: nếu danh sách trống => xoá toàn bộ cũ để "sync" đúng ý đồ
    // Nếu muốn giữ nguyên khi trống, hãy return thay vì xoá.
    foreach ($project->gallery as $img) {
        $this->mediaService->deleteProcessedImages($img);
        $img->delete();
    }

    foreach (array_values($flat) as $index => $path) {
        $imageData = $this->mediaService->processAndPrepareData(
            $path,
            'projects/gallery',
            self::GALLERY_IMAGE_CONFIG
        );

        if ($imageData) {
            $project->addGalleryImage($imageData, $index);
        }
    }
}


    /**
     * Xoá Project + toàn bộ media liên quan
     */
    public function delete(Project $project): void
    {
        foreach ($project->images as $img) {
            $this->mediaService->deleteProcessedImages($img);
        }

        $project->images()->delete();
        $project->delete();
    }
}
