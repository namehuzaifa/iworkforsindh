<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Duplicate detection needs a fingerprint it can group on. Computing it
     * from the description on every request does not scale, so it is stored
     * once per job and indexed.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            if (! Schema::hasColumn('jobs', 'duplicate_hash')) {
                $table->char('duplicate_hash', 32)->nullable()->after('waiting_for_edit_approval');
                $table->index('duplicate_hash');
            }

            if (! Schema::hasColumn('jobs', 'duplicate_of_job_id')) {
                $table->unsignedBigInteger('duplicate_of_job_id')->nullable()->after('duplicate_hash');
            }

            if (! Schema::hasColumn('jobs', 'duplicate_flagged_at')) {
                $table->timestamp('duplicate_flagged_at')->nullable()->after('duplicate_of_job_id');
            }
        });

        // The historical scan groups by company and title before it looks at
        // anything heavier. Without this index that is a full table scan.
        Schema::table('jobs', function (Blueprint $table) {
            $table->index(['company_id', 'title'], 'jobs_company_id_title_index');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('jobs_company_id_title_index');
            $table->dropIndex(['duplicate_hash']);
            $table->dropColumn(['duplicate_hash', 'duplicate_of_job_id', 'duplicate_flagged_at']);
        });
    }
};
