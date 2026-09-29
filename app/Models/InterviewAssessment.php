<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'position_id',
        'interviewer_id',
        'interview_type',
        'interview_method',
        'scheduled_at',
        'room_link',
        'location',
        'technical_score',
        'communication_score',
        'culture_score',
        'total_score',
        'notes',
        'recommendation',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
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

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
