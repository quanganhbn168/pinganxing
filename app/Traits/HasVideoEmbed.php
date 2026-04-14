<?php

namespace App\Traits;

trait HasVideoEmbed
{
    /**
     * Chuyển YouTube/Vimeo watch URL sang embed URL.
     */
    public function toEmbedUrl(?string $url): ?string
    {
        if (!$url) return null;

        // YouTube: youtube.com/watch?v=ID hoặc youtu.be/ID
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}?autoplay=1&rel=0";
        }

        // Vimeo: vimeo.com/ID
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}?autoplay=1";
        }

        return $url;
    }
}
