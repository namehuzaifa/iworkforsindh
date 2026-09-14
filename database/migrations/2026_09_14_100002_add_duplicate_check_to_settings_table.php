<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'duplicate_check_enabled')) {
                // Off by default so installing this migration cannot change
                // how a running site publishes jobs. The admin turns it on.
                $table->boolean('duplicate_check_enabled')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('duplicate_check_enabled');
        });
    }
};
