<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_catalog')) {
            Schema::create('product_catalog', function (Blueprint $table) {
                $table->id();
                $table->string('category')->nullable();
                $table->string('product_name');
                $table->string('size')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_active')->default(1);
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_featured')->default(0);
                $table->boolean('in_stock')->default(1);
                $table->string('image')->nullable();
                $table->string('image_status')->default('auto');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (app()->environment('testing')) {
            Schema::dropIfExists('product_catalog');
        }
    }
};
