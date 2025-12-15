<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->string('barcode')->unique();
            $table->integer('reorder_level')->default(0);
            $table->unsignedBigInteger('supplier_id');
            $table->timestamps();

            // Foreign key
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('medicines');
    }
};
