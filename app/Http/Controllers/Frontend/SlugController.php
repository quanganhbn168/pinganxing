<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Slug;
use App\Traits\HasSlug;
use Illuminate\Http\Request;

class SlugController extends Controller
{
    /**
     * Catch-all route: 301 redirect URL cũ (/{slug}) sang URL mới có prefix.
     * VD: /may-bom-abc → 301 → /san-pham/may-bom-abc
     * Giữ backward compatibility và bảo toàn SEO juice cho URL đã được Google index.
     */
    public function handle(Request $request, $slug)
    {
        $matches = Slug::query()
            ->where('slug', $slug)
            ->with('sluggable')
            ->get()
            ->filter(fn (Slug $slugData) => $slugData->sluggable);

        if ($matches->isEmpty()) {
            abort(404);
        }

        $pageSlug = $matches->first(fn (Slug $slugData) => $slugData->sluggable instanceof \App\Models\Page);

        if (! $pageSlug && $matches->count() > 1) {
            abort(404);
        }

        $slugData = $pageSlug ?? $matches->first();
        $model = $slugData->sluggable;
        $modelClass = get_class($model);

        // Nếu là Page (Trang đơn, có URL root-level), render trực tiếp giao diện thay vì redirect
        if ($model instanceof \App\Models\Page) {
            $page = $model;
            // Get generic setting for Page (if needed) or pass the model directly
            $setting = app(\App\Settings\GeneralSettings::class);
            $pageTitle = $page->title;
            // Optionally, page can have its own banner, wait Page currently doesn't have banner but meta_image
            $bannerUrl = $page->meta_image_id ? optional($page->metaImage)->url : ($setting->banner ?? asset('images/setting/no-banner.png'));
            
            $breadcrumbs = [
                ['label' => $pageTitle, 'url' => '']
            ];

            return view('frontend.pages.detail', compact('page', 'setting', 'pageTitle', 'bannerUrl', 'breadcrumbs'));
        }

        // Tự động tìm prefix
        $prefixMap = HasSlug::slugPrefixMap();
        $prefix = $prefixMap[$modelClass] ?? null;

        if ($prefix) {
            return redirect("/{$prefix}/{$slug}", 301);
        }

        // Fallback: nếu model không có trong map
        abort(404);
    }
}

