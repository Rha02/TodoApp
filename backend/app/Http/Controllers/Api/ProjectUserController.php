<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;

class ProjectUserController extends Controller
{
    protected $user;

    public function __construct()
    {
      $this->user = auth()->user();
    }

    public function store(Project $project)
    {
      if (auth()->id() != $project->user_id) {
        return response()->json([
          'is_error' => true,
          'message' => 'You are not authorized for this action.'
        ]);
      }

      $member = User::where('email', request('email'))->first();

      if (! $member) {
        return response()->json([
          'is_error' => true,
          'message' => 'This user does not exist.'
        ]);
      }

      if ($project->members->contains($member)) {
        return response()->json([
          'is_error' => true,
          'message' => 'This user is already part of the project'
        ]);
      }

      $project->members()->attach($member->id);

      return $project->members;
    }

    public function index(Project $project)
    {
      if (! $project->members->contains($this->user->id)) {
        return response()->json([
          'is_error' => true,
          'message' => 'You are not authorized for this action.'
        ]);
      }

      return $project->members;
    }

    public function destroy(Project $project)
    {
      if (auth()->id() != $project->user_id) {
        return response()->json([
          'is_error' => true,
          'message' => 'You are not authorized for this action.'
        ]);
      }

      $member = User::where('email', request('email'))->first();

      if (!$member || !$project->members->contains($member)) {
        return response()->json([
          'is_error' => true,
          'message' => 'This member is not in your group.'
        ]);
      }

      if ($project->user_id == $member->id) {
        return response()->json([
          'is_error' => true,
          'message' => 'You cannot remove yourself from a project.'
        ]);
      }

      $member->assignedTasks->each(function ($task) use ($project) {
        if ($task->project_id == $project->id) {
          $task->update([
            'project_id' => $task->project_id,
            'body' => $task->body,
            'status' => $task->status,
            'user_id' => null
          ]);
        }
      });

      $project->members()->detach($member->id);

      return 'Success';
    }
}
