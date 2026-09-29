<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\CandidateEducation;
use App\Models\CandidateExperience;
use App\Models\CandidateSkill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateProfileAndAdminApplicationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_access_applications_index_page(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Seluruh Pelamar');
        $response->assertSee('Daftar Seluruh Pelamar Kerja');
        $response->assertSee('Ahmad Fauzi');
    }

    public function test_admin_can_filter_applications_by_stage_and_search(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.applications.index', [
            'stage' => 'interview',
            'search' => 'Ahmad',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
    }

    public function test_admin_can_view_application_detail(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $application = CandidateApplication::first();

        $response = $this->get(route('admin.applications.show', $application->id));

        $response->assertStatus(200);
        $response->assertSee('Detail Lamaran');
        $response->assertSee($application->candidate->first_name);
    }

    public function test_candidate_cannot_access_admin_applications(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('admin.applications.index'));
        $response->assertRedirect(route('kadidat.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_candidate_can_access_profile_edit_form(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->get(route('kadidat.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Kelengkapan Profil');
        $response->assertSee('Ahmad');
        $response->assertSee('Berkas CV');
    }

    public function test_candidate_can_update_profile_and_upload_cv(): void
    {
        Storage::fake('local');

        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $file = UploadedFile::fake()->create('my_resume.pdf', 1500, 'application/pdf');

        $response = $this->put(route('kadidat.profile.update'), [
            'first_name' => 'Ahmad',
            'last_name' => 'Fauzi Pratama',
            'phone_number' => '081299887766',
            'city' => 'Jakarta Barat',
            'province' => 'DKI Jakarta',
            'summary' => 'Software engineer berpengalaman tinggi.',
            'resume' => $file,
        ]);

        $response->assertRedirect(route('kadidat.profile.edit'));
        $response->assertSessionHas('success');

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $this->assertEquals('Fauzi Pratama', $candidate->last_name);
        $this->assertEquals('081299887766', $candidate->phone_number);
        $this->assertNotNull($candidate->profile->resume_path);

        Storage::disk('local')->assertExists($candidate->profile->resume_path);
    }

    public function test_candidate_can_manage_education(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->post(route('kadidat.profile.education.store'), [
            'institution_name' => 'Institut Teknologi Bandung',
            'degree' => 'Magister (S2)',
            'field_of_study' => 'Sains Komputasi',
            'gpa' => '3.90',
            'start_date' => '2021-08-01',
            'end_date' => '2023-08-20',
        ]);

        $response->assertRedirect(route('kadidat.profile.edit'));
        $response->assertSessionHas('success');

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $education = CandidateEducation::where('candidate_id', $candidate->id)
            ->where('institution_name', 'Institut Teknologi Bandung')
            ->first();

        $this->assertNotNull($education);
        $this->assertEquals('Magister (S2)', $education->degree);

        // Delete education
        $delResponse = $this->delete(route('kadidat.profile.education.destroy', $education->id));
        $delResponse->assertRedirect(route('kadidat.profile.edit'));
        $this->assertDatabaseMissing('candidate_education', ['id' => $education->id]);
    }

    public function test_candidate_can_manage_experience(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->post(route('kadidat.profile.experience.store'), [
            'company_name' => 'PT Unicorn Teknologi Digital',
            'position' => 'Senior Backend Engineer',
            'description' => 'Mengembangkan microservices performa tinggi',
            'start_date' => '2023-01-01',
            'is_current' => '1',
        ]);

        $response->assertRedirect(route('kadidat.profile.edit'));
        $response->assertSessionHas('success');

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $experience = CandidateExperience::where('candidate_id', $candidate->id)
            ->where('company_name', 'PT Unicorn Teknologi Digital')
            ->first();

        $this->assertNotNull($experience);
        $this->assertTrue($experience->is_current);

        // Delete experience
        $delResponse = $this->delete(route('kadidat.profile.experience.destroy', $experience->id));
        $delResponse->assertRedirect(route('kadidat.profile.edit'));
        $this->assertDatabaseMissing('candidate_experiences', ['id' => $experience->id]);
    }

    public function test_candidate_can_manage_skill(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $response = $this->post(route('kadidat.profile.skill.store'), [
            'skill_name' => 'Docker & Kubernetes',
            'proficiency_level' => 'advanced',
            'years_of_experience' => 3,
        ]);

        $response->assertRedirect(route('kadidat.profile.edit'));
        $response->assertSessionHas('success');

        $candidate = Candidate::where('user_id', $candidateUser->id)->first();
        $skill = CandidateSkill::where('candidate_id', $candidate->id)
            ->where('skill_name', 'Docker & Kubernetes')
            ->first();

        $this->assertNotNull($skill);

        // Delete skill
        $delResponse = $this->delete(route('kadidat.profile.skill.destroy', $skill->id));
        $delResponse->assertRedirect(route('kadidat.profile.edit'));
        $this->assertDatabaseMissing('candidate_skills', ['id' => $skill->id]);
    }
}
