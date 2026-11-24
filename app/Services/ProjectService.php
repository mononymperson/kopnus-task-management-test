<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function create(User $user, array $data): Project
    {
        return DB::transaction(function () use ($user, $data) {
            $project = $user->projects()->create($data);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => ActivityLog::PROJECT_CREATED,
                'subject_type' => Project::class,
                'subject_id' => $project->id,
                'payload' => ['name' => $project->name],
            ]);

            return $project;
        });
    }

    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update($data);

            ActivityLog::create([
                'user_id' => $project->user_id,
                'action' => ActivityLog::PROJECT_UPDATED,
                'subject_type' => Project::class,
                'subject_id' => $project->id,
                'payload' => ['name' => $project->name],
            ]);

            return $project;
        });
    }

    public function delete(Project $project): void
    {
        DB::transaction(function () use ($project) {
            ActivityLog::create([
                'user_id' => $project->user_id,
                'action' => ActivityLog::PROJECT_DELETED,
                'subject_type' => Project::class,
                'subject_id' => $project->id,
                'payload' => ['name' => $project->name],
            ]);

            $project->delete();
        });
    }
}
