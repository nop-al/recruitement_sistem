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
        Schema::table('interview_assessments', function (Blueprint $table) {
            $table->string('interview_method', 20)->default('online')->after('interview_type');
            $table->string('location')->nullable()->after('room_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interview_assessments', function (Blueprint $table) {
            $table->dropColumn(['interview_method', 'location']);
        });
    }
};
