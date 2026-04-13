<?php

namespace App\Traits;

use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

trait HasSeo
{
    /**
     * Tự động điền dữ liệu SEO nếu người dùng bỏ trống
     */
    public static function bootHasSeo()
    {
        static::saving(function ($model) {
            // 1. Tự lấy tên hoặc tiêu đề
            if (empty($model->meta_title)) {
                $model->meta_title = $model->name ?? $model->title ?? null;
            }
            
            // 2. Tự trích xuất mô tả ngắn hoặc cắt 160 chữ từ nội dung
            if (empty($model->meta_description)) {
                $content = $model->description ?? $model->short_description ?? $model->excerpt ?? $model->content ?? null;
                if ($content) {
                    $cleanText = html_entity_decode(strip_tags((string) $content), ENT_QUOTES, 'UTF-8');
                    $model->meta_description = Str::limit(trim($cleanText), 160);
                }
            }

            // 3. Tự chép ID ảnh đại diện
            if (empty($model->meta_image_id)) {
                $model->meta_image_id = $model->image_id ?? $model->thumbnail_id ?? $model->banner_id ?? null;
            }
        });
    }
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
                    ->helperText('Ảnh chuẩn để chia sẻ Facebook/Zalo (1200x630 px).')
                    ->multiple(false),
            ]);
    }
}
