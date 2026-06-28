<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Task",
    title: "Task",
    description: "Task model schema",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Buy groceries"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Milk, bread, cheese"),
        new OA\Property(property: "status", type: "string", enum: ["Pending", "In Progress", "Completed"], example: "Pending"),
        new OA\Property(property: "due_date", type: "string", format: "date", nullable: true, example: "2026-07-01"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-28T12:00:00.000000Z"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-06-28T12:00:00.000000Z")
    ]
)]
class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $guarded = []; // set all attributes as mass assignable


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
