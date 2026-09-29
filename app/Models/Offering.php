<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offering extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_EXPIRED = 'expired';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SENT => 'Sent',
        self::STATUS_ACCEPTED => 'Accepted',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_EXPIRED => 'Expired',
    ];

    public const EMPLOYMENT_TYPES = [
        'Full-time',
        'Contract',
        'Internship',
        'Part-time',
    ];

    protected $fillable = [
        'candidate_id',
        'candidate_application_id',
        'position_id',
        'department',
        'salary',
        'salary_offered',
        'employment_type',
        'join_date',
        'start_date',
        'benefits',
        'notes',
        'status',
        'document_path',
        'sent_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'start_date' => 'date',
            'salary' => 'decimal:2',
            'salary_offered' => 'decimal:2',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * Ensure salary and salary_offered are synchronized.
     */
    public function getEffectiveSalaryAttribute(): float
    {
        return (float) ($this->salary ?? $this->salary_offered ?? 0);
    }

    /**
     * Ensure join_date and start_date are synchronized.
     */
    public function getEffectiveJoinDateAttribute()
    {
        return $this->join_date ?? $this->start_date;
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(CandidateApplication::class, 'candidate_application_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class);
    }
}
