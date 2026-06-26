<?php

namespace Database\Seeders;

use App\Models\Partner;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    public function run(): void
    {
        $partners = [
            ['name' => 'Vietnam Airlines', 'url' => 'https://www.vietnamairlines.com', 'sort_order' => 1],
            ['name' => 'Vietjet Air', 'url' => 'https://www.vietjetair.com', 'sort_order' => 2],
            ['name' => 'Vinpearl', 'url' => 'https://vinpearl.com', 'sort_order' => 3],
            ['name' => 'Sun World', 'url' => 'https://sunworld.vn', 'sort_order' => 4],
            ['name' => 'FLC Hotels & Resorts', 'url' => 'https://flchotelsresorts.com', 'sort_order' => 5],
            ['name' => 'Saigontourist', 'url' => 'https://www.saigontourist.net', 'sort_order' => 6],
        ];

        foreach ($partners as $partner) {
            $media = Media::firstOrCreate(
                ['name' => 'partner-' . str($partner['name'])->slug()],
                [
                    'disk' => 'public',
                    'directory' => 'media',
                    'visibility' => 'public',
                    'path' => 'https://placehold.co/320x140/png?text=' . rawurlencode($partner['name']),
                    'width' => 320,
                    'height' => 140,
                    'type' => 'image/png',
                    'ext' => 'png',
                    'alt' => $partner['name'],
                    'title' => $partner['name'],
                ]
            );

            Partner::updateOrCreate(
                ['name' => $partner['name']],
                $partner + [
                    'image_id' => $media->id,
                    'status' => true,
                ]
            );
        }
    }
}
