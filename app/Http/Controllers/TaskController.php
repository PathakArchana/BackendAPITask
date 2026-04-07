<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Task;
use Illuminate\Http\Request;
use SoftDeletes;

class TaskController extends Controller
{

    public function index(Request $request)
        {
            return $request->user()->tasks()->latest()->paginate(10);

        }

public function store(Request $request)
{ 
    $request->validate([
        'title' => 'required',
        'status' => 'in:pending,in-progress,completed'
    ]);
    $task = $request->user()->tasks()->create($request->all());
    Log::info('Task created', ['task_id' => $task->id]);

    return response()->json($task,201);
}



public function show($id)
{
    return Task::findOrFail($id);
}

public function update(Request $request, $id)
{
    $task = Task::findOrFail($id);

    if ($task->user_id !== $request->user()->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $task->update($request->all());
    Log::info('Task updated', ['task_id' => $id]);    

    return response()->json($task);
}


public function destroy(Request $request, $id)
{
    $task = Task::findOrFail($id);
    if ($task->user_id !== $request->user()->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    $task->delete();
    Log::info('Task deleted', ['task_id' => $id]);

    return response()->json(['message' => 'Deleted']);
}
}
