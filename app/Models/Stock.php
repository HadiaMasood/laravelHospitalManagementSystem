<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'supplier_id',
        'batch_number',
        'quantity',
        'purchase_price',
        'selling_price',
        'expiry_date',
        'purchase_date',
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'purchase_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'stock_value',
        'unit_price',
        'days_until_expiry',
        'is_expiring',
        'is_expired'
    ];

    // =====================
    // RELATIONSHIPS
    // =====================

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // =====================
    // ACCESSORS
    // =====================

    public function getStockValueAttribute()
    {
        return $this->quantity * $this->selling_price;
    }

    public function getUnitPriceAttribute()
    {
        return $this->selling_price;
    }

    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return 0;
        }
        
        // Return negative number if expired, positive if not
        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiringAttribute()
    {
        $days = $this->days_until_expiry;
        return $days <= 30 && $days > 0;
    }

    public function getIsExpiredAttribute()
    {
        return $this->days_until_expiry < 0;
    }

    // =====================
    // SCOPES
    // =====================

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->addDays($days))
                     ->where('expiry_date', '>', Carbon::now())
                     ->where('quantity', '>', 0);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now())
                     ->where('quantity', '>', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('quantity', '>', 0)
                     ->where('expiry_date', '>', Carbon::now());
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('medicine', function($q) {
            $q->whereRaw('total_stock < reorder_level');
        });
    }
}