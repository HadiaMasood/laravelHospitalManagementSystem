<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->integer('total_stock')->default(0)->after('reorder_level');
        });
        
        // Update total_stock for existing medicines
        $medicines = DB::table('medicines')->get();
        foreach ($medicines as $medicine) {
            $totalStock = DB::table('stocks')
                ->where('medicine_id', $medicine->id)
                ->where('quantity', '>', 0)
                ->where('expiry_date', '>', now())
                ->sum('quantity');
            
            DB::table('medicines')
                ->where('id', $medicine->id)
                ->update(['total_stock' => $totalStock]);
        }
    }

    public function down()
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn('total_stock');
        });
    }
};