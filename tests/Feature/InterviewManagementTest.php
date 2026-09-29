<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\InterviewAssessment;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InterviewManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_interviewer_can_view_interviews_index(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $response = $this->get(route('interview.interviews.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Sesi Wawancara Online');
        $response->assertSee('Jadwalkan Wawancara Baru');
    }

    public function test_admin_and_hr_can_view_interviews_index(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $responseAdmin = $this->get(route('admin.interviews.index'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('Manajemen Sesi Wawancara Online');

        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $responseHr = $this->get(route('hr.interviews.index'));
        $responseHr->assertStatus(200);
        $responseHr->assertSee('Manajemen Sesi Wawancara Online');
    }

    public function test_user_can_schedule_interview_with_unique_link(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $candidate = Candidate::first();
        $position = Position::first();

        $uniqueMeetLink = 'https://meet.google.com/abc-defg-hij';

        $response = $this->post(route('interview.interviews.store'), [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'interviewer_id' => $interviewer->id,
            'interview_type' => 'User & Technical Interview',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'platform' => 'google_meet',
            'room_link' => $uniqueMeetLink,
            'notes' => 'Fokus pengujian algoritma dan database concurrency.',
        ]);

        $response->assertRedirect(route('interview.interviews.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('interview_assessments', [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'room_link' => $uniqueMeetLink,
            'status' => 'scheduled',
        ]);
    }

    public function test_scheduling_interview_advances_application_stage_to_interview(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $candidate = Candidate::first();
        $position = Position::first();

        // Ensure candidate application exists in screening stage
        $application = CandidateApplication::updateOrCreate([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
        ], [
            'stage' => 'screening',
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        $zoomLink = 'https://zoom.us/j/9876543210?pwd=abcxyz';

        $this->post(route('admin.interviews.store'), [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'interview_type' => 'System Design & Architecture',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'platform' => 'zoom',
            'room_link' => $zoomLink,
        ]);

        $this->assertDatabaseHas('candidate_applications', [
            'id' => $application->id,
            'stage' => 'interview',
            'status' => 'in_review',
        ]);
    }

    public function test_interviewer_can_view_interview_details_and_scoring_form(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $interview = InterviewAssessment::first();

        $response = $this->get(route('interview.interviews.show', $interview->id));

        $response->assertStatus(200);
        $response->assertSee('Sesi Wawancara:');
        $response->assertSee('Formulir Penilaian');
        $response->assertSee('Keahlian Teknis');
    }

    public function test_interviewer_can_submit_evaluation_scores(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $interview = InterviewAssessment::where('status', 'scheduled')->first();

        $response = $this->post(route('interview.interviews.evaluate', $interview->id), [
            'technical_score' => 90,
            'communication_score' => 88,
            'culture_score' => 92,
            'recommendation' => 'pass',
            'notes' => 'Kandidat memiliki kemampuan arsitektur yang sangat bagus.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('interview_assessments', [
            'id' => $interview->id,
            'technical_score' => 90,
            'total_score' => 90,
            'recommendation' => 'pass',
            'status' => 'completed',
        ]);
    }

    public function test_user_can_delete_interview_session(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $interview = InterviewAssessment::create([
            'candidate_id' => Candidate::first()->id,
            'position_id' => Position::first()->id,
            'interview_type' => 'Temporary Sesi',
            'scheduled_at' => now()->addDays(3),
            'room_link' => 'https://meet.google.com/test-del-sess',
            'status' => 'scheduled',
        ]);

        $response = $this->delete(route('hr.interviews.destroy', $interview->id));

        $response->assertRedirect(route('hr.interviews.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('interview_assessments', [
            'id' => $interview->id,
        ]);
    }

    public function test_interviewer_can_view_applications_list(): void
    {
        $interviewer = User::where('email', 'interviewer@recruitment.test')->first();
        $this->actingAs($interviewer);

        $response = $this->get(route('interview.applications.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Seluruh Pelamar');
    }
}
