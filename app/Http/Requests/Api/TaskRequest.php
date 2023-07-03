<?php

namespace App\Http\Requests\Api;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'task_name'     => 'required',
            'due_date'      => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'task_name.required'    => 'Task name is required',
            'due_date.required'     => 'Due date is required',
        ];
    }

    public function data(): array
    {
        return [
            'task_name'     => $this->task_name,
            'due_date'      => date('Y-m-d', ((int) $this->due_date) / 1000),
            'task_status'   => 'open',
            'created_by'    => request()->user()->id
        ];
    }

    public function saveTask($data = [])
    {
        $task = new Task();
        $task->task_name    = $data['task_name'];
        $task->due_date     = $data['due_date'];
        $task->task_status  = $data['task_status'];
        $task->created_by   = $data['created_by'];
        $task->save();

        return $task;
    }
}
