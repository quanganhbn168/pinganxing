<?php



namespace App\Services;



use Illuminate\Support\Str;

use App\Models\Slug;



class SlugService

{

    public function upsert($model, string $base): string

    {

        $raw = Str::slug($base);

        $slug = $raw;

        $i = 2;



        while (

            Slug::where('slug', $slug)

                ->where('sluggable_type', get_class($model))

                ->where('sluggable_id', '!=', $model->id)

                ->exists()

        ) {

            $slug = $raw . '-' . $i++;

        }



        // morphOne

        $model->slug()->updateOrCreate([], [

            'slug' => $slug,

        ]);

        if ($model->isFillable('slug')) {

            $model->forceFill(['slug' => $slug])->saveQuietly();

        }

        return $slug;

    }

}

