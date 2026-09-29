<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateTestResult extends Model
{
    use HasFactory;

    protected $table = 'candidate_test_results';

    protected $fillable = [
        'candidate_id',
        'candidate_application_id',
        'assessment_id',
        'score',
        'result',
        'duration_spent',
        'total_questions',
        'correct_answers',
        'answers_payload',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
            'duration_spent' => 'integer',
            'total_questions' => 'integer',
            'correct_answers' => 'integer',
            'answers_payload' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(CandidateApplication::class, 'candidate_application_id');
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
