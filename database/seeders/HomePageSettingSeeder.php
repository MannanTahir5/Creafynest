<?php

namespace Database\Seeders;

use App\Models\HomePageSetting;
use Illuminate\Database\Seeder;

class HomePageSettingSeeder extends Seeder
{
    public function run(): void
    {
        HomePageSetting::singleton();
    }
}
