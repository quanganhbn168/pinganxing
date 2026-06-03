<?php

namespace App\Filament\Forms\Components;

use App\Support\SlugGenerator;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

class SlugInput extends TextInput
{
    public static function sourceField(TextInput $field, string $slugField = 'slug'): TextInput
    {
        return $field
            ->live(onBlur: true)
            ->afterStateUpdated(static::autoSlug($slugField));
    }

    public static function autoSlug(string $slugField = 'slug'): \Closure
    {
        return function (Set $set, ?string $state, ?Model $record) use ($slugField) {
            if (! filled($state)) {
                $set($slugField, null);
                return;
            }

            $ignoreSlugId = $record && method_exists($record, 'slugData')
                ? $record->slugData()->value('id')
                : null;

            $set($slugField, app(SlugGenerator::class)->generate($state, $record, $ignoreSlugId));
        };
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Đường dẫn')
            ->required()
            ->maxLength(255)
            ->prefix('/')
            ->helperText('Tự động tạo từ tên. Có thể chỉnh sửa thủ công.')
            ->afterStateHydrated(function (SlugInput $component, ?Model $record): void {
                if (! $record || ! method_exists($record, 'slugData')) {
                    return;
                }

                $slug = $record->slugData?->slug;

                if (! filled($slug)) {
                    $source = $record->name ?? $record->title ?? null;

                    if (filled($source)) {
                        $slug = app(SlugGenerator::class)->generate($source, $record);
                    }
                }

                $component->state($slug);
            })
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (SlugInput $component, Model $record, ?string $state): void {
                if (! method_exists($record, 'slugData') || ! filled($state)) {
                    return;
                }

                $slug = app(SlugGenerator::class)->syncModel($record, $state);
                $component->state($slug);
            });
    }
}
