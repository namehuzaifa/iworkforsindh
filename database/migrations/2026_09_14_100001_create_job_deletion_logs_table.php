<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * An audit trail for the duplicate cleanup. Jobs are hard deleted, so
     * without this there is no way to answer "what did we remove and why".
     */
    public function up(): void
    {
        Schema::create('job_deletion_logs', function (Blueprint $table) {
            $table->id();
            // Deliberately not a foreign key: the job it points at is gone.
            $table->unsignedBigInteger('job_id');
            $table->string('job_title');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name')->nullable();
            // The admin who acted, stored by id and by name. No foreign key:
            // admins and users are separate tables, and the name has to
            // survive the account being removed.
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->string('deleted_by_name')->nullable();
            $table->string('action', 20)->default('deleted');
            $table->unsignedBigInteger('kept_job_id')->nullable();
            $table->unsignedInteger('applications_count')->default(0);
            $table->unsignedInteger('bookmarks_count')->default(0);
            $table->json('snapshot')->nullable();
            $table->timestamps();

            $table->index('job_id');
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_deletion_logs');
    }
};
