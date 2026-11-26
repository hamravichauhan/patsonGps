<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            if (!Schema::hasColumn('product_catalog', 'mrp')) {
                $table->decimal('mrp', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('product_catalog', 'min_order_qty')) {
                $table->integer('min_order_qty')->default(1)->after('units_per_box');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_wholesaler')) {
                $table->boolean('is_wholesaler')->default(false)->after('is_admin');
            }
            if (!Schema::hasColumn('users', 'wholesale_requested')) {
                $table->boolean('wholesale_requested')->default(false)->after('is_wholesaler');
            }
            if (!Schema::hasColumn('users', 'business_name')) {
                $table->string('business_name')->nullable()->after('wholesale_requested');
            }
            if (!Schema::hasColumn('users', 'gstin')) {
                $table->string('gstin')->nullable()->after('business_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            if (Schema::hasColumn('product_catalog', 'mrp')) {
                $table->dropColumn('mrp');
            }
            if (Schema::hasColumn('product_catalog', 'min_order_qty')) {
                $table->dropColumn('min_order_qty');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            foreach (['is_wholesaler', 'wholesale_requested', 'business_name', 'gstin'] as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $cols[] = $c;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
