<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'category',
        'description',
        'unit_price',
        'barcode',
        'reorder_level',
        'supplier_id',
        
        'barcode',
       
        'stock',
        'description',
        'manufacturer'
    ];


    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getTotalStockAttribute()
    {
       
        return $this->stocks()->where('quantity', '>', 0)
                         ->where('expiry_date', '>', now())
                         ->sum('quantity');
    }
}
