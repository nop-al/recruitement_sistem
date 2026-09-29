<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Positions
            ['name' => 'positions.view', 'display_name' => 'Melihat Lowongan Pekerjaan', 'group' => 'Positions'],
            ['name' => 'positions.manage', 'display_name' => 'Mengelola Lowongan Pekerjaan', 'group' => 'Positions'],

            // Applications
            ['name' => 'applications.view', 'display_name' => 'Melihat Berkas Lamaran', 'group' => 'Applications'],
            ['name' => 'applications.manage', 'display_name' => 'Mengelola Tahap Lamaran', 'group' => 'Applications'],

            // Interviews
            ['name' => 'interviews.schedule', 'display_name' => 'Menjadwalkan Sesi Wawancara', 'group' => 'Interviews'],
            ['name' => 'interviews.conduct', 'display_name' => 'Melakukan Penilaian Wawancara', 'group' => 'Interviews'],

            // Assessments
            ['name' => 'assessments.manage', 'display_name' => 'Mengelola Tes & Bank Soal', 'group' => 'Assessments'],
            ['name' => 'assessments.take', 'display_name' => 'Mengikuti Ujian Online', 'group' => 'Assessments'],

            // Offerings
            ['name' => 'offerings.manage', 'display_name' => 'Menerbitkan & Mengirimkan Offering', 'group' => 'Offerings'],
            ['name' => 'offerings.respond', 'display_name' => 'Merespons Surat Penawaran Kerja', 'group' => 'Offerings'],

            // Onboarding
            ['name' => 'onboarding.manage', 'display_name' => 'Memantau Proses Onboarding', 'group' => 'Onboarding'],
            ['name' => 'onboarding.verify', 'display_name' => 'Verifikasi Dokumen Onboarding', 'group' => 'Onboarding'],
            ['name' => 'onboarding.submit', 'display_name' => 'Mengunggah Berkas Onboarding', 'group' => 'Onboarding'],

            // Documents
            ['name' => 'documents.download_any', 'display_name' => 'Mengunduh Dokumen Seluruh Kandidat', 'group' => 'Documents'],
            ['name' => 'documents.download_assigned', 'display_name' => 'Mengunduh Resume Kandidat Terjadwal', 'group' => 'Documents'],
            ['name' => 'documents.download_own', 'display_name' => 'Mengunduh Dokumen Milik Sendiri', 'group' => 'Documents'],

            // System
            ['name' => 'system.config', 'display_name' => 'Mengelola Konfigurasi & Identitas Sistem', 'group' => 'System'],
            ['name' => 'system.backup', 'display_name' => 'Melakukan Backup & Restore Sistem', 'group' => 'System'],
            ['name' => 'system.logs', 'display_name' => 'Melihat Audit Trail & Log Aktivitas', 'group' => 'System'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $perm['display_name'],
                    'group' => $perm['group'],
                ]
            );
        }

        // Roles & Permission Assignment
        $roleAdmin = Role::where('name', 'admin')->first();
        if ($roleAdmin) {
            $roleAdmin->syncPermissions(Permission::all());
        }

        $roleHr = Role::where('name', 'hr')->first();
        if ($roleHr) {
            $roleHr->syncPermissions([
                'positions.view', 'positions.manage',
                'applications.view', 'applications.manage',
                'interviews.schedule',
                'assessments.manage',
                'offerings.manage',
                'onboarding.manage', 'onboarding.verify',
                'documents.download_any',
                'system.logs',
            ]);
        }

        $roleInterviewer = Role::where('name', 'interviewer')->first();
        if ($roleInterviewer) {
            $roleInterviewer->syncPermissions([
                'applications.view',
                'interviews.conduct',
                'documents.download_assigned',
            ]);
        }

        $roleKandidat = Role::where('name', 'kandidat')->first();
        if ($roleKandidat) {
            $roleKandidat->syncPermissions([
                'positions.view',
                'assessments.take',
                'offerings.respond',
                'onboarding.submit',
                'documents.download_own',
            ]);
        }

        // Refresh cache and re-register permissions onto Gate
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(PermissionRegistrar::class)->registerPermissions(app(Gate::class));
    }
}
