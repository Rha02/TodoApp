<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['goal_id', 'user_id', 'body', 'status', 'depth'];

    protected $with = ['prevTasks', 'nextTasks'];

    public function goal()
    {
      return $this->belongsTo(Goal::class);
    }

    public function assignedUser()
    {
      return $this->belongsTo(User::class);
    }

    public function prevTasks()
    {
      return $this->hasMany(Sequence::class, 'to_task_id')
        ->join('tasks', 'sequences.from_task_id', '=', 'tasks.id');
    }

    public function nextTasks()
    {
      return $this->hasMany(Sequence::class, 'from_task_id')
        ->join('tasks', 'tasks.id', '=', 'sequences.to_task_id');
    }
}
