<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create(User $user, Project $project): bool
    {
        return (int) $project->user_id === (int) $user->id;
    }

    public function update(User $user, Task $task): bool
    {
        return (int) $task->project->user_id === (int) $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return (int) $task->project->user_id === (int) $user->id;
    }
}
