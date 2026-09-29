<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'gender',
        'date_of_birth',
        'place_of_birth',
        'nik',
        'address',
        'city',
        'province',
        'postal_code',
        'linkedin_url',
        'portfolio_url',
        'summary',
        'photo',
        'resume_path',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
