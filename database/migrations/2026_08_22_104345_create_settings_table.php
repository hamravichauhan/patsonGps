<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
        });

        // Insert initial configuration defaults
        DB::table('settings')->insert([
            ['key' => 'whatsapp_admin_number', 'value' => '919876543210'],
            ['key' => 'auto_approval_min_qty', 'value' => '10'],
            ['key' => 'auto_approval_enabled', 'value' => '1'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};