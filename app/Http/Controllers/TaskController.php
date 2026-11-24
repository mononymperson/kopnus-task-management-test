<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly TaskService $tasks)
    {
    }

    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('create', [Task::class, $project]);

        $this->tasks->create($request->user(), $project, $request->validated());

        return back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $this->tasks->update($request->user(), $task, $request->validated());

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        $this->tasks->delete($request->user(), $task);

        return back()->with('success', 'Tugas berhasil dihapus.');
    }
}
