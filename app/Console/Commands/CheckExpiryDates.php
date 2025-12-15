<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiryAlertMail;

class CheckExpiryDates extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:check-expiry {--days=30 : Number of days to check}';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring medicines and send email alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        $expiringStocks = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->where('quantity', '>', 0)
            ->get();

        if ($expiringStocks->count() > 0) {
            $this->info("Found {$expiringStocks->count()} expiring medicines");
            
            // Send email alert
            try {
                Mail::to(config('mail.admin_email', 'admin@medicalstore.com'))
                    ->send(new ExpiryAlertMail($expiringStocks));
                
                $this->info('Email alert sent successfully');
            } catch (\Exception $e) {
                $this->error('Failed to send email: ' . $e->getMessage());
            }
            
            // Display in console
            foreach ($expiringStocks as $stock) {
                $daysUntilExpiry = Carbon::now()->diffInDays($stock->expiry_date);
                $this->warn("{$stock->medicine->name} (Batch: {$stock->batch_number}) expires in {$daysUntilExpiry} days");
            }
        } else {
            $this->info('No expiring medicines found');
        }

        return 0;
    }
}