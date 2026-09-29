<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\CandidateProfile;
use App\Models\Onboarding;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_spatie_roles_and_permissions_are_properly_assigned(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $kandidat = User::where('email', 'kandidat@recruitment.test')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->can('documents.download_any'));
        $this->assertTrue($admin->can('system.config'));

        $this->assertNotNull($hr);
        $this->assertTrue($hr->hasRole('hr'));
        $this->assertTrue($hr->can('documents.download_any'));
        $this->assertTrue($hr->can('positions.view'));

        $this->assertNotNull($kandidat);
        $this->assertTrue($kandidat->hasRole('kandidat'));
        $this->assertTrue($kandidat->can('documents.download_own'));
        $this->assertFalse($kandidat->can('system.config'));
    }

    public function test_unauthenticated_user_cannot_access_sensitive_documents(): void
    {
        $candidate = Candidate::first();
        $this->assertNotNull($candidate);

        // Web request gets redirected to login
        $response = $this->get(route('documents.resume', $candidate->id));
        $response->assertRedirect(route('login'));

        // JSON / API request gets 401 Unauthorized
        $responseJson = $this->getJson(route('documents.resume', $candidate->id));
        $responseJson->assertStatus(401);
    }

    public function test_candidate_cannot_access_another_candidates_resume(): void
    {
        // Candidate 1
        $user1 = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate1 = Candidate::where('user_id', $user1->id)->first();

        // Candidate 2
        $roleKandidat = Role::where('name', 'kandidat')->first();
        $user2 = User::create([
            'name' => 'Kandidat Dua',
            'email' => 'kandidat2@recruitment.test',
            'password' => Hash::make('password123'),
            'role_id' => $roleKandidat->id,
        ]);
        $candidate2 = Candidate::create([
            'user_id' => $user2->id,
            'first_name' => 'Kandidat',
            'last_name' => 'Dua',
            'email' => 'kandidat2@recruitment.test',
            'phone_number' => '081299998888',
        ]);

        Storage::fake('local');
        $filePath = 'resumes/cv_candidate_2.pdf';
        Storage::disk('local')->put($filePath, 'Fake resume content');

        CandidateProfile::create([
            'candidate_id' => $candidate2->id,
            'resume_path' => $filePath,
        ]);

        // Acting as Candidate 1 trying to download Candidate 2's CV
        $this->actingAs($user1);
        $response = $this->get(route('documents.resume', $candidate2->id));

        $response->assertStatus(403);

        // Verify unauthorized access attempt was logged in audit trail
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'UNAUTHORIZED_DOCUMENT_ACCESS',
            'user_id' => $user1->id,
        ]);
    }

    public function test_candidate_can_download_their_own_resume(): void
    {
        $user = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $user->id)->first();

        Storage::fake('local');
        $filePath = 'resumes/my_resume.pdf';
        Storage::disk('local')->put($filePath, 'PDF file content for resume');

        $candidate->profile()->updateOrCreate(
            ['candidate_id' => $candidate->id],
            ['resume_path' => $filePath]
        );

        $this->actingAs($user);
        $response = $this->get(route('documents.resume', $candidate->id));

        $response->assertStatus(200);

        // Verify activity log recorded the download
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'DOWNLOAD_DOCUMENT',
            'user_id' => $user->id,
        ]);
    }

    public function test_hr_or_admin_can_download_any_candidate_document(): void
    {
        $hr = User::where('email', 'hr@recruitment.test')->first();
        $candidate = Candidate::first();

        Storage::fake('local');
        $filePath = 'resumes/candidate_resume.pdf';
        Storage::disk('local')->put($filePath, 'Candidate resume PDF binary content');

        $candidate->profile()->updateOrCreate(
            ['candidate_id' => $candidate->id],
            ['resume_path' => $filePath]
        );

        $this->actingAs($hr);
        $response = $this->get(route('documents.resume', $candidate->id));

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'DOWNLOAD_DOCUMENT',
            'user_id' => $hr->id,
        ]);
    }

    public function test_onboarding_documents_are_secured_by_authorization(): void
    {
        $user1 = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate1 = Candidate::where('user_id', $user1->id)->first();
        $position = Position::first();

        Storage::fake('local');
        $ktpPath = 'onboarding/ktp_secure.pdf';
        Storage::disk('local')->put($ktpPath, 'KTP secret file data');

        $onboarding = Onboarding::create([
            'candidate_id' => $candidate1->id,
            'position_id' => $position->id,
            'status' => 'in_progress',
            'checklist_percentage' => 20,
            'ktp_file' => $ktpPath,
        ]);

        // 1. Unauthenticated -> Redirect to login
        $responseUnauth = $this->get(route('documents.onboarding', [$onboarding->id, 'ktp']));
        $responseUnauth->assertRedirect(route('login'));

        // 2. Owner candidate -> 200
        $this->actingAs($user1);
        $responseOwner = $this->get(route('documents.onboarding', [$onboarding->id, 'ktp']));
        $responseOwner->assertStatus(200);

        // 3. Different candidate -> 403
        $roleKandidat = Role::where('name', 'kandidat')->first();
        $user2 = User::create([
            'name' => 'Kandidat Lain',
            'email' => 'kandidat_lain@recruitment.test',
            'password' => Hash::make('password123'),
            'role_id' => $roleKandidat->id,
        ]);

        $this->actingAs($user2);
        $responseOther = $this->get(route('documents.onboarding', [$onboarding->id, 'ktp']));
        $responseOther->assertStatus(403);
    }

    public function test_failed_login_records_activity_log(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@recruitment.test',
            'password' => 'wrongpassword123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'LOGIN_FAILED',
            'module' => 'Authentication',
        ]);
    }

    public function test_input_sanitization_middleware_strips_dangerous_html_tags(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($kandidatUser);

        $payload = [
            'first_name' => 'John <script>alert("XSS")</script>',
            'last_name' => 'Doe<b>Bold</b>',
            'phone_number' => '08123456789',
            'summary' => '<b>Senior</b> <script>steal()</script>Developer',
        ];

        $this->put(route('kadidat.profile.update'), $payload);

        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $this->assertNotNull($candidate);
        $this->assertEquals('John alert("XSS")', $candidate->first_name);
        $this->assertEquals('DoeBold', $candidate->last_name);
        $this->assertStringNotContainsString('<script>', $candidate->profile->summary);
        $this->assertStringNotContainsString('<b>', $candidate->profile->summary);
    }

    public function test_login_endpoint_rate_limiting(): void
    {
        RateLimiter::clear('login:127.0.0.1');

        // Send 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), [
                'email' => 'admin@recruitment.test',
                'password' => 'wrongpassword',
            ]);
        }

        // 6th attempt should hit throttle (429)
        $response = $this->post(route('login.post'), [
            'email' => 'admin@recruitment.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }
}
