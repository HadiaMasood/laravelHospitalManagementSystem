<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAlertSetting;
use App\Models\Stock;
use App\Models\Medicine;
use App\Mail\ExpiringStockAlert;
use App\Mail\LowStockAlert;
use App\Mail\ExpiredStockAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EmailAlertController extends Controller
{
    public function index()
    {
        $settings = EmailAlertSetting::all();
        return view('admin.email-alerts.index', compact('settings'));
    }

    public function update(Request $request, EmailAlertSetting $setting)
    {
        $request->validate([
            'is_enabled' => 'required|boolean',
            'days_before' => 'required|integer|min:0',
            'recipients' => 'required|string',
            'send_time' => 'required',
        ]);

        $setting->update($request->all());

        return back()->with('success', 'Email alert settings updated successfully!');
    }

    public function sendTestEmail(Request $request, $type)
    {
        $setting = EmailAlertSetting::where('alert_type', $type)->first();
        
        if (!$setting || !$setting->is_enabled) {
            return back()->with('error', 'This alert is disabled.');
        }

        try {
            switch ($type) {
                case 'expiry':
                    $stocks = Stock::with(['medicine', 'supplier'])
                        ->where('quantity', '>', 0)
                        ->where('expiry_date', '<=', Carbon::now()->addDays($setting->days_before))
                        ->where('expiry_date', '>', Carbon::now())
                        ->get();
                    
                    if ($stocks->count() > 0) {
                        Mail::to($setting->recipients_array)
                            ->send(new ExpiringStockAlert($stocks, $setting->days_before));
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
                    }
                    break;
            }

            return back()->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}