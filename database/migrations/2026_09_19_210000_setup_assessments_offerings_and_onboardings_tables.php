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
        // 1. Upgrade assessments table
        Schema::table('assessments', function (Blueprint $table) {
            $table->string('title')->default('');
            $table->string('test_type')->default('Technical Test'); // Technical Test, Logic Test, Administrative Test, Psychological Test, Personality Test
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('duration')->default(30); // in minutes
            $table->integer('passing_score')->default(70);
            $table->boolean('is_active')->default(true);
        });

        // 2. Upgrade assessment_questions table
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->cascadeOnDelete();
            $table->text('question_text')->nullable();
            $table->integer('points')->default(10);
            $table->text('explanation')->nullable();
        });

        // 3. Upgrade assessment_answers table
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->foreignId('assessment_question_id')->nullable()->constrained('assessment_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
        });

        // 4. Create candidate_test_results table
        Schema::create('candidate_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignId('candidate_application_id')->nullable()->constrained('candidate_applications')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->string('result')->default('pending'); // passed, failed, pending
            $table->integer('duration_spent')->default(0); // in seconds
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->json('answers_payload')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Upgrade offerings table
        Schema::table('offerings', function (Blueprint $table) {
            $table->foreignId('candidate_application_id')->nullable()->constrained('candidate_applications')->nullOnDelete();
            $table->string('department')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('employment_type')->default('Full-time'); // Full-time, Contract, Internship, Part-time
            $table->date('join_date')->nullable();
            $table->text('benefits')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
        });

        // 6. Upgrade onboardings table
        Schema::table('onboardings', function (Blueprint $table) {
            $table->foreignId('candidate_application_id')->nullable()->constrained('candidate_applications')->nullOnDelete();
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->nullOnDelete();
            $table->date('join_date')->nullable();
            $table->string('ktp_number')->nullable();
            $table->string('ktp_file')->nullable();
            $table->boolean('ktp_verified')->default(false);
            $table->string('npwp_number')->nullable();
            $table->string('npwp_file')->nullable();
            $table->boolean('npwp_verified')->default(false);
            $table->string('bpjs_number')->nullable();
            $table->string('bpjs_file')->nullable();
            $table->boolean('bpjs_verified')->default(false);
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_book_file')->nullable();
            $table->boolean('bank_verified')->default(false);
            $table->string('ijazah_file')->nullable();
            $table->boolean('ijazah_verified')->default(false);
            $table->string('pas_foto_file')->nullable();
            $table->boolean('pas_foto_verified')->default(false);
            $table->string('contract_file')->nullable();
            $table->boolean('contract_signed')->default(false);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->boolean('emergency_verified')->default(false);
            $table->timestamp('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_test_results');

        Schema::table('onboardings', function (Blueprint $table) {
            $table->dropForeign(['candidate_application_id']);
            $table->dropForeign(['offering_id']);
            $table->dropColumn([
                'candidate_application_id',
                'offering_id',
                'join_date',
                'ktp_number',
                'ktp_file',
                'ktp_verified',
                'npwp_number',
                'npwp_file',
                'npwp_verified',
                'bpjs_number',
                'bpjs_file',
                'bpjs_verified',
                'bank_name',
                'bank_account_number',
                'bank_account_holder',
                'bank_book_file',
                'bank_verified',
                'ijazah_file',
                'ijazah_verified',
                'pas_foto_file',
                'pas_foto_verified',
                'contract_file',
                'contract_signed',
                'emergency_contact_name',
                'emergency_contact_relation',
                'emergency_contact_phone',
                'emergency_verified',
                'completed_at',
            ]);
        });

        Schema::table('offerings', function (Blueprint $table) {
            $table->dropForeign(['candidate_application_id']);
            $table->dropColumn([
                'candidate_application_id',
                'department',
                'salary',
                'employment_type',
                'join_date',
                'benefits',
                'sent_at',
                'responded_at',
            ]);
        });

        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropForeign(['assessment_question_id']);
            $table->dropColumn(['assessment_question_id', 'answer_text', 'is_correct']);
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropColumn(['assessment_id', 'question_text', 'points', 'explanation']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn(['title', 'test_type', 'position_id', 'description', 'duration', 'passing_score', 'is_active']);
        });
    }
};
