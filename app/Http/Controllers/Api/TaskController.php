<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Services\TaskServices;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(TaskServices $taskServices){

    }
    public function index(Request $request){
        $task = $this->taskServices->getTask($request);


    }
    






}
