<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminAccessSeeder::class);
        $this->call(StoreSettingsSeeder::class);
        $this->call(MediaSeeder::class);
        $this->call(CommerceSeeder::class);
        $this->call(MarketingSeeder::class);
    }
}
