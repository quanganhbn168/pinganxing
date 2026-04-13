<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Settings\GeneralSettings;
use Awcodes\Curator\Models\Media;

class GlobalSettingsComposer
{
    protected ?GeneralSettings $setting = null;

    public function __construct()
    {
        try {
            $this->setting = app(GeneralSettings::class);
        } catch (\Exception $e) {
            // Do nothing if settings table isn't migrated
        }
    }

    public function compose(View $view): void
    {
        if (!$this->setting) {
            $view->with('globalFaviconUrl', asset('favicon.ico'));
            $view->with('globalMetaImageUrl', '');
            $view->with('globalLogoUrl', '');
            $view->with('globalFooterBackgroundUrl', '');
            return;
        }

        $resolveMedia = function ($settingValue) {
            if (empty($settingValue)) return null;
            
            // Nếu Spatie lưu mảng dưới dạng JSON string (ví dụ: '["45"]')
            if (is_string($settingValue) && str_starts_with($settingValue, '[') && str_ends_with($settingValue, ']')) {
                $decoded = json_decode($settingValue, true);
                if (is_array($decoded)) {
                    $settingValue = $decoded;
                }
            }

            // Handle if Curator saved it as an array (e.g. ['45'] or [45])
            $id = is_array($settingValue) ? ($settingValue[0] ?? null) : $settingValue;
            
            return (is_numeric($id)) ? Media::find((int) $id) : null;
        };

        $favMedia = $resolveMedia($this->setting->favicon);
        $metaMedia = $resolveMedia($this->setting->meta_image);
        $logoMedia = $resolveMedia($this->setting->logo);
        $footerBgMedia = $resolveMedia($this->setting->footer_background);

        $urls = [
            'globalFaviconUrl' => $favMedia ? url($favMedia->url) : asset('favicon.ico'),
            'globalMetaImageUrl' => $metaMedia ? url($metaMedia->url) : '',
            'globalLogoUrl' => $logoMedia ? url($logoMedia->url) : '',
            'globalFooterBackgroundUrl' => $footerBgMedia ? url($footerBgMedia->url) : ''
        ];

        foreach ($urls as $key => $url) {
            $view->with($key, $url);
        }
    }
}
