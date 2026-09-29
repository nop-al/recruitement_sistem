<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Onboarding;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_and_hr_can_view_onboardings_index(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.onboardings.index'));
        $response->assertStatus(200);
        $response->assertSee('Onboarding');

        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $responseHr = $this->get(route('hr.onboardings.index'));
        $responseHr->assertStatus(200);
        $responseHr->assertSee('Onboarding');
    }

    public function test_candidate_can_view_onboarding_portal(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $position = Position::first();

        // Create onboarding for this candidate
        $onboarding = Onboarding::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'status' => 'in_progress',
            'checklist_percentage' => 25,
            'ktp_number' => '3171011204970005',
            'ktp_verified' => true,
        ]);

        $this->actingAs($kandidatUser);

        $response = $this->get(route('kadidat.onboarding.index'));
        $response->assertStatus(200);
        $response->assertSee('Portal Onboarding');
        $response->assertSee('Kartu Tanda Penduduk (KTP)');
        $response->assertSee('Nomor Pokok Wajib Pajak (NPWP)');
        $response->assertSee('BPJS (Kesehatan / Ketenagakerjaan)');
        $response->assertSee('Rekening Bank untuk Payroll Gaji');
        $response->assertSee('Ijazah');
        $response->assertSee('Pas Foto Formal untuk ID Card');
        $response->assertSee('Kontrak Kerja (Bertandatangan)');
        $response->assertSee('Emergency Contact (Kontak Darurat)');
    }

    public function test_candidate_can_submit_onboarding_checklist_data_and_files(): void
    {
        Storage::fake('local');

        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $position = Position::first();

        $onboarding = Onboarding::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'status' => 'pending',
            'checklist_percentage' => 0,
        ]);

        $this->actingAs($kandidatUser);

        $ktpFile = UploadedFile::fake()->create('ktp_ahmad.pdf', 200, 'application/pdf');
        $pasFoto = UploadedFile::fake()->image('pas_foto.jpg');

        $payload = [
            'ktp_number' => '3171011204970005',
            'ktp_file' => $ktpFile,
            'npwp_number' => '98.765.432.1-012.000',
            'bpjs_number' => '000192837465',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account_number' => '8839201920',
            'bank_account_holder' => 'Ahmad Fauzi',
            'pas_foto_file' => $pasFoto,
            'emergency_contact_name' => 'Siti Khadijah',
            'emergency_contact_relation' => 'Ibu Kandung',
            'emergency_contact_phone' => '081234567890',
        ];

        $response = $this->post(route('kadidat.onboarding.submit', $onboarding->id), $payload);

        $response->assertRedirect(route('kadidat.onboarding.index'));

        $onboarding->refresh();
        $this->assertEquals('3171011204970005', $onboarding->ktp_number);
        $this->assertEquals('Bank Central Asia (BCA)', $onboarding->bank_name);
        $this->assertEquals('8839201920', $onboarding->bank_account_number);
        $this->assertEquals('Siti Khadijah', $onboarding->emergency_contact_name);
        $this->assertNotNull($onboarding->ktp_file);
        $this->assertNotNull($onboarding->pas_foto_file);

        // Files stored in secure local private storage
        Storage::disk('local')->assertExists($onboarding->ktp_file);
        Storage::disk('local')->assertExists($onboarding->pas_foto_file);

        // Checklist percentage should be greater than 0
        $this->assertGreaterThan(0, $onboarding->checklist_percentage);
    }

    public function test_admin_or_hr_can_verify_individual_checklist_items(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $onboarding = Onboarding::first();
        $this->assertNotNull($onboarding);

        $response = $this->post(route('hr.onboardings.verify', $onboarding->id), [
            'item' => 'ktp',
            'verified' => 1,
        ]);

        $response->assertRedirect(route('hr.onboardings.show', $onboarding->id));

        $onboarding->refresh();
        $this->assertTrue($onboarding->ktp_verified);
    }

    public function test_admin_or_hr_can_complete_onboarding_and_hire_candidate(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $candidate = Candidate::first();
        $position = Position::first();
        $application = CandidateApplication::where('candidate_id', $candidate->id)->first();

        $onboarding = Onboarding::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'candidate_application_id' => $application?->id,
            'status' => 'in_progress',
            'checklist_percentage' => 75,
            'ktp_verified' => true,
            'npwp_verified' => true,
            'bpjs_verified' => true,
            'bank_verified' => true,
        ]);

        $response = $this->put(route('hr.onboardings.status.update', $onboarding->id), [
            'status' => 'completed',
            'notes' => 'Semua berkas dan dokumen kontrak telah diverifikasi lengkap.',
        ]);

        $response->assertRedirect(route('hr.onboardings.show', $onboarding->id));

        $onboarding->refresh();
        $this->assertEquals('completed', $onboarding->status);
        $this->assertEquals(100, $onboarding->checklist_percentage);
        $this->assertNotNull($onboarding->completed_at);

        if ($application) {
            $application->refresh();
            $this->assertEquals('hired', $application->stage);
            $this->assertEquals('passed', $application->status);
        }
    }
}
