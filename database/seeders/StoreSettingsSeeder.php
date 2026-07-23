<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use App\Support\StoreSettings;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (StoreSetting::query()->exists()) {
            return;
        }

        StoreSettings::seedDefaults();
        StoreSettings::clearCache();
    }
}
