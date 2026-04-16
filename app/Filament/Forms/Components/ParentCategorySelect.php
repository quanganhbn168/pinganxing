<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

/**
 * Reusable parent category/tree select for models using HasCategoryTree.
 *
 * Supports 2 common "root" styles:
 * - Root as NULL (nullable + placeholder)
 * - Root as 0    (includes 0 option + default(0) + int dehydration)
 */
class ParentCategorySelect extends Select
{
    protected ?string $treeModelClass = null;

    protected bool $rootIsNull = true;

    protected string $rootLabel = '-- Danh mục gốc --';

    public function treeModel(string $modelClass): static
    {
        $this->treeModelClass = $modelClass;

        // Options depend on the record (edit) to exclude itself + descendants.
        $this->options(function (?Model $record) use ($modelClass) {
            $options = $modelClass::getTreeOptions($record?->id);

            if ($this->rootIsNull) {
                return $options;
            }

            return [0 => $this->rootLabel] + $options;
        });

        return $this;
    }

    public function rootAsNull(string $placeholder = '-- Danh mục gốc --'): static
    {
        $this->rootIsNull = true;
        $this->placeholder($placeholder)->nullable();

        return $this;
    }

    public function rootAsZero(string $label = '-- Danh mục gốc --'): static
    {
        $this->rootIsNull = false;
        $this->rootLabel = $label;

        $this->default(0)
            ->required()
            ->dehydrateStateUsing(fn ($state) => (int) ($state ?? 0));

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Danh mục cha')
            ->searchable();
    }
}

