<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => self::STATUS_IN_PROGRESS,
        self::STATUS_IN_PROGRESS => self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'project_id',
        'title',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return isset(self::ALLOWED_TRANSITIONS[$this->status])
            && self::ALLOWED_TRANSITIONS[$this->status] === $newStatus;
    }

    public function nextStatus(): ?string
    {
        return self::ALLOWED_TRANSITIONS[$this->status] ?? null;
    }
}
