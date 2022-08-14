<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->get();

        // TODO: Improve calculation of time spent on a task
        foreach($tasks as $task) {
            $timeSpent = Block::where('task_id', $task->id)->sum(DB::raw('TIME_TO_SEC(duration)'));
            $timeSpent = date('H:i:s', $timeSpent);
            $task['time_spent'] = $timeSpent;
        }
        
        return $tasks;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required'
        ]);

        $fields['user_id'] = auth()->id();

        return Task::create($fields);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::find($id);
        
        if(!$task || $task->user_id != auth()->id()) {
            return response([
                'message' => 'Unauthorised Action'
            ], 401);
        }

        return $task;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if(!$task || $task->user_id != auth()->id()) {
            return response([
                'message' => 'Unauthorised Action'
            ], 401);
        }

        $task->update($request->all());

        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::find($id);
        
        if(!$task || $task->user_id != auth()->id()) {
            return response([
                'message' => 'Unauthorised Action'
            ], 401);
        }

        return $task->delete();
    }
}
