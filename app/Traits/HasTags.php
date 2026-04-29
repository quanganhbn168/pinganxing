<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTags
{
    /**
     * Get all of the tags for the model.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->orderBy('sort_order')
            ->orderBy('name');
    }
}
