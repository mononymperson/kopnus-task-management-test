<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ListingQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_list_query_count_does_not_grow_with_project_count(): void
    {
        $user = User::factory()->create();

        $this->createProjectsWithTasks($user, 3);
        $smallCount = $this->countListingQueries(fn () => $this->actingAs($user)->get(route('dashboard'))->assertOk());

        $this->createProjectsWithTasks($user, 7);
        $largeCount = $this->countListingQueries(fn () => $this->actingAs($user)->get(route('dashboard'))->assertOk());

        $this->assertSame($smallCount, $largeCount);
    }

    public function test_task_list_query_count_does_not_grow_with_task_count(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Proyek']);

        $this->createTasks($project, 3);
        $smallCount = $this->countListingQueries(fn () => $this->actingAs($user)->get(route('projects.show', $project))->assertOk());

        $this->createTasks($project, 7);
        $largeCount = $this->countListingQueries(fn () => $this->actingAs($user)->get(route('projects.show', $project))->assertOk());

        $this->assertSame($smallCount, $largeCount);
    }

    private function createProjectsWithTasks(User $user, int $projectCount): void
    {
        foreach (range(1, $projectCount) as $index) {
            $project = Project::create([
                'user_id' => $user->id,
                'name' => "Proyek {$index}",
            ]);

            $this->createTasks($project, 3);
        }
    }

    private function createTasks(Project $project, int $taskCount): void
    {
        foreach (range(1, $taskCount) as $index) {
            Task::create([
                'project_id' => $project->id,
                'title' => "Tugas {$index}",
                'status' => Task::STATUS_PENDING,
            ]);
        }
    }

    private function countListingQueries(Closure $request): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $request();

        DB::disableQueryLog();

        return collect(DB::getQueryLog())
            ->filter(fn (array $query): bool => preg_match('/\b(?:from|join)\s+"?(?:projects|tasks)"?\b/i', $query['query']) === 1)
            ->count();
    }
}
