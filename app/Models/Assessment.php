<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    public const TEST_TYPES = [
        'Technical Test',
        'Logic Test',
        'Administrative Test',
        'Psychological Test',
        'Personality Test',
    ];

    protected $fillable = [
        'title',
        'test_type',
        'position_id',
        'description',
        'duration',
        'passing_score',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'passing_score' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(CandidateTestResult::class);
    }
}
