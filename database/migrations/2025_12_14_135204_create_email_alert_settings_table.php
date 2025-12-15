<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('email_alert_settings', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type'); // expiry, low_stock, expired
            $table->boolean('is_enabled')->default(true);
            $table->integer('days_before')->default(30); // For expiry alerts
            $table->string('recipients'); // Comma-separated emails
            $table->time('send_time')->default('09:00:00'); // When to send daily
            $table->timestamps();
        });

        // Insert default settings
        DB::table('email_alert_settings')->insert([
            [
                'alert_type' => 'expiry',
                'is_enabled' => true,
                'days_before' => 30,
                'recipients' => 'admin@hospital.com',
                'send_time' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'alert_type' => 'low_stock',
                'is_enabled' => true,
                'days_before' => 0,
                'recipients' => 'admin@hospital.com',
                'send_time' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'alert_type' => 'expired',
                'is_enabled' => true,
                'days_before' => 0,
                'recipients' => 'admin@hospital.com',
                'send_time' => '09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('email_alert_settings');
    }
};