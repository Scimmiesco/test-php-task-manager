<?php
namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function createTask(array $data): Task
    {
        if (empty($data["priority"])) {
            $maxPriority = Task::where(
                "project_id",
                $data["project_id"] ?? null,
            )->max("priority");

            $data["priority"] = $maxPriority ? $maxPriority + 1 : 1;
        }

        $task = Task::create([
            "name" => $data["name"],
            "project_id" => $data["project_id"] ?? null,
            "priority" => $data["priority"],
        ]);

        return $task;
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }
}
