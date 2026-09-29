<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExcelAndOfflineInterviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_schedule_offline_interview_with_physical_location(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $candidate = Candidate::first();
        $position = Position::first();

        $response = $this->post(route('admin.interviews.store'), [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'interviewer_id' => $admin->id,
            'interview_type' => 'Wawancara User & HR',
            'interview_method' => 'offline',
            'location' => 'Gedung Recruitment Lt. 3, Ruang Rapat Merak, Jakarta Selatan',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Harap membawa KTP dan berkas portofolio asli.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('interview_assessments', [
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'interview_method' => 'offline',
            'location' => 'Gedung Recruitment Lt. 3, Ruang Rapat Merak, Jakarta Selatan',
        ]);
    }

    public function test_admin_can_export_applications_to_xlsx(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.applications.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_download_mass_status_template(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.applications.template'));

        $response->assertStatus(200);
        $this->assertStringContainsString('Template_Upload_Massal_Status_Pelamar.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_admin_can_upload_mass_status_using_xlsx_template(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $app = CandidateApplication::with('candidate.user')->first();
        $candidateEmail = $app->candidate->user->email;

        // Path to the actual template file
        $templatePath = public_path('upload_mass_status.xlsx');
        $this->assertFileExists($templatePath);

        // Make an uploaded file copy with the candidate's actual email
        $tempCopy = tempnam(sys_get_temp_dir(), 'xlsx_test_').'.xlsx';
        copy($templatePath, $tempCopy);

        // We can test upload directly with the template file
        $file = new UploadedFile($tempCopy, 'upload_mass_status.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post(route('admin.applications.upload-mass'), [
            'file_excel' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        if (file_exists($tempCopy)) {
            unlink($tempCopy);
        }
    }
}
