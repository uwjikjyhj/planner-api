<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Block::where('user_id', auth()->id())->get();
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
            'start' => ['required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
            'end' => ['required', 'date_format:Y-m-d H:i:s', 'after:start'],
            'task_id' => Rule::exists('tasks', 'id')->where('user_id', auth()->id())
        ]);

        $fields['user_id'] = auth()->id();

        $start = Carbon::parse($request['start']);
        $end = Carbon::parse($request['end']);
        $duration = $start->diff($end)->format('%H:%i:%s');
        $fields['duration'] = $duration;

        $block = Block::create($fields);

        return $block;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $block = Block::find($id);

        if(!$block || $block->user_id != auth()->id()) {
            return response([
                'message' => 'Unauthorised Action'
            ], 401);
        }

        $fields = $request->validate([
            'start' => ['required', 'date_format:Y-m-d H:i:s', 'before_or_equal:now'],
            'end' => ['required', 'date_format:Y-m-d H:i:s', 'after:start'],
            'task_id' => Rule::exists('tasks', 'id')->where('user_id', auth()->id())
        ]);

        
        $start = Carbon::parse($request['start']);
        $end = Carbon::parse($request['end']);
        $duration = $start->diff($end)->format('%H:%i:%s');
        $fields['duration'] = $duration;

        $block->update($fields);

        return $block;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $block = Block::find($id);

        if(!$block || $block->user_id != auth()->id()) {
            return response([
                'message' => 'Unauthorised Action'
            ], 401);
        }

        return $block->delete();
    }
}
