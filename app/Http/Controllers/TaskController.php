<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $req)
    {
        $tasks = Task::where('user_id', auth('sanctum')->id())
            ->when($req->search, fn($qry) => $qry->whereLike('title', "%{$req->search}%"))
            ->when($req->status, fn($qry) => $qry->whereLike('status', $req->status))
            ->latest()
            ->paginate();

        return response()->json(['tasks' => $tasks]);
    }

    public function store(Request $req)
    {
        $validated = $req->validate([
            'title' => 'required|string|between:3,255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,In Progress,Completed',
            'due_date' => 'nullable|date',
        ]);

        $validated['user_id'] = auth('sanctum')->id();

        $task = Task::create($validated);

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function show(Task $task)
    {
        if ($task->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json(['task' => $task]);
    }

    public function update(Request $req, Task $task)
    {
        if ($task->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $validated = $req->validate([
            'title' => 'required|string|between:3,255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:Pending,In Progress,Completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
