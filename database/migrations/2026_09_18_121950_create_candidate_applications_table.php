<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('candidate_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', ['screening', 'assessment', 'interview', 'offering', 'onboarding', 'hired', 'rejected'])->default('screening');
            $table->enum('status', ['pending', 'in_review', 'passed', 'rejected'])->default('pending');
            $table->integer('match_score')->nullable()->default(85);
            $table->string('resume_url')->nullable();
            $table->text('cover_letter')->nullable();
            $table->text('hr_notes')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_applications');
    }
};
