<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Schema;

class SlugInput extends TextInput
{
    /**
     * Helper: trả về closure cho afterStateUpdated của source field.
     * 
     * Dùng trong form:
     *   TextInput::make('name')        // hoặc 'title', 'label', bất kỳ field nào
     *       ->live(onBlur: true)
     *       ->afterStateUpdated(SlugInput::autoSlug())
     */
    public static function autoSlug(string $slugField = 'slug'): \Closure
    {
        return function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) use ($slugField) {
            if ($state) {
                $set($slugField, Str::slug($state));
            }
        };
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Đường dẫn SEO (Slug)')
            ->required()
            ->maxLength(255)
            ->prefix('/')
            ->helperText('Tự động tạo từ tên. Có thể chỉnh sửa thủ công.')
            // Tự load giá trị khi Edit từ relation hoặc cột local
            ->afterStateHydrated(function (SlugInput $component, $state, ?Model $record) {
                if ($record && method_exists($record, 'getSlugValueAttribute') && $record->slug_value) {
                    $component->state($record->slug_value);
                } elseif ($record && $record->slug) {
                    $component->state($record->slug);
                }
            })
            // Check trùng theo bảng slugs (loại trừ record hiện tại)
            ->unique(
                table: 'slugs',
                column: 'slug',
                ignoreRecord: true,
                modifyRuleUsing: function (Unique $rule, ?Model $record) {
                    if ($record && method_exists($record, 'slugData') && $record->slugData) {
                        return $rule->ignore($record->slugData->id);
                    }
                    return $rule;
                }
            )
            // Ngăn chặn Filament insert tự động vào mảng Attributes
            ->dehydrated(false)
            // Can thiệp sau khi Model được Save
            ->saveRelationshipsUsing(function ($state, ?Model $record) {
                if ($record && method_exists($record, 'slugData')) {
                    if (empty($state)) {
                        $state = Str::slug($record->name ?? $record->title ?? 'item-' . $record->id);
                    }

                    $record->slugData()->updateOrCreate([], ['slug' => $state]);

                    if (Schema::hasColumn($record->getTable(), 'slug')) {
                        if ($record->slug !== $state) {
                            $record->slug = $state;
                            $record->saveQuietly();
                        }
                    }
                }
            });
    }
}
