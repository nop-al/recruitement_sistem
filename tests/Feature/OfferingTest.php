<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Offering;
use App\Models\Onboarding;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_and_hr_can_view_offerings_index(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.offerings.index'));
        $response->assertStatus(200);
        $response->assertSee('Offering Letter');

        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $responseHr = $this->get(route('hr.offerings.index'));
        $responseHr->assertStatus(200);
        $responseHr->assertSee('Offering Letter');
    }

    public function test_hr_can_create_offering_in_draft_status(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $candidate = Candidate::first();
        $position = Position::first();
        $application = CandidateApplication::where('candidate_id', $candidate->id)->first();

        $payload = [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'candidate_application_id' => $application?->id,
            'department' => 'Technology & Engineering',
            'salary' => 18500000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(30)->toDateString(),
            'benefits' => "- Asuransi Kesehatan Keluarga\n- Tunjangan Transportasi\n- Laptop Kantor",
            'notes' => 'Draft penawaran telah disetujui direksi.',
            'status' => 'draft',
        ];

        $response = $this->post(route('hr.offerings.store'), $payload);

        $this->assertDatabaseHas('offerings', [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'department' => 'Technology & Engineering',
            'salary' => 18500000,
            'employment_type' => 'Full-time',
            'status' => 'draft',
        ]);

        $created = Offering::where('candidate_id', $candidate->id)->latest('id')->first();
        $response->assertRedirect(route('hr.offerings.show', $created->id));
    }

    public function test_hr_can_send_offering_to_candidate(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $candidate = Candidate::first();
        $position = Position::first();
        $application = CandidateApplication::where('candidate_id', $candidate->id)->first();

        $offering = Offering::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'candidate_application_id' => $application?->id,
            'department' => 'Technology & Engineering',
            'salary' => 15000000,
            'salary_offered' => 15000000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(14),
            'start_date' => Carbon::now()->addDays(14),
            'status' => 'draft',
            'benefits' => 'Standar Benefit Perusahaan',
            'notes' => 'Siap dikirim',
        ]);

        $response = $this->post(route('hr.offerings.send', $offering->id));

        $offering->refresh();
        $this->assertEquals('sent', $offering->status);
        $this->assertNotNull($offering->sent_at);

        if ($application) {
            $application->refresh();
            $this->assertEquals('offering', $application->stage);
        }

        $response->assertRedirect(route('hr.offerings.show', $offering->id));
    }

    public function test_candidate_can_view_offering_portal_and_letter(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $this->actingAs($kandidatUser);

        $offering = Offering::where('candidate_id', $candidate->id)->first();
        $this->assertNotNull($offering);

        $responseIndex = $this->get(route('kadidat.offerings.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Surat Penawaran Kerja Resmi');

        $responseShow = $this->get(route('kadidat.offerings.show', $offering->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Surat Penawaran Kerja Resmi');
        $responseShow->assertSee($offering->department);
    }

    public function test_candidate_can_accept_offering_which_initializes_onboarding(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $this->actingAs($kandidatUser);

        $position = Position::first();
        $application = CandidateApplication::where('candidate_id', $candidate->id)->first();

        // Create a sent offering for this candidate
        $offering = Offering::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'candidate_application_id' => $application?->id,
            'department' => 'Engineering',
            'salary' => 20000000,
            'salary_offered' => 20000000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(14),
            'start_date' => Carbon::now()->addDays(14),
            'status' => 'sent',
            'benefits' => 'Kompensasi Penuh',
            'sent_at' => Carbon::now(),
        ]);

        $response = $this->post(route('kadidat.offerings.respond', $offering->id), [
            'action' => 'accept',
            'notes' => 'Terima kasih, saya menerima offering ini dengan senang hati.',
        ]);

        $offering->refresh();
        $this->assertEquals('accepted', $offering->status);
        $this->assertNotNull($offering->responded_at);

        // Onboarding record should be automatically created
        $this->assertDatabaseHas('onboardings', [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'offering_id' => $offering->id,
            'status' => 'pending',
        ]);

        if ($application) {
            $application->refresh();
            $this->assertEquals('onboarding', $application->stage);
        }

        $response->assertRedirect(route('kadidat.onboarding.index'));
    }

    public function test_candidate_can_reject_offering(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $this->actingAs($kandidatUser);

        $position = Position::first();
        $application = CandidateApplication::where('candidate_id', $candidate->id)->first();

        $offering = Offering::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'candidate_application_id' => $application?->id,
            'department' => 'Engineering',
            'salary' => 12000000,
            'salary_offered' => 12000000,
            'employment_type' => 'Full-time',
            'join_date' => Carbon::now()->addDays(14),
            'start_date' => Carbon::now()->addDays(14),
            'status' => 'sent',
            'sent_at' => Carbon::now(),
        ]);

        $response = $this->post(route('kadidat.offerings.respond', $offering->id), [
            'action' => 'reject',
            'notes' => 'Terima kasih atas tawarannya, namun saya memilih opsi lain.',
        ]);

        $offering->refresh();
        $this->assertEquals('rejected', $offering->status);

        if ($application) {
            $application->refresh();
            $this->assertEquals('rejected', $application->status);
        }

        $response->assertRedirect(route('kadidat.offerings.index'));
    }

    public function test_admin_and_hr_can_access_offering_print_view(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $offering = Offering::first();

        $response = $this->get(route('offerings.print', $offering->id));

        $response->assertStatus(200);
        $response->assertSee('SURAT PENAWARAN KERJA RESMI');
        $response->assertSee('Cetak / Simpan PDF (Ctrl+P)');
        $response->assertSee('Gaji Pokok yang Ditawarkan');
    }

    public function test_candidate_can_access_own_offering_print_view(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $offering = Offering::where('candidate_id', $candidateUser->candidate->id)->first();

        $response = $this->get(route('kadidat.offerings.print', $offering->id));

        $response->assertStatus(200);
        $response->assertSee('SURAT PENAWARAN KERJA RESMI');
        $response->assertSee($candidateUser->candidate->full_name);
    }

    public function test_candidate_cannot_access_other_candidate_offering_print_view(): void
    {
        $candidateUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($candidateUser);

        $otherOffering = Offering::where('candidate_id', '!=', $candidateUser->candidate->id)->first();

        $response = $this->get(route('offerings.print', $otherOffering->id));

        $response->assertStatus(403);
    }
}
