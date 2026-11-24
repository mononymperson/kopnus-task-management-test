<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function create(User $user, Project $project, array $data): Task
    {
        return DB::transaction(function () use ($user, $project, $data) {
            $task = $project->tasks()->create([
                ...$data,
                'status' => Task::STATUS_PENDING,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => ActivityLog::TASK_CREATED,
                'subject_type' => Task::class,
                'subject_id' => $task->id,
                'payload' => [
                    'title' => $task->title,
                    'project_id' => $project->id,
                ],
            ]);

            return $task;
        });
    }

    public function update(User $user, Task $task, array $data): Task
    {
        $newStatus = $data['status'];

        throw_unless(
            $task->canTransitionTo($newStatus),
            ValidationException::withMessages([
                'status' => 'Perubahan status tidak valid. Status hanya boleh maju satu tahap: pending ke in_progress, lalu in_progress ke completed.',
            ])
        );

        return DB::transaction(function () use ($user, $task, $newStatus) {
            $oldStatus = $task->status;
            $task->update(['status' => $newStatus]);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => ActivityLog::TASK_STATUS_UPDATED,
                'subject_type' => Task::class,
                'subject_id' => $task->id,
                'payload' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                ],
            ]);

            return $task;
        });
    }

    public function delete(User $user, Task $task): void
    {
        DB::transaction(function () use ($user, $task) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => ActivityLog::TASK_DELETED,
                'subject_type' => Task::class,
                'subject_id' => $task->id,
                'payload' => ['title' => $task->title],
            ]);

            $task->delete();
        });
    }
}
