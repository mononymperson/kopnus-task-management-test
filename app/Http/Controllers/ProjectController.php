<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private readonly ProjectService $projects)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $projects = $request->user()
            ->projects()
            ->withCount('tasks')
            ->latest()
            ->get();

        return view('dashboard', compact('projects'));
    }

    public function store(StoreProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $this->projects->create($request->user(), $request->validated());

        return redirect()->route('dashboard')->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['tasks' => fn ($q) => $q->latest()]);

        return view('projects.show', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $this->projects->update($project, $request->validated());

        return back()->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $this->projects->delete($project);

        return redirect()->route('dashboard')->with('success', 'Proyek berhasil dihapus.');
    }
}
