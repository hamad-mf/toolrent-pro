<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'platform_name' => 'ToolRent Pro',
            'support_email' => 'support@toolrent.com',
            'default_plan' => 'Basic',
            'maintenance_mode' => '0',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
