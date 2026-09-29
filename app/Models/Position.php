<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'department',
        'location',
        'type',
        'experience_level',
        'salary_min',
        'salary_max',
        'description',
        'requirements',
        'status',
        'is_internal',
        'deadline',
        'quota',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'is_internal' => 'boolean',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CandidateApplication::class);
    }

    public function interviewAssessments(): HasMany
    {
        return $this->hasMany(InterviewAssessment::class);
    }

    /**
     * Get the encrypted ID for public URL parameters (no raw database ID exposed).
     */
    public function getEncryptedIdAttribute(): string
    {
        return encrypt($this->id);
    }
}
