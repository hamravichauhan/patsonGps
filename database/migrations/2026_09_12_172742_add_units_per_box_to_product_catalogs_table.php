<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->unsignedInteger('units_per_box')->default(12)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('product_catalog', function (Blueprint $table) {
            $table->dropColumn('units_per_box');
        });
    }
};
