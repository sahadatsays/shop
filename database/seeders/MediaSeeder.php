<?php

namespace Database\Seeders;

use App\Models\MediaFolder;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $folders = [
            ['name' => 'Products', 'slug' => 'products', 'sort_order' => 1],
            ['name' => 'Categories', 'slug' => 'categories', 'sort_order' => 2],
            ['name' => 'Marketing', 'slug' => 'marketing', 'sort_order' => 3],
            ['name' => 'Store Assets', 'slug' => 'store-assets', 'sort_order' => 4],
        ];

        foreach ($folders as $folder) {
            MediaFolder::query()->updateOrCreate(
                ['slug' => $folder['slug'], 'parent_id' => null],
                [
                    'name' => $folder['name'],
                    'sort_order' => $folder['sort_order'],
                ],
            );
        }
    }
}
