<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ProjectServices
{

    public function getAllForUser(User $user): Collection
    {
        if ($user->hasRole('admin')) {
            return Project::with('owner')->get();
        }

        return $user->projects()->with('owner')->get();
    }


    public function createProject(User $user, array $data): Project
    {
        $project = $user->projects()->create($data);
        return $project->load('owner');
    }


    public function updateProject(User $user, Project $project, array $data): Project
    {
        $this->verifyAccess($user, $project);

        $project->update($data);
        return $project->load('owner');
    }


    public function deleteProject(User $user, Project $project): void
    {
        $this->verifyAccess($user, $project);
        $project->delete();
    }


    public function verifyAccess(User $user, Project $project): void
    {
        if ($project->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'У вас нет прав на просмотр или изменение этого проекта.');
        }
    }
}
