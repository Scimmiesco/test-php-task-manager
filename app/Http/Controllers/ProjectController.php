<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
        ]);

        Project::create($validated);

        return redirect()
            ->route("tasks.index")
            ->with("success", "Project created!");
    }
}
