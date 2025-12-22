<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->string('batch_number', 50);
            $table->integer('quantity');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->date('expiry_date');
            $table->date('manufacture_date')->nullable();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('purchase_date');
            
            $table->timestamps();
               $table->index('batch_number');
    $table->index('expiry_date');
    $table->index('medicine_id');
        });
    }
// Update your stocks migration

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};