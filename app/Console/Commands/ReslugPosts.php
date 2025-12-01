<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Services\SlugService;

class ReslugPosts extends Command
{
    protected $signature = 'posts:reslug {--ids=* : Chỉ reslug các ID} {--dry : Chạy thử, không ghi DB}';
    protected $description = 'Regenerate slugs for posts (polymorphic Slug)';

    public function handle(SlugService $slugService)
    {
        $query = Post::query()->where('status', 1);
        if ($ids = $this->option('ids')) $query->whereIn('id', $ids);

        $count = 0;
        $query->orderBy('id')->chunkById(200, function($posts) use (&$count, $slugService){
            foreach ($posts as $post) {
                $base = $post->title; // có thể đổi thành $post->title.'-'.$post->id nếu muốn ổn định
                $new = $slugService->upsert($post, $base);
                $this->info("Post #{$post->id}: {$new}");
                $count++;
            }
        });

        $this->info("Done. {$count} posts processed.");
        return Command::SUCCESS;
    }
}
