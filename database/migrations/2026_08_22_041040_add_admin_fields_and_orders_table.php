<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Add is_admin column to users table if missing
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false);
            });
        }

        // 2. Create orders table
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->string('customer_name');
                $table->string('customer_phone')->index();
                $table->text('delivery_address')->nullable();
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->enum('status', ['pending', 'approved', 'dispatched', 'delivered', 'rejected'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->string('reorder_token')->nullable()->unique();
                $table->timestamps();
            });
        }

        // 3. Create order_items table
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('product_catalog_id')->nullable();
                $table->string('product_name');
                $table->string('size');
                $table->decimal('unit_price', 10, 2);
                $table->integer('quantity')->default(1);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }
};