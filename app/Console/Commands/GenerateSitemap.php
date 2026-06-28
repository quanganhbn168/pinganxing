<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slug;
use App\Models\Tour;
use App\Models\TourCategory;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap.xml';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        $this->addStaticPages($sitemap);
        $this->addTours($sitemap);
        $this->addDynamicSlugs($sitemap);

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->updateRobotsSitemapUrl();

        $this->info('Sitemap generated successfully.');

        return self::SUCCESS;
    }

    protected function addStaticPages(Sitemap $sitemap): void
    {
        $pages = [
            ['url' => route('home'), 'priority' => 1.0],
            ['url' => route('frontend.posts.index'), 'priority' => 0.9],
            ['url' => route('frontend.tours.index'), 'priority' => 0.9],
            ['url' => route('frontend.services.index'), 'priority' => 0.8],
            ['url' => route('frontend.projects.index'), 'priority' => 0.8],
            ['url' => route('frontend.intro.index'), 'priority' => 0.5],
            ['url' => route('frontend.careers.index'), 'priority' => 0.5],
            ['url' => route('contact.show'), 'priority' => 0.5],
            ['url' => route('consulting.index'), 'priority' => 0.5],
            ['url' => route('agency.index'), 'priority' => 0.5],
        ];

        foreach ($pages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setPriority($page['priority'])
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            );
        }
    }

    protected function addTours(Sitemap $sitemap): void
    {
        TourCategory::query()
            ->where('status', 1)
            ->whereNotNull('slug')
            ->get()
            ->each(function (TourCategory $category) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('frontend.tours.category', ['slug' => $category->slug_value]))
                        ->setLastModificationDate($category->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            });

        Tour::query()
            ->where('status', 1)
            ->whereNotNull('slug')
            ->with('category')
            ->get()
            ->filter(fn (Tour $tour) => $tour->category?->status && filled($tour->category?->slug_value))
            ->each(function (Tour $tour) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('frontend.tours.show', [
                        'categorySlug' => $tour->category->slug_value,
                        'slug' => $tour->slug_value,
                    ]))
                        ->setLastModificationDate($tour->updated_at)
                        ->setPriority(0.8)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                );
            });
    }

    protected function updateRobotsSitemapUrl(): void
    {
        $path = public_path('robots.txt');
        $content = file_exists($path) ? file_get_contents($path) : "User-agent: *\nAllow: /\n";
        $content = preg_replace('/^Sitemap:.*$/mi', '', $content);
        $content = rtrim((string) $content) . PHP_EOL . PHP_EOL
            . 'Sitemap: ' . url('/sitemap.xml') . PHP_EOL;

        file_put_contents($path, $content);
    }

    protected function addDynamicSlugs(Sitemap $sitemap): void
    {
        Slug::query()
            ->with('sluggable')
            ->whereNotNull('slug')
            ->chunkById(500, function ($slugs) use ($sitemap) {
                foreach ($slugs as $slug) {
                    $model = $slug->sluggable;

                    if (! $model) {
                        continue;
                    }

                    if (isset($model->status) && ! $model->status) {
                        continue;
                    }

                    $url = $this->resolveUrl($model, $slug->slug);

                    if (! $url) {
                        continue;
                    }

                    $sitemap->add(
                        Url::create($url)
                            ->setLastModificationDate(
                                $model->updated_at ?? $slug->updated_at
                            )
                            ->setPriority($this->resolvePriority($model))
                            ->setChangeFrequency($this->resolveFrequency($model))
                    );
                }
            });
    }

    protected function resolveUrl(Model $model, string $slug): ?string
    {
        return match (true) {
            $model instanceof Page => route('frontend.slug.handle', $slug),
            $model instanceof PostCategory => route('frontend.post-category.bySlug', $slug),
            $model instanceof Post => route('frontend.post.bySlug', $slug),
            $model instanceof Category => route('frontend.product-category.bySlug', $slug),
            $model instanceof Product => route('frontend.product.bySlug', $slug),
            $model instanceof ServiceCategory => route('frontend.service-category.bySlug', $slug),
            $model instanceof Service => route('frontend.service.bySlug', $slug),
            $model instanceof ProjectCategory => route('frontend.project-category.bySlug', $slug),
            $model instanceof Project => route('frontend.project.bySlug', $slug),

            default => null,
        };
    }

    protected function resolvePriority(Model $model): float
    {
        return match (true) {
            $model instanceof Category => 0.8,
            $model instanceof Product => 0.8,
            $model instanceof ServiceCategory => 0.8,
            $model instanceof Service => 0.8,
            $model instanceof PostCategory => 0.7,
            $model instanceof Post => 0.7,
            $model instanceof ProjectCategory => 0.7,
            $model instanceof Project => 0.7,

            default => 0.5,
        };
    }

    protected function resolveFrequency(Model $model): string
    {
        return match (true) {
            $model instanceof Category => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof Product => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof ServiceCategory => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof Service => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof PostCategory => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof Post => Url::CHANGE_FREQUENCY_WEEKLY,
            $model instanceof ProjectCategory => Url::CHANGE_FREQUENCY_MONTHLY,
            $model instanceof Project => Url::CHANGE_FREQUENCY_MONTHLY,

            default => Url::CHANGE_FREQUENCY_MONTHLY,
        };
    }
}
