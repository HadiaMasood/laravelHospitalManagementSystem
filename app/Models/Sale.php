<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

   protected $fillable = [
    'invoice_number',
    'customer_name',
    'customer_phone',
    'total_amount',
    'discount',
    'tax',
    'final_amount',
    'payment_method',
    'user_id',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}