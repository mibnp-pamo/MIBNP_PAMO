<?php

namespace Database\Seeders;

use App\Models\NewsPost;
use Illuminate\Database\Seeder;

class NewsPostSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Community-linked conservation support',
                'slug' => 'community-linked-conservation-support',
                'summary' => 'Browse Daboville Foundation updates for environmental and community work that can strengthen the wider conservation setting around the park.',
                'category' => 'Partner update',
                'source' => 'Daboville Foundation',
                'external_url' => 'https://www.dabovillefoundation.org/',
                'image_asset' => 'bckgrndHome/hbg1.jpg',
                'image_alt' => 'Mountain landscape representing community-linked conservation support',
                'is_pinned' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Corridor stories across connected habitats',
                'slug' => 'corridor-stories-across-connected-habitats',
                'summary' => 'The Biodiversity Corridors site highlights landscape-scale conservation efforts that help explain how protected areas connect beyond a single park boundary.',
                'category' => 'Conservation',
                'source' => 'Biodiversity Corridors',
                'external_url' => 'https://bdcorridors.wordpress.com/corridor-sites/',
                'image_asset' => 'bckgrndHome/hbg2.jpg',
                'image_alt' => 'Ridges and connected habitat representing biodiversity corridor coverage',
                'is_pinned' => false,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Island biodiversity initiatives',
                'slug' => 'island-biodiversity-initiatives',
                'summary' => 'Mindoro Biodiversity Conservation Foundation Inc. shares work tied to habitat protection, species conservation, and environmental action across the island.',
                'category' => 'Partner update',
                'source' => 'MBCFI',
                'external_url' => 'https://www.mbcfi.org.ph/',
                'image_asset' => 'bckgrndHome/hbg3.jpeg',
                'image_alt' => 'Forest habitat representing island biodiversity initiatives',
                'is_pinned' => false,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'Tamaraw-related public announcements',
                'slug' => 'tamaraw-related-public-announcements',
                'summary' => 'Follow Tamaraw DENR for outreach posts and public-facing conservation updates connected to the park\'s most iconic flagship species.',
                'category' => 'Field update',
                'source' => 'Tamaraw DENR',
                'external_url' => 'https://www.facebook.com/tamarawdenr/',
                'image_asset' => 'bckgrndHome/tamaraw.JPG',
                'image_alt' => 'Tamaraw representing public field announcements and conservation updates',
                'is_pinned' => false,
                'published_at' => now()->subDays(14),
            ],
        ];

        foreach ($items as $item) {
            NewsPost::query()->firstOrCreate(
                ['slug' => $item['slug']],
                $item + ['status' => NewsPost::STATUS_PUBLISHED],
            );
        }
    }
}
