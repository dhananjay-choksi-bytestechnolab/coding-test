<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\{
    Task,
    Phase,
    User
};

class TaskController extends Controller
{
    public $taskModel, $phaseModel, $userModel;

    public function __construct(
        Task $taskModel,
        Phase $phaseModel,
        User $userModel
    ) {
        $this->taskModel = $taskModel;
        $this->phaseModel = $phaseModel;
        $this->userModel = $userModel;
    }

    public function kanban()
    {
        return view('tasks.index');
    }

    public function statistics()
    {
        return view('tasks.statistics');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->phaseModel->with('tasks.user')->get();
    }

    /**
     * Display a listing of the Users resource.
     */
    public function users()
    {
        return $this->userModel->all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // Create a new task from the $request
        $data = $request->validated();
        $task = $this->taskModel->create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    public function update(UpdateTaskRequest $request)
    {
        $data = $request->validated();
        $taskId = $data['id'];
        unset($data['id']);

        // Retrieve the Task model instance by its ID
        $task = Task::find($taskId);

        if ($task) {
            // Update the model attributes
            $task->fill($data);
            $task->save();
        } else {
            // Handle the case when the Task with the provided ID is not found
            // You can log an error or return a response as needed
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->taskModel->destroy($task->id);
    }

    public function getStatistics() {
        $usersWithTasks = $this->userModel
            ->with(['tasks'])
            ->get();

        $groupedUsers = $usersWithTasks->map(function ($user) {
            $dueTasks = [];
            $inProgressTasks = [];
            $completedTasks = [];

            foreach ($user->tasks as $task) {
                if ($task->status === 'due') {
                    $dueTasks[] = $task;
                } elseif ($task->status === 'in_progress') {
                    $inProgressTasks[] = $task;
                } elseif ($task->status === 'completed') {
                    $completedTasks[] = $task;
                }
            }

            $user->due_tasks = $dueTasks;
            $user->in_progress_tasks = $inProgressTasks;
            $user->completed_tasks = $completedTasks;

            return $user;
        });

        return $groupedUsers;
    }
}
