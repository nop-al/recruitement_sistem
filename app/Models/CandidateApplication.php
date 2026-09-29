<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CandidateApplication extends Model
{
    use HasFactory;

    protected $table = 'candidate_applications';

    protected $fillable = [
        'candidate_id',
        'position_id',
        'stage',
        'status',
        'match_score',
        'resume_url',
        'cover_letter',
        'hr_notes',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(CandidateTestResult::class, 'candidate_application_id');
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class, 'candidate_application_id');
    }

    public function offering(): HasOne
    {
        return $this->hasOne(Offering::class, 'candidate_application_id')->latestOfMany();
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class, 'candidate_application_id')->latestOfMany();
    }
}
