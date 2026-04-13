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
            return;
        }

        $resolveMedia = function ($settingValue) {
            if (empty($settingValue)) return null;
            // Handle if Curator saved it as an array (e.g. ['45'])
            $id = is_array($settingValue) ? ($settingValue[0] ?? null) : $settingValue;
            return (is_numeric($id)) ? Media::find($id) : null;
        };

        $favMedia = $resolveMedia($this->setting->favicon);
        $metaMedia = $resolveMedia($this->setting->meta_image);
        $logoMedia = $resolveMedia($this->setting->logo);

        $urls = [
            'globalFaviconUrl' => $favMedia ? url($favMedia->url) : asset('favicon.ico'),
            'globalMetaImageUrl' => $metaMedia ? url($metaMedia->url) : '',
            'globalLogoUrl' => $logoMedia ? url($logoMedia->url) : ''
        ];

        foreach ($urls as $key => $url) {
            $view->with($key, $url);
        }
    }
}
