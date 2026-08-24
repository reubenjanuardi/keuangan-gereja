<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /**
     * Record a new activity log entry.
     */
    public static function log(
        string $description,
        string $logName = 'default',
        ?Model $subject = null,
        ?array $properties = []
    ): self {
        $user = Auth::user();

        $defaultProps = [
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        $mergedProps = array_merge($defaultProps, $properties ?? []);

        return static::create([
            'log_name' => $logName,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'causer_type' => $user ? get_class($user) : null,
            'causer_id' => $user?->getKey(),
            'properties' => $mergedProps,
        ]);
    }
}
