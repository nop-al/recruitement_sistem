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
        Schema::create('interview_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('interview_type')->default('User Technical Interview');
            $table->dateTime('scheduled_at');
            $table->string('room_link')->nullable();
            $table->integer('technical_score')->nullable();
            $table->integer('communication_score')->nullable();
            $table->integer('culture_score')->nullable();
            $table->integer('total_score')->nullable();
            $table->text('notes')->nullable();
            $table->enum('recommendation', ['pass', 'fail', 'hold'])->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_assessments');
    }
};
