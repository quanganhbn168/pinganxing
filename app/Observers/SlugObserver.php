<?php

namespace App\Observers;

use App\Support\SlugGenerator;
use Illuminate\Database\Eloquent\Model;

class SlugObserver
{
    public function saved(Model $model): void
    {
        $sourceString = null;

        if (! empty($model->custom_slug)) {
            $sourceString = $model->custom_slug;
        } elseif ($model->wasRecentlyCreated || $model->wasChanged(['name', 'title'])) {
            $sourceString = $model->name ?? $model->title;
        }

        if (! $sourceString || ! method_exists($model, 'slugData')) {
            return;
        }

        app(SlugGenerator::class)->syncModel($model, $sourceString, optional($model->slugData)->id);
    }

    public function deleting(Model $model): void
    {
        if (method_exists($model, 'slugData') && $model->slugData()->exists()) {
            $model->slugData()->delete();
        }
    }
}
