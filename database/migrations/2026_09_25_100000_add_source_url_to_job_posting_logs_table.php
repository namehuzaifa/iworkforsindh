<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The scraper sends the original URL of every job it posts. Keeping it
     * lets the same listing be recognised when the scraper picks it up again.
     *
     * URLs can be longer than an index allows, so the lookup goes through a
     * sha1 of the URL. The unique index also stops two requests racing to
     * post the same listing twice; MySQL allows any number of NULLs, so rows
     * from the normal posting flow are unaffected.
     */
    public function up(): void
    {
        Schema::table('job_posting_logs', function (Blueprint $table) {
            $table->text('source_url')->nullable()->after('source_note');
            $table->char('source_url_hash', 40)->nullable()->unique()->after('source_url');
        });
    }

    public function down(): void
    {
        Schema::table('job_posting_logs', function (Blueprint $table) {
            $table->dropUnique(['source_url_hash']);
            $table->dropColumn(['source_url', 'source_url_hash']);
        });
    }
};
