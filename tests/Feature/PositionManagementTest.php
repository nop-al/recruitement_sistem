<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_view_positions_index(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.positions.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Lowongan Kerja');
        $response->assertSee('Senior Full-Stack Developer');
    }

    public function test_hr_can_view_positions_index(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $response = $this->get(route('hr.positions.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Lowongan Kerja');
        $response->assertSee('HR Recruiter');
    }

    public function test_candidate_can_view_positions_index(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('kadidat.positions.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Lowongan Kerja');
        $response->assertSee('Senior Full-Stack Developer');
    }

    public function test_user_can_filter_positions_by_department(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('kadidat.positions.index', [
            'department' => 'Technology & Engineering',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Senior Full-Stack Developer');
    }

    public function test_admin_can_view_position_details_with_applicant_table(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $pos = Position::first();

        $response = $this->get(route('admin.positions.show', $pos->id));

        $response->assertStatus(200);
        $response->assertSee($pos->title);
        $response->assertSee('Deskripsi Pekerjaan');
        $response->assertSee('Daftar Pelamar untuk Posisi Ini');
    }

    public function test_candidate_can_view_position_details_and_apply_form(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        // Find position that candidate hasn't applied to yet
        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $appliedIds = $candidate->applications()->pluck('position_id')->toArray();
        $unappliedPos = Position::whereNotIn('id', $appliedIds)->first();

        $response = $this->get(route('kadidat.positions.show', $unappliedPos->id));

        $response->assertStatus(200);
        $response->assertSee($unappliedPos->title);
        $response->assertSee('Formulir Pengajuan Lamaran Pekerjaan');
    }

    public function test_candidate_can_apply_for_job(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $appliedIds = $candidate->applications()->pluck('position_id')->toArray();
        $unappliedPos = Position::whereNotIn('id', $appliedIds)->first();

        $response = $this->post(route('kadidat.positions.apply', $unappliedPos->id), [
            'cover_letter' => 'Saya sangat tertarik dengan lowongan ini dan memiliki kemampuan yang sesuai.',
        ]);

        $response->assertRedirect(route('kadidat.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('candidate_applications', [
            'candidate_id' => $candidate->id,
            'position_id' => $unappliedPos->id,
            'stage' => 'screening',
            'status' => 'pending',
        ]);
    }

    public function test_candidate_cannot_apply_twice_for_same_position(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $existingApp = CandidateApplication::where('candidate_id', $candidate->id)->first();

        $response = $this->post(route('kadidat.positions.apply', $existingApp->position_id), [
            'cover_letter' => 'Mencoba melamar lagi.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('info');
    }

    public function test_admin_can_delete_position(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $position = Position::create([
            'title' => 'Temporary Position To Delete',
            'department' => 'Operations',
            'location' => 'Jakarta, ID',
            'type' => 'full-time',
            'experience_level' => 'mid',
            'status' => 'active',
            'description' => 'Temporary test description',
        ]);

        $response = $this->delete(route('admin.positions.destroy', $position->id));

        $response->assertRedirect(route('admin.positions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('positions', [
            'id' => $position->id,
        ]);
    }

    public function test_hr_can_delete_position(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $position = Position::create([
            'title' => 'HR Temporary Position',
            'department' => 'HR',
            'location' => 'Bandung, ID',
            'type' => 'contract',
            'experience_level' => 'entry',
            'status' => 'draft',
            'description' => 'Temporary HR description',
        ]);

        $response = $this->delete(route('hr.positions.destroy', $position->id));

        $response->assertRedirect(route('hr.positions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('positions', [
            'id' => $position->id,
        ]);
    }

    public function test_admin_can_view_edit_position_page(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $position = Position::first();

        $response = $this->get(route('admin.positions.edit', $position->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Lowongan');
        $response->assertSee($position->title);
    }

    public function test_admin_can_update_position(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $position = Position::first();

        $response = $this->put(route('admin.positions.update', $position->id), [
            'title' => 'Updated Tech Lead Role',
            'department' => $position->department,
            'location' => 'Jakarta Selatan',
            'type' => 'Full-time',
            'experience_level' => 'Senior Level (4+ Tahun)',
            'salary_min' => 15000000,
            'salary_max' => 25000000,
            'quota' => 3,
            'deadline' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'active',
            'description' => 'Updated description text for testing',
            'requirements' => 'Updated requirements text',
        ]);

        $response->assertRedirect(route('admin.positions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('positions', [
            'id' => $position->id,
            'title' => 'Updated Tech Lead Role',
            'quota' => 3,
        ]);
    }

    public function test_hr_can_view_edit_position_page(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $position = Position::first();

        $response = $this->get(route('hr.positions.edit', $position->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Lowongan');
        $response->assertSee($position->title);
    }

    public function test_candidate_navbar_displays_photo_and_application_id(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('kadidat.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('ID Lamaran');
        $response->assertSee('CADINEW-');
        $response->assertSee('Ahmad Fauzi');
    }
}
