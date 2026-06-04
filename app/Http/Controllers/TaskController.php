<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->query("project_id");

        $tasks = Task::when($projectId, function ($query, $projectId) {
            return $query->where("project_id", $projectId);
        })
            ->orderBy("priority")
            ->get();

        return Inertia::render("Tasks/Index", [
            "tasks" => $tasks,
            "projects" => Project::all(),
            "currentProjectId" => $projectId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "project_id" => "nullable|exists:projects,id",
        ]);

        $maxPriority =
            Task::where("project_id", $validated["project_id"])->max(
                "priority",
            ) ?? 0;
        $validated["priority"] = $maxPriority + 1;

        Task::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
        ]);

        $task->update($validated);

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            "tasks" => "required|array",
            "tasks.*.id" => "required|exists:tasks,id",
        ]);

        foreach ($validated["tasks"] as $index => $taskData) {
            Task::where("id", $taskData["id"])->update([
                "priority" => $index + 1,
            ]);
        }

        return redirect()->back();
    }
}
