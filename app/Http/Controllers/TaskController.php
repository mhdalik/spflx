<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\LogService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    #[OA\Get(
        path: "/api/tasks",
        summary: "List tasks",
        description: "Returns a paginated list of tasks for the authenticated user, optionally filtered by search and status.",
        tags: ["Tasks"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\QueryParameter(
                name: "search",
                description: "Filter tasks by title matching this term",
                required: false,
                schema: new OA\Schema(type: "string")
            ),
            new OA\QueryParameter(
                name: "status",
                description: "Filter tasks by status",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["Pending", "In Progress", "Completed"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of tasks with pagination details",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "tasks",
                            properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(
                                    property: "data",
                                    type: "array",
                                    items: new OA\Items(ref: "#/components/schemas/Task")
                                ),
                                new OA\Property(property: "first_page_url", type: "string", example: "http://localhost:8000/api/tasks?page=1"),
                                new OA\Property(property: "from", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page_url", type: "string", example: "http://localhost:8000/api/tasks?page=1"),
                                new OA\Property(property: "next_page_url", type: "string", nullable: true, example: null),
                                new OA\Property(property: "path", type: "string", example: "http://localhost:8000/api/tasks"),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
                                new OA\Property(property: "to", type: "integer", example: 2),
                                new OA\Property(property: "total", type: "integer", example: 2)
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            )
        ]
    )]
    public function index(Request $req)
    {
        $tasks = Task::where('user_id', auth('sanctum')->id())
            ->when($req->search, fn($qry) => $qry->whereLike('title', "%{$req->search}%"))
            ->when($req->status, fn($qry) => $qry->whereLike('status', $req->status))
            ->latest()
            ->paginate();

        return response()->json(['tasks' => $tasks]);
    }

    #[OA\Post(
        path: "/api/tasks",
        summary: "Create a new task",
        description: "Creates a new task for the authenticated user.",
        tags: ["Tasks"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "status"],
                properties: [
                    new OA\Property(property: "title", type: "string", minLength: 3, maxLength: 255, example: "Buy groceries"),
                    new OA\Property(property: "description", type: "string", maxLength: 255, nullable: true, example: "Milk, bread, cheese"),
                    new OA\Property(property: "status", type: "string", enum: ["Pending", "In Progress", "Completed"], example: "Pending"),
                    new OA\Property(property: "due_date", type: "string", format: "date", nullable: true, example: "2026-07-01")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Task created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task created successfully"),
                        new OA\Property(property: "task", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
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

        LogService::log('Task created');

        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    #[OA\Get(
        path: "/api/tasks/{task}",
        summary: "Get task details",
        description: "Retrieves the details of a specific task.",
        tags: ["Tasks"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\PathParameter(
                name: "task",
                description: "The ID of the task",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task details retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "task", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Access denied",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Access denied")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Task not found"
            )
        ]
    )]
    public function show(Task $task)
    {
        if ($task->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json(['task' => $task]);
    }

    #[OA\Put(
        path: "/api/tasks/{task}",
        summary: "Update task",
        description: "Updates an existing task.",
        tags: ["Tasks"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\PathParameter(
                name: "task",
                description: "The ID of the task to update",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "status"],
                properties: [
                    new OA\Property(property: "title", type: "string", minLength: 3, maxLength: 255, example: "Buy groceries"),
                    new OA\Property(property: "description", type: "string", maxLength: 255, nullable: true, example: "Milk, bread, cheese"),
                    new OA\Property(property: "status", type: "string", enum: ["Pending", "In Progress", "Completed"], example: "Completed"),
                    new OA\Property(property: "due_date", type: "string", format: "date", nullable: true, example: "2026-07-01")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Task updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task updated successfully"),
                        new OA\Property(property: "task", ref: "#/components/schemas/Task")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Access denied",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Access denied")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Task not found"
            ),
            new OA\Response(
                response: 422,
                description: "Validation error"
            )
        ]
    )]
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

        LogService::log('Task updated');

        return response()->json(['message' => 'Task updated successfully', 'task' => $task]);
    }

    #[OA\Delete(
        path: "/api/tasks/{task}",
        summary: "Delete task",
        description: "Deletes a specific task.",
        tags: ["Tasks"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\PathParameter(
                name: "task",
                description: "The ID of the task to delete",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Task deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task deleted successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated"
            ),
            new OA\Response(
                response: 403,
                description: "Access denied",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Access denied")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Task not found"
            )
        ]
    )]
    public function destroy(Task $task)
    {
        if ($task->user_id !== auth('sanctum')->id()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $task->delete();

        LogService::log('Task deleted');

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
