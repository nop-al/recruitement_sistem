<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_access_create_user_form(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Pengguna Baru');
        $response->assertSee('Informasi Profil');
    }

    public function test_admin_can_store_new_hr_user(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $hrRole = Role::where('name', 'hr')->first();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Doni Recruiter',
            'email' => 'doni.recruiter@example.test',
            'role_id' => $hrRole->id,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Doni Recruiter',
            'email' => 'doni.recruiter@example.test',
            'role_id' => $hrRole->id,
        ]);
    }

    public function test_admin_can_store_candidate_user_and_creates_candidate_profile(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $candidateRole = Role::where('name', 'kandidat')->first();

        $response = $this->post(route('admin.users.store'), [
            'name' => 'Sinta Nurhaliza',
            'email' => 'sinta.nurhaliza@example.test',
            'role_id' => $candidateRole->id,
            'phone_number' => '081299887766',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $user = User::where('email', 'sinta.nurhaliza@example.test')->first();
        $this->assertNotNull($user);

        $candidate = Candidate::where('user_id', $user->id)->first();
        $this->assertNotNull($candidate);
        $this->assertEquals('Sinta', $candidate->first_name);

        $profile = CandidateProfile::where('candidate_id', $candidate->id)->first();
        $this->assertNotNull($profile);
    }

    public function test_non_admin_cannot_access_create_user_page(): void
    {
        $kandidat = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($kandidat);

        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('kadidat.dashboard'));

        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('hr.dashboard'));
    }

    public function test_admin_can_access_create_position_form(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.positions.create'));

        $response->assertStatus(200);
        $response->assertSee('Buka Lowongan Pekerjaan Baru');
        $response->assertSee('Informasi Utama Pekerjaan');
    }

    public function test_hr_can_access_create_position_form(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $response = $this->get(route('hr.positions.create'));

        $response->assertStatus(200);
        $response->assertSee('Buka Lowongan Pekerjaan Baru');
    }

    public function test_admin_can_store_new_position(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->post(route('admin.positions.store'), [
            'title' => 'DevOps Cloud Architect',
            'department' => 'Infrastructure & Cloud',
            'location' => 'Jakarta (Remote OK)',
            'type' => 'Full-time',
            'experience_level' => 'Senior Level',
            'salary_min' => 20000000,
            'salary_max' => 30000000,
            'quota' => 2,
            'status' => 'active',
            'description' => 'Mengelola infrastruktur Kubernetes dan AWS.',
            'requirements' => 'Docker, K8s, Terraform, CI/CD pipelines.',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('positions', [
            'title' => 'DevOps Cloud Architect',
            'department' => 'Infrastructure & Cloud',
        ]);
    }

    public function test_hr_can_store_new_position(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $response = $this->post(route('hr.positions.store'), [
            'title' => 'QA Automation Specialist',
            'department' => 'Quality Assurance',
            'location' => 'Bandung (Hybrid)',
            'type' => 'Full-time',
            'experience_level' => 'Mid Level',
            'salary_min' => 11000000,
            'salary_max' => 16000000,
            'quota' => 1,
            'status' => 'active',
            'description' => 'Membuat skrip automation test dengan Cypress dan Playwright.',
            'requirements' => 'JavaScript/TypeScript, Cypress, Playwright, API Testing.',
        ]);

        $response->assertRedirect(route('hr.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('positions', [
            'title' => 'QA Automation Specialist',
            'department' => 'Quality Assurance',
        ]);
    }

    public function test_sidebar_contains_menu_and_topbar_has_no_navigation(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Sidebar navigation links exist
        $response->assertSee(route('admin.users.create'));
        $response->assertSee(route('admin.positions.create'));
        $response->assertSee('Tambah Pengguna (User)');
        $response->assertSee('Buat Lowongan Kerja');

        // Verify top bar doesn't have role-switching or navigation
        $content = $response->getContent();
        $this->assertStringNotContainsString('Ganti Role', $content);
        $this->assertStringNotContainsString('Switch Role', $content);
    }
}
