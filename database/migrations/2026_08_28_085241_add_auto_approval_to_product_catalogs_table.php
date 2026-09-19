<?php

use App\Models\ProductCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tableName = (new ProductCatalog())->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'auto_approve_override')) {
                $table->boolean('auto_approve_override')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn($table->getTable(), 'auto_approve_min_qty')) {
                $table->unsignedInteger('auto_approve_min_qty')->nullable()->after('auto_approve_override');
            }
        });
    }

    public function down(): void
    {
        $tableName = (new ProductCatalog())->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn($table->getTable(), 'auto_approve_override')) {
                $columns[] = 'auto_approve_override';
            }
            if (Schema::hasColumn($table->getTable(), 'auto_approve_min_qty')) {
                $columns[] = 'auto_approve_min_qty';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};