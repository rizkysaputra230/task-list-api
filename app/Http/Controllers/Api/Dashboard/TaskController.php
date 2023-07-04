<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function chart(Request $request)
    {
        $type = $request->type ?? 'year';

        $label = [];
        $open = [];
        $onProgress = [];
        $done = [];
        $cancelled = [];
        if ($type === 'year') {
            $label = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            $open = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $onProgress = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $done = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $cancelled = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            $queryOpen = Task::where('task_status', 'open')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(due_date) as month'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('MONTH(due_date)')
                ->get();

            foreach ($queryOpen as $dataOpen) {
                $index = (int) $dataOpen->month;
                $index -= 1;
                $open[$index] = $dataOpen->total;
            }

            $queryProgress = Task::where('task_status', 'progress')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(due_date) as month'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('MONTH(due_date)')
                ->get();

            foreach ($queryProgress as $dataProgress) {
                $index = (int) $dataProgress->month;
                $index -= 1;
                $onProgress[$index] = $dataProgress->total;
            }

            $queryDone = Task::where('task_status', 'done')
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(due_date) as month'))
                ->groupByRaw('MONTH(due_date)')
                ->get();

            foreach ($queryDone as $dataDone) {
                $index = (int) $dataDone->month;
                $index -= 1;
                $done[$index] = $dataDone->total;
            }

            $queryCancelled = Task::where('task_status', 'cancelled')
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->select(DB::raw('COUNT(*) as total'), DB::raw('MONTH(due_date) as month'))
                ->groupByRaw('MONTH(due_date)')
                ->get();

            foreach ($queryCancelled as $dataCancelled) {
                $index = (int) $dataCancelled->month;
                $index -= 1;
                $cancelled[$index] = $dataCancelled->total;
            }
        } elseif ($type === 'month') {
            $label = [];
            $totalDays = Carbon::now()->daysInMonth;

            foreach (range(1, $totalDays) as $day) {
                $open[] = 0;
                $onProgress[] = 0;
                $done[] = 0;
                $cancelled[] = 0;
                $label[] = $day;
            }

            $queryOpen = Task::where('task_status', 'open')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('DAY(due_date) as day'))
                ->whereMonth('due_date', Carbon::now()->format('m'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            foreach ($queryOpen as $dataOpen) {
                $index = (int) $dataOpen->day;
                $index -= 1;
                $open[$index] = $dataOpen->total;
            }
            $queryProgress = Task::where('task_status', 'progress')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('DAY(due_date) as day'))
                ->whereMonth('due_date', Carbon::now()->format('m'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            foreach ($queryProgress as $dataProgress) {
                $index = (int) $dataProgress->day;
                $index -= 1;
                $onProgress[$index] = $dataProgress->total;
            }

            $queryDone = Task::where('task_status', 'done')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('DAY(due_date) as day'))
                ->whereMonth('due_date', Carbon::now()->format('m'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            foreach ($queryDone as $dataDone) {
                $index = (int) $dataDone->day;
                $index -= 1;
                $done[$index] = $dataDone->total;
            }

            $queryCancelled = Task::where('task_status', 'cancelled')
                ->select(DB::raw('COUNT(*) as total'), DB::raw('DAY(due_date) as day'))
                ->whereMonth('due_date', Carbon::now()->format('m'))
                ->whereYear('due_date', Carbon::now()->format('Y'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            foreach ($queryCancelled as $dataCancelled) {
                $index = (int) $dataCancelled->day;
                $index -= 1;
                $cancelled[$index] = $dataCancelled->total;
            }
        } else {
            $label = [date('Y-m-d')];
            $queryOpen = Task::where('task_status', 'open')
                ->select(DB::raw('COUNT(*) as total'))
                ->whereDate('due_date', date('Y-m-d'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            $open = [$queryOpen[0]->total ?? 0];

            $queryOnProgress = Task::where('task_status', 'progress')
                ->select(DB::raw('COUNT(*) as total'))
                ->whereDate('due_date', date('Y-m-d'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            $onProgress = [$queryOnProgress[0]->total ?? 0];

            $queryDone = Task::where('task_status', 'done')
                ->select(DB::raw('COUNT(*) as total'))
                ->whereDate('due_date', date('Y-m-d'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            $done = [$queryDone[0]->total ?? 0];

            $queryCancelled = Task::where('task_status', 'cancelled')
                ->select(DB::raw('COUNT(*) as total'))
                ->whereDate('due_date', date('Y-m-d'))
                ->groupByRaw('DAY(due_date)')
                ->get();

            $cancelled = [$queryCancelled[0]->total ?? 0];
        }

        return response()->json([
            'labels'         => $label,
            'open'          => $open,
            'on_progress'   => $onProgress,
            'done'          => $done,
            'cancelled'     => $cancelled
        ]);
    }
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
        $count = [
            'open'          => $taskOpen->count(),
            'on_progress'   => $taskProgress->count(),
            'done'          => $taskDone->count(),
            'cancelled'     => $taskCancelled->count(),
        ];

        $task = [
            'open'          => $taskOpen->get(),
            'on_progress'   => $taskProgress->get(),
            'done'          => $taskDone->get(),
            'cancelled'     => $taskCancelled->get(),
        ];

        return response()->json([
            'task'          => $task,
            'count'         => $count
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
        $task = Task::find($id);

        return response()->json($task);
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

        $task->delete();

        return response()->json([
            'status'    => true,
            'message'   => 'Delete task successful'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $task = $task->update([
            'task_status'       => $request->task_status,
            'done_date'         => $request->task_status === 'done' ? Carbon::now() : null,
            'cancelled_date'    => $request->task_status === 'cancelled' ? Carbon::now() : null,
        ]);
    }
}
