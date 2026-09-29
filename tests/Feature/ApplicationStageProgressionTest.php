<?php

namespace Tests\Feature;

use App\Models\CandidateApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationStageProgressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_hr_can_view_application_detail_with_screening_controls(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $screeningApp = CandidateApplication::where('stage', 'screening')->first();

        $response = $this->get(route('hr.applications.show', $screeningApp->id));

        $response->assertStatus(200);
        $response->assertSee('Tahap 1: Screening Dokumen');
        $response->assertSee('Approve CV');
        $response->assertSee('Tolak Berkas CV');
    }

    public function test_hr_can_approve_cv_from_screening_to_assessment(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $screeningApp = CandidateApplication::where('stage', 'screening')->first();

        $response = $this->post(route('hr.applications.update-stage', $screeningApp->id), [
            'stage' => 'assessment',
            'status' => 'in_review',
            'hr_notes' => 'Kualifikasi CV sangat baik, disetujui untuk lanjut ke ujian psikotes dan logika.',
        ]);

        $response->assertRedirect();
        $screeningApp->refresh();

        $this->assertEquals('assessment', $screeningApp->stage);
        $this->assertEquals('in_review', $screeningApp->status);
        $this->assertStringContainsString('Kualifikasi CV sangat baik', $screeningApp->hr_notes);
    }

    public function test_hr_can_reject_cv_at_screening(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $screeningApp = CandidateApplication::where('stage', 'screening')->first();

        $response = $this->post(route('hr.applications.update-stage', $screeningApp->id), [
            'stage' => 'rejected',
            'status' => 'rejected',
            'hr_notes' => 'Pengalaman belum memenuhi kriteria minimal posisi.',
        ]);

        $response->assertRedirect();
        $screeningApp->refresh();

        $this->assertEquals('rejected', $screeningApp->stage);
        $this->assertEquals('rejected', $screeningApp->status);
    }

    public function test_hr_can_approve_assessment_score_and_advance_to_interview_with_redirect(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $assessmentApp = CandidateApplication::where('stage', 'assessment')->first();

        $response = $this->post(route('hr.applications.update-stage', $assessmentApp->id), [
            'stage' => 'interview',
            'status' => 'in_review',
            'hr_notes' => 'Nilai asesmen memuaskan. Lolos ke wawancara teknis.',
            'schedule_interview' => 1,
        ]);

        $assessmentApp->refresh();
        $this->assertEquals('interview', $assessmentApp->stage);

        $response->assertRedirect(route('hr.interviews.create', [
            'candidate_id' => $assessmentApp->candidate_id,
            'position_id' => $assessmentApp->position_id,
        ]));
    }

    public function test_hr_can_advance_interview_to_offering_with_redirect(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $interviewApp = CandidateApplication::where('stage', 'interview')->first();

        $response = $this->post(route('hr.applications.update-stage', $interviewApp->id), [
            'stage' => 'offering',
            'status' => 'in_review',
            'hr_notes' => 'Kandidat lulus interview. Siapkan surat penawaran.',
            'create_offering' => 1,
        ]);

        $interviewApp->refresh();
        $this->assertEquals('offering', $interviewApp->stage);

        $response->assertRedirect(route('hr.offerings.create', [
            'candidate_id' => $interviewApp->candidate_id,
            'position_id' => $interviewApp->position_id,
            'application_id' => $interviewApp->id,
        ]));
    }

    public function test_hr_can_advance_offering_to_onboarding(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $offeringApp = CandidateApplication::where('stage', 'offering')->first();

        $response = $this->post(route('hr.applications.update-stage', $offeringApp->id), [
            'stage' => 'onboarding',
            'status' => 'passed',
            'hr_notes' => 'Offering diterima oleh kandidat. Masuk ke onboarding.',
        ]);

        $response->assertRedirect();
        $offeringApp->refresh();

        $this->assertEquals('onboarding', $offeringApp->stage);
        $this->assertEquals('passed', $offeringApp->status);
    }

    public function test_hr_can_finalize_onboarding_to_hired(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $onboardingApp = CandidateApplication::where('stage', 'onboarding')->first();

        $response = $this->post(route('hr.applications.update-stage', $onboardingApp->id), [
            'stage' => 'hired',
            'status' => 'passed',
            'hr_notes' => 'Semua berkas onboarding lengkap dan terverifikasi. Resmi karyawan.',
        ]);

        $response->assertRedirect();
        $onboardingApp->refresh();

        $this->assertEquals('hired', $onboardingApp->stage);
        $this->assertEquals('passed', $onboardingApp->status);
    }

    public function test_candidate_cannot_update_application_stage(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $app = CandidateApplication::where('candidate_id', $candidateUser->candidate->id)->first();

        $response = $this->post(route('hr.applications.update-stage', $app->id), [
            'stage' => 'hired',
            'status' => 'passed',
        ]);

        $response->assertRedirect(route('kadidat.dashboard'));
    }
}
