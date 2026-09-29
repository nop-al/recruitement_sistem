<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminConfigTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_config_page(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.config.index'));

        $response->assertStatus(200);
        $response->assertSee('Konfigurasi');
        $response->assertSee('Pengaturan Pusat');
        $response->assertSee('Identitas');
        $response->assertSee('Logo Sistem');
        $response->assertSee('Departemen');
        $response->assertSee('Divisi');
        $response->assertSee('Backup');
        $response->assertSee('Restore Sistem');
        $response->assertSee('Pembersihan Cache');
    }

    public function test_non_admin_cannot_view_config_page(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('admin.config.index'));

        $response->assertRedirect(route('kadidat.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_can_update_system_identity(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->put(route('admin.config.identity.update'), [
            'app_name' => 'RecruitPro Enterprise',
            'app_version' => '3.0.0',
            'app_tagline' => 'Platform Rekrutmen Modern',
            'company_name' => 'PT Karya Teknologi Gemilang',
            'footer_text' => 'RecruitPro Enterprise • Hak Cipta Dilindungi',
        ]);

        $response->assertRedirect(route('admin.config.index', ['tab' => 'identity']));
        $response->assertSessionHas('success');

        $this->assertEquals('RecruitPro Enterprise', SystemSetting::get('app_name'));
        $this->assertEquals('3.0.0', SystemSetting::get('app_version'));
    }

    public function test_admin_can_store_and_destroy_department(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        // 1. Store department
        $response = $this->post(route('admin.config.departments.store'), [
            'name' => 'Quality Assurance & Automation',
            'code' => 'QA-AUTO',
            'description' => 'Divisi pengujian software dan automasi kualitas sistem',
        ]);

        $response->assertRedirect(route('admin.config.index', ['tab' => 'departments']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('departments', [
            'name' => 'Quality Assurance & Automation',
            'code' => 'QA-AUTO',
        ]);

        $dept = Department::where('name', 'Quality Assurance & Automation')->first();

        // 2. Destroy department
        $deleteResponse = $this->delete(route('admin.config.departments.destroy', $dept->id));
        $deleteResponse->assertRedirect(route('admin.config.index', ['tab' => 'departments']));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('departments', [
            'id' => $dept->id,
        ]);
    }

    public function test_admin_can_trigger_database_backup(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.config.backup.create'));

        $response->assertRedirect(route('admin.config.index', ['tab' => 'backup']));
        $response->assertSessionHas('success');
    }

    public function test_admin_can_clear_cache(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.config.cache.clear'), [
            'type' => 'all',
        ]);

        $response->assertRedirect(route('admin.config.index', ['tab' => 'cache']));
        $response->assertSessionHas('success');
    }
}
