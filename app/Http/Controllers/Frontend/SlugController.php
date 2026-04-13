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
        $slugData = Slug::where('slug', $slug)->first();

        if (!$slugData || !$slugData->sluggable) {
            abort(404);
        }

        $model = $slugData->sluggable;
        $modelClass = get_class($model);

        // Lấy prefix từ map tập trung trong HasSlug trait
        $prefixMap = HasSlug::slugPrefixMap();
        $prefix = $prefixMap[$modelClass] ?? null;

        if ($prefix) {
            return redirect("/{$prefix}/{$slug}", 301);
        }

        // Fallback: nếu model không có trong map
        abort(404);
    }
}

