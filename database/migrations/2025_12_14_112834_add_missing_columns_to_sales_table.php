<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('sales', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('sales', 'customer_phone')) {
                $table->string('customer_phone')->nullable();
            }
            if (!Schema::hasColumn('sales', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'tax')) {
                $table->decimal('tax', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'total')) {
                $table->decimal('total', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'payment_method')) {
                $table->string('payment_method')->default('cash');
            }
            if (!Schema::hasColumn('sales', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }
            if (!Schema::hasColumn('sales', 'invoice_number')) {
                $table->string('invoice_number')->unique();
            }
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = ['customer_name', 'customer_phone', 'subtotal', 'discount', 'tax', 'total', 'payment_method', 'user_id', 'invoice_number'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};