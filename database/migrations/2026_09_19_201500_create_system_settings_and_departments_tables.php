<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table for System Settings (key-value store)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Table for Departments / Divisi
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial default settings
        $defaultSettings = [
            'app_name' => 'SisRec',
            'app_version' => '2.5',
            'app_tagline' => 'Sistem Rekrutmen Terpadu',
            'company_name' => 'TalentFlow HRMS',
            'footer_text' => 'Sistem Recruitment V 2.5 • All Rights Reserved',
            'app_logo' => null,
        ];

        foreach ($defaultSettings as $key => $value) {
            DB::table('system_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Seed initial default departments
        $defaultDepartments = [
            ['name' => 'Technology & Engineering', 'code' => 'TECH', 'description' => 'Divisi pengembangan perangkat lunak, sistem, dan infrastruktur IT'],
            ['name' => 'Product & Design', 'code' => 'PD', 'description' => 'Divisi manajemen produk, UI/UX design, dan riset pengguna'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Divisi rekrutmen, kompensasi & benefit, serta employee engagement'],
            ['name' => 'Data & Analytics', 'code' => 'DATA', 'description' => 'Divisi data science, business intelligence, dan data engineering'],
            ['name' => 'Finance & Accounting', 'code' => 'FIN', 'description' => 'Divisi keuangan perusahaan, perpajakan, dan pembukuan'],
            ['name' => 'Marketing & Growth', 'code' => 'MKT', 'description' => 'Divisi pemasaran digital, branding produk, dan akusisi pengguna'],
            ['name' => 'Sales & Business Development', 'code' => 'SALES', 'description' => 'Divisi penjualan B2B/B2C dan pengembangan kemitraan strategis'],
        ];

        foreach ($defaultDepartments as $dept) {
            DB::table('departments')->insert([
                'name' => $dept['name'],
                'code' => $dept['code'],
                'description' => $dept['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('system_settings');
    }
};
