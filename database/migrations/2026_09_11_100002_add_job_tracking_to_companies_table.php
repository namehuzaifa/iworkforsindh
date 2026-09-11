<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks the handful of in-house accounts the team posts from. Only these
     * companies get the "Posted by" / "Job source" fields on the job form —
     * every other company keeps the form exactly as it is today.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_job_tracking')->default(false)->after('is_counselor');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('is_job_tracking');
        });
    }
};
