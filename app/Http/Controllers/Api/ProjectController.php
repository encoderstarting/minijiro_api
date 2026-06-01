<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectEditRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Models\Project;
use App\Services\ProjectService;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Внедряем сервис через конструктор
    public function __construct(private ProjectService $projectService) {}

    public function index(Request $request)
    {
        $projects = $this->projectService->getAllForUser($request->user());
        return ProjectResource::collection($projects);
    }

    public function store(ProjectStoreRequest, $request)
    {
        $data = $request->validated();




        $project = $this->projectService->createProject($request->user(), $data);

        return new ProjectResource($project);
    }

    public function show(Request $request, Project $project)
    {
        $this->projectService->verifyAccess($request->user(), $project);
        return new ProjectResource($project->load('owner'));
    }

    public function update(ProjectEditRequest $request, Project $project)
    {
        $data = $request->validated();


        $updatedProject = $this->projectService->updateProject($request->user(), $project, $data);

        return new ProjectResource($updatedProject);
    }

    public function destroy(Request $request, Project $project)
    {
        $this->projectService->deleteProject($request->user(), $project);
        return response()->json(null, 204);
    }
}
