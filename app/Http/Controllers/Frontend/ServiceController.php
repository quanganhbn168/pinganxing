<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;
use App\Settings\GeneralSettings;
use App\Settings\PageSettings;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Resolve slug cho prefix /dich-vu/{slug}.
     */
    public function resolveBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->whereIn('sluggable_type', [ServiceCategory::class, Service::class])
            ->firstOrFail();
        $model = $slugData->sluggable;

        return match (true) {
            $model instanceof ServiceCategory => $this->byCategory($model),
            $model instanceof Service         => $this->detail($model),
            default => abort(404),
        };
    }

    public function serviceBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', Service::class)
            ->firstOrFail();

        return $this->detail($slugData->sluggable);
    }

    public function categoryBySlug(string $slug)
    {
        $slugData = Slug::query()
            ->where('slug', $slug)
            ->where('sluggable_type', ServiceCategory::class)
            ->firstOrFail();

        return $this->byCategory($slugData->sluggable);
    }

    /**
     * Nhiệm vụ 1: Hiển thị trang DỊCH VỤ TỔNG.
     * Lấy tất cả danh mục và các dịch vụ con tương ứng.
     */
    public function index()
    {
        $setting      = app(GeneralSettings::class);
        $pageSettings = app(PageSettings::class);

        $pageTitle    = $pageSettings->services_title    ?: 'Dịch vụ';
        $pageSubtitle = $pageSettings->services_headline ?: null;
        $bannerUrl    = $setting->banner ?? asset('images/setting/no-banner.png');
        $breadcrumbs  = [['label' => $pageTitle]];

        $serviceCategories = ServiceCategory::where('status', 1)
            ->where(function($query) {
                $query->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->with([
                'services' => fn ($query) => $query
                    ->where('status', 1)
                    ->with('image')
                    ->latest(),
            ])
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('frontend.services.index', compact(
            'serviceCategories', 'setting',
            'pageTitle', 'pageSubtitle', 'bannerUrl', 'breadcrumbs'
        ));
    }

    /**
     * Nhiệm vụ 2: Hiển thị trang DANH MỤC DỊCH VỤ CỤ THỂ.
     * Chỉ lấy các dịch vụ thuộc về danh mục này.
     *
     * @param ServiceCategory $category
     * @return \Illuminate\View\View
     */
    public function byCategory(ServiceCategory $category)
    {
        // Lấy các dịch vụ chỉ thuộc về danh mục đã cho
        $services = $category->services()
            ->where('status', 1)
            ->with('image')
            ->latest()
            ->paginate(10);
        
        $pageTitle = $category->name;
        $breadcrumbItems = [
            ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')],
            ['label' => $category->name],
        ];
        
        // Truyền ra view cả danh mục hiện tại và danh sách dịch vụ của nó
        return view('frontend.services.index', compact(
            'category', 
            'services',
            'pageTitle',
            'breadcrumbItems'
        ));
    }


    public function detail(Service $service)
    {
        $service->loadMissing(['category', 'image', 'banner']);

        // Danh sách dịch vụ cùng cấp
        $relatedServices = Service::where("status", 1)
        ->where("id", '!=', $service->id)
        ->where('service_category_id', $service->service_category_id)
        ->with('image')
        ->orderBy("updated_at", "DESC")
        ->limit(3)
        ->get();

        $serviceCoverImage = $this->resolveMediaUrl($service->image);
        $serviceImages = $this->serviceGalleryImages($service);
        $relatedServiceImageUrls = $relatedServices->mapWithKeys(fn (Service $relatedService) => [
            $relatedService->id => $this->resolveMediaUrl($relatedService->image),
        ]);

        // Removed data Landing Page load since the modules (projects, posts, products) pivot tables do not exist

        // Khởi tạo Breadcrumb
        $breadcrumbItems = [
            ['label' => 'Dịch vụ', 'url' => route('frontend.services.index')]
        ];
        if ($service->category) {
            $breadcrumbItems[] = ['label' => $service->category->name, 'url' => $service->category->slug_url];
        }
        $breadcrumbItems[] = ['label' => $service->name, 'url' => ''];

        // Banner Image
        $setting = app(\App\Settings\GeneralSettings::class);
        $bannerUrl = !empty($service->banner) ? $service->banner : (!empty($setting->banner) ? $setting->banner : asset('images/setting/no-banner.png'));

        return view("frontend.services.detail", compact(
            "service",
            "serviceCoverImage",
            "serviceImages",
            "relatedServices",
            "relatedServiceImageUrls",
            "breadcrumbItems",
            "bannerUrl",
            "setting"
        ));
    }

    /**
     * Chỉ hiển thị gallery khi quản trị viên đã chọn ảnh trong trường thư viện.
     */
    private function serviceGalleryImages(Service $service): Collection
    {
        $images = collect();

        $push = function (?string $url) use ($images): void {
            if (blank($url)) {
                return;
            }

            $normalizedUrl = $this->normalizeImageUrl($url);

            if ($normalizedUrl && ! $images->contains($normalizedUrl)) {
                $images->push($normalizedUrl);
            }
        };

        foreach (collect($service->gallery)->filter() as $item) {
            $push($this->resolveGalleryItemUrl($item));
        }

        return $images->values();
    }

    private function resolveGalleryItemUrl(mixed $item): ?string
    {
        if ($item instanceof Media) {
            return $this->resolveMediaUrl($item);
        }

        if (is_numeric($item)) {
            return $this->resolveMediaUrl(Media::find((int) $item));
        }

        if (is_string($item)) {
            return $item;
        }

        if (is_array($item)) {
            $id = $item['id'] ?? $item['media_id'] ?? null;

            if (is_numeric($id)) {
                $mediaUrl = $this->resolveMediaUrl(Media::find((int) $id));

                if ($mediaUrl) {
                    return $mediaUrl;
                }
            }

            return $item['url'] ?? $item['path'] ?? null;
        }

        return null;
    }

    private function resolveMediaUrl(?Media $media): ?string
    {
        if (! $media) {
            return null;
        }

        if (preg_match('~(?:picsum\.photos|placehold\.co|images\.unsplash\.com)~i', (string) $media->path)) {
            return null;
        }

        // Một số dữ liệu cũ lưu URL tuyệt đối ngay trong cột path.
        if (Str::startsWith($media->path, ['http://', 'https://', '//'])) {
            return $media->path;
        }

        return $media->url;
    }

    private function normalizeImageUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://', '//'])) {
            return $url;
        }

        return asset(ltrim($url, '/'));
    }
}
