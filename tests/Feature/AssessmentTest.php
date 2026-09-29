<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\Candidate;
use App\Models\CandidateTestResult;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_and_hr_can_view_assessments_index(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.assessments.index'));
        $response->assertStatus(200);
        $response->assertSee('Online Assessment');

        $hr = User::where('email', 'hr@recruitment.test')->first();
        $this->actingAs($hr);

        $responseHr = $this->get(route('hr.assessments.index'));
        $responseHr->assertStatus(200);
        $responseHr->assertSee('Online Assessment');
    }

    public function test_admin_can_create_new_assessment(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $position = Position::first();

        $payload = [
            'title' => 'Tes Kemampuan Psikologi & Sikap Kerja',
            'test_type' => 'Psychological Test',
            'position_id' => $position->id,
            'description' => 'Evaluasi stabilitas emosi, konsistensi kerja, dan orientasi pencapaian target.',
            'duration' => 45,
            'passing_score' => 75,
            'is_active' => '1',
        ];

        $response = $this->post(route('admin.assessments.store'), $payload);

        $this->assertDatabaseHas('assessments', [
            'title' => 'Tes Kemampuan Psikologi & Sikap Kerja',
            'test_type' => 'Psychological Test',
            'duration' => 45,
            'passing_score' => 75,
        ]);

        $created = Assessment::where('title', 'Tes Kemampuan Psikologi & Sikap Kerja')->first();
        $response->assertRedirect(route('admin.assessments.show', $created->id));
    }

    public function test_admin_can_add_question_and_answers_to_question_bank(): void
    {
        $admin = User::where('email', 'admin@recruitment.test')->first();
        $this->actingAs($admin);

        $assessment = Assessment::first();

        $questionData = [
            'question_text' => 'Manakah perintah Artisan yang digunakan untuk menjalankan migrasi database?',
            'points' => 20,
            'explanation' => 'php artisan migrate digunakan untuk mengeksekusi migrasi yang belum dijalankan.',
            'options' => [
                'php artisan migrate',
                'php artisan db:run',
                'php artisan make:migration',
                'php artisan schema:up',
            ],
            'correct_option' => 0,
        ];

        $response = $this->post(route('admin.assessments.questions.store', $assessment->id), $questionData);
        $response->assertRedirect(route('admin.assessments.show', $assessment->id));

        $this->assertDatabaseHas('assessment_questions', [
            'assessment_id' => $assessment->id,
            'question_text' => 'Manakah perintah Artisan yang digunakan untuk menjalankan migrasi database?',
            'points' => 20,
        ]);

        $question = AssessmentQuestion::where('question_text', 'Manakah perintah Artisan yang digunakan untuk menjalankan migrasi database?')->first();
        $this->assertCount(4, $question->answers);

        $correctAnswer = $question->answers()->where('is_correct', true)->first();
        $this->assertNotNull($correctAnswer);
        $this->assertEquals('php artisan migrate', $correctAnswer->answer_text);
    }

    public function test_candidate_can_view_assessments_list(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $this->actingAs($kandidatUser);

        $response = $this->get(route('kadidat.assessments.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Tes yang Tersedia');
    }

    public function test_candidate_can_take_assessment_and_submit_answers(): void
    {
        $kandidatUser = User::where('email', 'kandidat@recruitment.test')->first();
        $candidate = Candidate::where('user_id', $kandidatUser->id)->first();
        $this->actingAs($kandidatUser);

        // Find test with questions
        $assessment = Assessment::has('questions')->first();
        $this->assertNotNull($assessment);

        $responseTake = $this->get(route('kadidat.assessments.take', $assessment->id));
        $responseTake->assertStatus(200);
        $responseTake->assertSee($assessment->title);

        // Prepare answers with 100% correct answers
        $answers = [];
        foreach ($assessment->questions as $q) {
            $correct = $q->answers()->where('is_correct', true)->first();
            $answers[$q->id] = $correct->id;
        }

        $submitPayload = [
            'answers' => $answers,
            'duration_spent' => 300,
        ];

        $responseSubmit = $this->post(route('kadidat.assessments.submit', $assessment->id), $submitPayload);

        $this->assertDatabaseHas('candidate_test_results', [
            'candidate_id' => $candidate->id,
            'assessment_id' => $assessment->id,
            'result' => 'passed',
            'score' => 100.0,
        ]);

        $result = CandidateTestResult::where('candidate_id', $candidate->id)
            ->where('assessment_id', $assessment->id)
            ->latest('id')
            ->first();

        $responseSubmit->assertRedirect(route('kadidat.assessments.result', $result->id));

        // Candidate can view the result page
        $responseResult = $this->get(route('kadidat.assessments.result', $result->id));
        $responseResult->assertStatus(200);
        $responseResult->assertSee('Hasil Ujian');
        $responseResult->assertSee('100.0%');
        $responseResult->assertSee('Passed');
    }
}
