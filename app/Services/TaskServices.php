<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class TaskServices
{

    public function getTasksForUser(User $user, ?int $projectId = null): Collection
    {
        $query = Task::with(['project', 'assignee']);


        if (!$user->hasRole('admin')) {
            $query->whereHas('project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->orWhere('assignee_id', $user->id);
        }

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->get();
    }


    public function createTask(User $user, array $data): Task
    {
        $project = Project::findOrFail($data['project_id']);

        if ($project->user_id !== $user->id && !$user->hasRole('admin')) {
            abort(403, 'Вы можете создавать задачи только в своих проектах.');
        }

        $task = Task::create($data);
        return $task->load(['project', 'assignee']);
    }


    public function updateTask(User $user, Task $task, array $data): Task
    {
        $this->verifyAccess($user, $task);

        $isOwner = $task->project->user_id === $user->id;
        $isAdmin = $user->hasRole('admin');

        // Исполнитель может менять только статус
        if (!$isOwner && !$isAdmin) {
            $allowedFields = ['status'];
            $attemptedFields = array_keys($data);

            if (array_diff($attemptedFields, $allowedFields)) {
                abort(403, 'Исполнитель может менять только статус задачи.');
            }
        }

        $task->update($data);
        return $task->load(['project', 'assignee']);
    }


    public function deleteTask(User $user, Task $task): void
    {
        $isOwner = $task->project->user_id === $user->id;
        $isAdmin = $user->hasRole('admin');

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Только владелец проекта или админ может удалить задачу.');
        }

        $task->delete();
    }


    public function verifyAccess(User $user, Task $task): void
    {
        $isAdmin = $user->hasRole('admin');
        $isProjectOwner = $task->project->user_id === $user->id;
        $isAssignee = $task->assignee_id === $user->id;

        if (!$isAdmin && !$isProjectOwner && !$isAssignee) {
            abort(403, 'У вас нет прав на эту задачу.');
        }
    }
}
