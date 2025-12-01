<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Slug;

class GenerateSlugs extends Command
{
    protected $signature = 'generate:slugs';
    protected $description = 'Generate missing slugs for existing models';

    protected $models = [
        \App\Models\Intro::class,
        \App\Models\Product::class,
        \App\Models\Service::class,
        \App\Models\Project::class,
        \App\Models\ProjectCategory::class,
        \App\Models\Post::class,
        \App\Models\Category::class,
        \App\Models\FieldCategory::class,
        \App\Models\Field::class,
        \App\Models\Category::class,
        \App\Models\ServiceCategory::class,
        \App\Models\PostCategory::class,
    ];

    public function handle()
    {
        foreach ($this->models as $modelClass) {
            $this->info("Processing {$modelClass}...");

            $items = $modelClass::all();

            foreach ($items as $item) {
                if ($item->slug()->exists()) continue;

                $base = Str::slug($item->name ?? $item->title ?? 'item-' . $item->id);
                $slug = $base;
                $i = 1;

                while (Slug::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $item->slugData()->create(['slug' => $slug]);
                $this->line("{$slug}");
            }
        }

        $this->info('Done generating slugs!');
    }
}
