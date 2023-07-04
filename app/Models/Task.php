<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    protected $guarded = [];

    public static function taskOpen()
    {
        return Self::where('task_status', 'open');
    }

    public static function taskProgress()
    {
        return Self::where('task_status', 'progress');
    }

    public static function taskDone()
    {
        return Self::where('task_status', 'done');
    }

    public static function taskCancelled()
    {
        return Self::where('task_status', 'cancelled');
    }

    public function getDueDateAttribute($date)
    {
        return Carbon::parse($date)->isoFormat('dddd, DD MMMM YYYY');
    }

    public function getDoneDateAttribute($date)
    {
        return Carbon::parse($date)->isoFormat('dddd, DD MMMM YYYY');
    }

    public function getCancelledDateAttribute($date)
    {
        return Carbon::parse($date)->isoFormat('dddd, DD MMMM YYYY');
    }
}
