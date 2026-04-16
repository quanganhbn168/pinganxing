<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Support\Facades\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class SlugInput extends TextInput
{
    protected const SLUG_LOCK_STATE_PATH = '__slug_locked';
    protected const SLUG_LAST_AUTO_STATE_PATH = '__slug_last_auto';

    /**
     * Helper: áp cấu hình auto slug cho field nguồn.
     *
     * Dùng trong form:
     *   SlugInput::sourceField(
     *       TextInput::make('name')->label('Tên danh mục')->required()
     *   )
     */
    public static function sourceField(TextInput $field, string $slugField = 'slug'): TextInput
    {
        return $field
            // Livewire v4.1+ requires `.live.blur` to trigger a server update on blur.
            ->stateBindingModifiers(['live', 'blur'])
            ->afterStateUpdated(static::autoSlug($slugField));
    }

    /**
     * Helper: trả về closure cho afterStateUpdated của source field.
     */
    public static function autoSlug(string $slugField = 'slug'): \Closure
    {
        return function (Get $get, Set $set, ?string $state, ?Model $record) use ($slugField) {
            if (! filled($state)) {
                $set($slugField, null);
                return;
            }

            $isLocked = (bool) ($get(static::SLUG_LOCK_STATE_PATH) ?? false);
            $currentSlug = $get($slugField);

            // Nếu user đã chỉnh slug thủ công, không overwrite nữa.
            if ($isLocked && filled($currentSlug)) {
                return;
            }

            $baseSlug = Str::slug($state);
            $slug     = $baseSlug;
            $counter  = 1;

            // Lấy ID slug hiện tại của record (khi Edit) để loại trừ khỏi check
            $excludeId = $record && method_exists($record, 'slugData')
                ? optional($record->slugData)->id
                : null;

            while (true) {
                $exists = DB::table('slugs')
                    ->where('slug', $slug)
                    ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                    ->exists();

                if (! $exists) break;

                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $set($slugField, $slug);
            $set(static::SLUG_LAST_AUTO_STATE_PATH, $slug);
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
            // Chỉ khóa auto-slug khi user thực sự đổi khác slug auto.
            // Nếu user để nguyên (= slug auto) hoặc xóa rỗng thì không khóa.
            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                if (! filled($state)) {
                    $set(static::SLUG_LOCK_STATE_PATH, false);
                    return;
                }

                $lastAuto = $get(static::SLUG_LAST_AUTO_STATE_PATH);

                $set(static::SLUG_LOCK_STATE_PATH, filled($lastAuto) && ((string) $state !== (string) $lastAuto));
            })
            // Tự load giá trị khi Edit từ relation hoặc cột local
            ->afterStateHydrated(function (Set $set, SlugInput $component, $state, ?Model $record) {
                $resolved = null;

                if (filled($state)) {
                    $resolved = $state;
                } elseif ($record && method_exists($record, 'getSlugValueAttribute') && $record->slug_value) {
                    $resolved = $record->slug_value;
                    $component->state($resolved);
                } elseif ($record && $record->slug) {
                    $resolved = $record->slug;
                    $component->state($resolved);
                }

                // Nếu đang edit và đã có slug, mặc định khóa để không bị auto overwrite.
                if (filled($resolved)) {
                    $set(static::SLUG_LAST_AUTO_STATE_PATH, $resolved);
                    $set(static::SLUG_LOCK_STATE_PATH, true);
                }
            })
            // Check trùng theo bảng slugs (loại trừ record hiện tại)
            ->unique(
                table: 'slugs',
                column: 'slug',
                modifyRuleUsing: function (Unique $rule, ?Model $record) {
                    if ($record && method_exists($record, 'slugData') && $record->slugData) {
                        return $rule->ignore($record->slugData->id);
                    }
                    return $rule;
                }
            )
            // Filament cần dehydrate state để $set() hoạt động reactive trên UI
            // saveRelationshipsUsing xử lý việc lưu xuống DB, Model::$fillable không có 'slug' nên sẽ không bị mass-assign
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
