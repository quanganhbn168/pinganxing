<?php

namespace App\Traits;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

trait HasSeo
{
    /**
     * Accessor: $model->seo_image_url
     * Trả về URL ảnh SEO (curation 1200x630) hoặc fallback về ảnh gốc.
     */
    public function getSeoImageUrlAttribute(): ?string
    {
        if (! $this->meta_image_id) {
            return null;
        }

        $media = Media::find($this->meta_image_id);

        if (! $media) {
            return null;
        }

        if ($media->curations) {
            $curation = collect($media->curations)->first(function ($item) {
                return str_contains($item['curation']['key'] ?? ($item['key'] ?? ''), 'seo');
            });

            if ($curation) {
                return $curation['url'] ?? $media->url;
            }
        }

        return $media->url;
    }

    /**
     * Schema: Section SEO dùng chung cho mọi form Filament.
     *
     * Dùng: ...HasSeo::seoSection(),
     */
    public static function seoSection(): Section
    {
        return Section::make('SEO')
            ->schema([
                TextInput::make('meta_title')
                    ->label('Meta Title')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('meta_description')
                    ->label('Meta Description')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('meta_keywords')
                    ->label('Meta Keywords')
                    ->rows(2)
                    ->columnSpanFull(),
                CuratorPicker::make('meta_image_id')
                    ->label('Meta Image')
                    ->helperText('Ảnh chuẩn để chia sẻ Facebook/Zalo (1200x630 px).'),
            ]);
    }
}
