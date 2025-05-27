<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Skill;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('skilled_labours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->longText('description');
            $table->foreignId('profession_id')->constrained('professions')->cascadeOnDelete();
            $table->foreignIdFor(Skill::class)->constrained()->cascadeOnDelete();
            $table->string('cnic')->unique();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('marital_status')->nullable();
            $table->datetime('birth_date');
            $table->string('phone');
            $table->string('image')->default('backend/image/default.png');
            $table->string('cnic_image')->default('backend/image/default.png');
            $table->string('fingerprint_image')->default('backend/image/default.png');
            $table->enum('role', ['skilledlabor'])->default('skilledlabor');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skilled_labours');
    }
};
