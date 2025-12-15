<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAlertSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_type',
        'is_enabled',
        'days_before',
        'recipients',
        'send_time',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get recipients as an array
     */
    public function getRecipientsArrayAttribute()
    {
        return array_filter(array_map('trim', explode(',', $this->recipients)));
    }
}