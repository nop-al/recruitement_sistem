<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Onboarding extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
    ];

    protected static function booted(): void
    {
        static::creating(function (Onboarding $onboarding): void {
            if (! $onboarding->start_date && $onboarding->join_date) {
                $onboarding->start_date = $onboarding->join_date;
            } elseif (! $onboarding->start_date) {
                $onboarding->start_date = now()->toDateString();
            }

            if (! $onboarding->join_date && $onboarding->start_date) {
                $onboarding->join_date = $onboarding->start_date;
            }
        });
    }

    protected $fillable = [
        'candidate_id',
        'candidate_application_id',
        'position_id',
        'offering_id',
        'start_date',
        'join_date',
        'status',
        'checklist_percentage',
        'ktp_number',
        'ktp_file',
        'ktp_verified',
        'npwp_number',
        'npwp_file',
        'npwp_verified',
        'bpjs_number',
        'bpjs_file',
        'bpjs_verified',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'bank_book_file',
        'bank_verified',
        'ijazah_file',
        'ijazah_verified',
        'pas_foto_file',
        'pas_foto_verified',
        'contract_file',
        'contract_signed',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'emergency_verified',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'join_date' => 'date',
            'checklist_percentage' => 'integer',
            'ktp_verified' => 'boolean',
            'npwp_verified' => 'boolean',
            'bpjs_verified' => 'boolean',
            'bank_verified' => 'boolean',
            'ijazah_verified' => 'boolean',
            'pas_foto_verified' => 'boolean',
            'contract_signed' => 'boolean',
            'emergency_verified' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Recalculate checklist completion percentage based on submitted/verified items.
     */
    public function updateCalculatedPercentage(): int
    {
        $items = [
            'ktp' => ! empty($this->ktp_number) || ! empty($this->ktp_file) || $this->ktp_verified,
            'npwp' => ! empty($this->npwp_number) || ! empty($this->npwp_file) || $this->npwp_verified,
            'bpjs' => ! empty($this->bpjs_number) || ! empty($this->bpjs_file) || $this->bpjs_verified,
            'bank' => (! empty($this->bank_account_number) && ! empty($this->bank_name)) || $this->bank_verified,
            'ijazah' => ! empty($this->ijazah_file) || $this->ijazah_verified,
            'pas_foto' => ! empty($this->pas_foto_file) || $this->pas_foto_verified,
            'contract' => ! empty($this->contract_file) || $this->contract_signed,
            'emergency' => (! empty($this->emergency_contact_name) && ! empty($this->emergency_contact_phone)) || $this->emergency_verified,
        ];

        $completedCount = count(array_filter($items));
        $percentage = (int) round(($completedCount / 8) * 100);

        $this->checklist_percentage = $percentage;

        if ($percentage > 0 && $this->status === self::STATUS_PENDING) {
            $this->status = self::STATUS_IN_PROGRESS;
        }

        return $percentage;
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

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }
}
