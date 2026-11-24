<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    public const PROJECT_CREATED = 'PROJECT_CREATED';
    public const PROJECT_UPDATED = 'PROJECT_UPDATED';
    public const PROJECT_DELETED = 'PROJECT_DELETED';
    public const TASK_CREATED = 'TASK_CREATED';
    public const TASK_STATUS_UPDATED = 'TASK_STATUS_UPDATED';
    public const TASK_DELETED = 'TASK_DELETED';

    public const ACTIONS = [
        self::PROJECT_CREATED => 'Proyek Dibuat',
        self::PROJECT_UPDATED => 'Proyek Diperbarui',
        self::PROJECT_DELETED => 'Proyek Dihapus',
        self::TASK_CREATED => 'Tugas Dibuat',
        self::TASK_STATUS_UPDATED => 'Status Tugas Diubah',
        self::TASK_DELETED => 'Tugas Dihapus',
    ];

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'payload',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
