<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class TaskStatusConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_status_requests_apply_one_transition_and_log(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Proyek']);
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Tugas',
            'status' => Task::STATUS_PENDING,
        ]);

        $firstRequestTask = Task::findOrFail($task->id);
        $secondRequestTask = Task::findOrFail($task->id);
        $tasks = app(TaskService::class);

        $tasks->update($user, $firstRequestTask, ['status' => Task::STATUS_IN_PROGRESS]);

        try {
            $tasks->update($user, $secondRequestTask, ['status' => Task::STATUS_IN_PROGRESS]);
        } catch (Throwable) {
        }

        $this->assertSame(Task::STATUS_IN_PROGRESS, Task::findOrFail($task->id)->status);
        $this->assertSame(1, ActivityLog::where([
            'action' => ActivityLog::TASK_STATUS_UPDATED,
            'subject_type' => Task::class,
            'subject_id' => $task->id,
        ])->count());
    }
}
