<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskRequest;
use App\Models\Task;
use Exception;
use Illuminate\Http\JsonResponse;
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
        $taskOpen       = Task::taskOpen();
        $taskProgress   = Task::taskProgress();
        $taskDone       = Task::taskDone();
        $taskCancelled  = Task::taskCancelled();

        return response()->json([
            'taskOpen'              => $taskOpen->get(),
            'taskProgress'          => $taskProgress->get(),
            'taskDone'              => $taskDone->get(),
            'taskCancelled'         => $taskCancelled->get(),
            'taskOpenCount'         => $taskOpen->count(),
            'taskProgressCount'     => $taskProgress->count(),
            'taskDoneCount'         => $taskDone->count(),
            'taskCancelledCount'    => $taskCancelled->count()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        DB::beginTransaction();

        $data = $request->data();

        try {
            $task = $request->saveTask($data);

            if (!$task) {
                throw new \Exception("Can't create task");
            }

            DB::commit();
            return response()->json([
                'status'    => true,
                'data'      => $task,
                'message'   => 'Created task successful'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'    => false,
                'data'      => $e->getMessage(),
                'message'   => 'Created task unsuccessful'
            ]);
        }
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
