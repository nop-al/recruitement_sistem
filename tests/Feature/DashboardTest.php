<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_can_view_home_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_authenticated_user_is_redirected_to_their_dashboard(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get('/');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_dashboard_can_be_rendered_by_admin(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Kontrol & Manajemen Sistem');
        $response->assertSee('Total Pengguna');
    }

    public function test_hr_dashboard_can_be_rendered_by_hr(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $response = $this->get(route('hr.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pipeline Rekrutmen & Manajemen Pelamar');
        $response->assertSee('Funnel Jalur Rekrutmen');
    }

    public function test_interview_dashboard_can_be_rendered_by_interviewer(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $response = $this->get(route('interview.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Wawancara');
        $response->assertSee('Jadwal Sesi Wawancara Mendatang');
    }

    public function test_kadidat_dashboard_can_be_rendered_by_candidate(): void
    {
        $candidate = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidate);

        $response = $this->get(route('kadidat.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Pelacak Status Lamaran Kerja Aktif');
        $response->assertSee('Kelengkapan Profil CV');
    }

    public function test_candidate_cannot_access_admin_dashboard(): void
    {
        $candidate = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidate);

        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('kadidat.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_access_dashboards_directly(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('hr.dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('kadidat.dashboard'));
        $response->assertRedirect(route('login'));
    }
}
