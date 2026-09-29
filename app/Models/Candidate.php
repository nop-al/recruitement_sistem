<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'candidates';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'is_active',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(CandidateEducation::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(CandidateExperience::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(CandidateSkill::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CandidateApplication::class);
    }

    public function interviewAssessments(): HasMany
    {
        return $this->hasMany(InterviewAssessment::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(CandidateTestResult::class);
    }

    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class);
    }

    public function onboardings(): HasMany
    {
        return $this->hasMany(Onboarding::class);
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class)->latestOfMany();
    }

    /**
     * Check if candidate is currently in an active offering or beyond (onboarding / hired).
     */
    public function hasActiveOffering(): bool
    {
        $hasOffering = $this->offerings()
            ->whereIn('status', [Offering::STATUS_SENT, Offering::STATUS_ACCEPTED])
            ->exists();

        if ($hasOffering) {
            return true;
        }

        return $this->applications()
            ->whereIn('stage', ['offering', 'onboarding', 'hired'])
            ->where('status', '!=', 'rejected')
            ->exists();
    }
}
