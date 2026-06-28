<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Partner;
use App\Settings\IntroSettings;
use App\Settings\GeneralSettings;
use App\Traits\HasVideoEmbed;
use Awcodes\Curator\Models\Media;

class IntroController extends Controller
{
    use HasVideoEmbed;

    public function index()
    {
        $intro   = app(IntroSettings::class);
        $setting = app(GeneralSettings::class);

        // Ảnh banner
        $bannerMedia = $intro->page_banner_id
            ? Media::find($intro->page_banner_id)
            : null;

        // Ảnh câu chuyện
        $storyMedia = $intro->story_image_id
            ? Media::find($intro->story_image_id)
            : null;

        // Thumbnail video
        $videoThumbnail = $intro->video_thumbnail_id
            ? Media::find($intro->video_thumbnail_id)
            : $storyMedia;

        // Embed URL từ YouTube / Vimeo
        $videoEmbed = $this->toEmbedUrl($intro->video_url);

        // Đội ngũ — lấy tất cả active
        $teams = Team::with('image')->get();

        // Đối tác — lấy active, sắp xếp theo sort_order
        $partners = Partner::where('status', 1)
            ->with('image')
            ->whereHas('image', function ($query) {
                $query->where('path', 'not like', '%placehold.co%')
                    ->where('path', 'not like', '%picsum.photos%')
                    ->where('path', 'not like', '%images.unsplash.com%');
            })
            ->orderBy('sort_order')
            ->get();

        return view('frontend.intro', compact(
            'intro', 'setting',
            'bannerMedia', 'storyMedia',
            'videoEmbed', 'videoThumbnail',
            'teams', 'partners'
        ));
    }
}
