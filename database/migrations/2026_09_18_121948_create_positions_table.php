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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department');
            $table->string('location')->default('Jakarta (Hybrid)');
            $table->string('type')->default('Full-time');
            $table->string('experience_level')->default('Mid Level');
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->enum('status', ['active', 'closed', 'draft'])->default('active');
            $table->date('deadline')->nullable();
            $table->integer('quota')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
