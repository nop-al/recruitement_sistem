<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'module',
        'description',
        'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convenient static method to record an activity log.
     */
    public static function record(string $action, string $module, string $description, ?User $user = null, ?string $ip = null): self
    {
        $activeUser = $user ?? Auth::user();

        return self::create([
            'user_id' => $activeUser?->id,
            'user_name' => $activeUser?->name ?? 'System/Guest',
            'action' => strtoupper($action),
            'module' => $module,
            'description' => $description,
            'ip_address' => $ip ?? (request()?->ip() ?? '127.0.0.1'),
        ]);
    }
}
