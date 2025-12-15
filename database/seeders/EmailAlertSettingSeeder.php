<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailAlertSetting;

class EmailAlertSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'alert_type' => 'expiry',
                'is_enabled' => true,
                'days_before' => 90,
                'recipients' => 'admin@pharmacy.com',
                'send_time' => '09:00:00',
            ],
            [
                'alert_type' => 'low_stock',
                'is_enabled' => true,
                'days_before' => 0,
                'recipients' => 'admin@pharmacy.com',
                'send_time' => '09:00:00',
            ],
            [
                'alert_type' => 'expired',
                'is_enabled' => true,
                'days_before' => 0,
                'recipients' => 'admin@pharmacy.com',
                'send_time' => '09:00:00',
            ],
        ];

        foreach ($settings as $setting) {
            EmailAlertSetting::updateOrCreate(
                ['alert_type' => $setting['alert_type']],
                $setting
            );
        }
    }
}