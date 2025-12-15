<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            // Add unit_price column if it doesn't exist
            if (!Schema::hasColumn('stocks', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->after('purchase_price')->default(0);
            }
            
            // Add stock_value column if it doesn't exist
            if (!Schema::hasColumn('stocks', 'stock_value')) {
                $table->decimal('stock_value', 10, 2)->after('quantity')->default(0);
            }
        });
        
        // Update existing records: set unit_price from selling_price and calculate stock_value
        DB::table('stocks')->update([
            'unit_price' => DB::raw('COALESCE(selling_price, purchase_price)'),
            'stock_value' => DB::raw('quantity * COALESCE(selling_price, purchase_price)')
        ]);
    }

    public function down()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'stock_value']);
        });
    }
};