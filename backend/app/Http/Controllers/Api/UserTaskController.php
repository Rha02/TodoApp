<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserTaskController extends Controller
{
    public function index()
    {
      return auth()->user()->assignedTasks->toArray();
    }
}
