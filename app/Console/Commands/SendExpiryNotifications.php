<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\User;
use App\Mail\ExpiryNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiry:notify 
                            {--days=30 : Number of days to check for expiring medicines}
                            {--email= : Specific email to send notification to}
                            {--type=daily : Notification type (daily, weekly, monthly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications for expired and expiring medicines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $email = $this->option('email');
        $type = $this->option('type');

        $this->info("Checking for medicines expiring within {$days} days...");

        // Get expired medicines
        $expiredItems = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<', Carbon::now())
            ->where('quantity', '>', 0)
            ->get();

        // Get expiring medicines
        $expiringItems = Stock::with(['medicine', 'supplier'])
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->where('quantity', '>', 0)
            ->get();

        $this->info("Found {$expiredItems->count()} expired items and {$expiringItems->count()} expiring items.");

        // If no items to report and it's not a forced notification, skip
        if ($expiredItems->isEmpty() && $expiringItems->isEmpty() && !$this->option('email')) {
            $this->info('No expired or expiring medicines found. No notification sent.');
            return 0;
        }

        // Determine recipients
        $recipients = [];
        
        if ($email) {
            $recipients[] = $email;
        } else {
            // Send to all admin users
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $recipients[] = $admin->email;
            }
        }

        if (empty($recipients)) {
            $this->error('No recipients found. Please specify an email or ensure admin users exist.');
            return 1;
        }

        // Send notifications
        $successCount = 0;
        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new ExpiryNotification($expiringItems, $expiredItems, $type));
                $successCount++;
                $this->info("Notification sent to: {$recipient}");
            } catch (\Exception $e) {
                $this->error("Failed to send notification to {$recipient}: " . $e->getMessage());
            }
        }

        $this->info("Expiry notifications sent successfully to {$successCount} recipients.");

        // Log the notification activity
        $this->info('Expiry notification sent', [
            'expired_count' => $expiredItems->count(),
            'expiring_count' => $expiringItems->count(),
            'recipients_count' => $successCount,
            'type' => $type
        ]);

        return 0;
    }
}