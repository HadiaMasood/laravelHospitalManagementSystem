<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailAlertSetting;
use App\Models\Stock;
use App\Models\Medicine;
use App\Mail\ExpiringStockAlert;
use App\Mail\LowStockAlert;
use App\Mail\ExpiredStockAlert;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDailyAlerts extends Command
{
    protected $signature = 'alerts:send-daily';
    protected $description = 'Send daily email alerts for expiring, low stock, and expired medicines';

    public function handle()
    {
        $this->info('Checking for alerts to send...');

        $settings = EmailAlertSetting::where('is_enabled', true)->get();

        foreach ($settings as $setting) {
            // Check if it's time to send
            $currentTime = Carbon::now()->format('H:i');
            $sendTime = Carbon::parse($setting->send_time)->format('H:i');

            if ($currentTime == $sendTime) {
                $this->sendAlert($setting);
            }
        }

        $this->info('Alert check completed!');
    }

    private function sendAlert($setting)
    {
        try {
            switch ($setting->alert_type) {
                case 'expiry':
                    $stocks = Stock::with(['medicine', 'supplier'])
                        ->where('quantity', '>', 0)
                        ->where('expiry_date', '<=', Carbon::now()->addDays($setting->days_before))
                        ->where('expiry_date', '>', Carbon::now())
                        ->get();
                    
                    if ($stocks->count() > 0) {
                        Mail::to($setting->recipients_array)
                            ->send(new ExpiringStockAlert($stocks, $setting->days_before));
                        $this->info("Sent expiring stock alert to {$setting->recipients}");
                    }
                    break;

                case 'low_stock':
                    $medicines = Medicine::with('supplier')
                        ->get()
                        ->filter(function($medicine) {
                            return $medicine->current_stock < $medicine->reorder_level;
                        });
                    
                    if ($medicines->count() > 0) {
                        Mail::to($setting->recipients_array)
                            ->send(new LowStockAlert($medicines));
                        $this->info("Sent low stock alert to {$setting->recipients}");
                    }
                    break;

                case 'expired':
                    $stocks = Stock::with(['medicine', 'supplier'])
                        ->where('quantity', '>', 0)
                        ->where('expiry_date', '<', Carbon::now())
                        ->get();
                    
                    if ($stocks->count() > 0) {
                        Mail::to($setting->recipients_array)
                            ->send(new ExpiredStockAlert($stocks));
                        $this->info("Sent expired stock alert to {$setting->recipients}");
                    }
                    break;
            }
        } catch (\Exception $e) {
            $this->error("Failed to send {$setting->alert_type} alert: " . $e->getMessage());
        }
    }
}