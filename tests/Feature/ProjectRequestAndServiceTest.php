<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRequestAndServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_others_project_by_manual_url(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $project = Project::create(['user_id' => $owner->id, 'name' => 'Proyek Pribadi']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'Tugas Pribadi', 'status' => 'pending']);

        $this->actingAs($intruder)->get(route('projects.show', $project))->assertForbidden();
        $this->actingAs($intruder)->put(route('projects.update', $project), ['name' => 'Hacked'])->assertForbidden();
        $this->actingAs($intruder)->delete(route('projects.destroy', $project))->assertForbidden();
        $this->actingAs($intruder)->patch(route('tasks.updateStatus', $task), ['status' => 'in_progress'])->assertForbidden();
    }

    public function test_project_update_uses_form_request_and_service(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Lama']);

        $this->actingAs($user)->put(route('projects.update', $project), ['name' => 'Baru'])
            ->assertSessionHasNoErrors();

        $this->assertEquals('Baru', $project->fresh()->name);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'PROJECT_UPDATED',
            'subject_type' => Project::class,
            'subject_id' => $project->id,
        ]);

        $this->actingAs($user)
            ->from(route('projects.show', $project))
            ->followingRedirects()
            ->put(route('projects.update', $project), ['name' => ''])
            ->assertSee('Nama proyek wajib diisi.')
            ->assertSee('border-slate-200 pt-4" open', false);
    }

    public function test_invalid_status_transition_rejected_by_form_request(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Proyek']);
        $task = Task::create(['project_id' => $project->id, 'title' => 'Tugas', 'status' => 'pending']);

        $this->actingAs($user)->patch(route('tasks.updateStatus', $task), ['status' => 'completed'])
            ->assertSessionHasErrors('status');

        $this->assertEquals('pending', $task->fresh()->status);
        $this->assertDatabaseMissing('activity_logs', ['action' => 'TASK_STATUS_UPDATED']);
    }

    public function test_task_service_handles_create_update_delete(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Proyek']);

        $this->actingAs($user)->post(route('tasks.store', $project), ['title' => 'Tugas A'])
            ->assertSessionHasNoErrors();

        $task = $project->tasks()->firstOrFail();
        $this->assertEquals('pending', $task->status);

        $this->actingAs($user)->patch(route('tasks.updateStatus', $task), ['status' => 'in_progress'])
            ->assertSessionHasNoErrors();
        $this->assertEquals('in_progress', $task->fresh()->status);

        $this->actingAs($user)->delete(route('tasks.destroy', $task))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'TASK_DELETED',
            'subject_type' => Task::class,
            'subject_id' => $task->id,
        ]);
    }

    public function test_project_detail_hides_activity_logs(): void
    {
        $user = User::factory()->create();
        $project = Project::create(['user_id' => $user->id, 'name' => 'Proyek']);
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'PROJECT_CREATED',
            'subject_type' => Project::class,
            'subject_id' => $project->id,
            'payload' => [],
        ]);

        $this->actingAs($user)->get(route('projects.show', $project))
            ->assertStatus(200)
            ->assertDontSee('Riwayat Aktivitas');
    }
}
