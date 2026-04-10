<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskForm extends Component
{
    public $taskId;
    public $title = '';
    public $description = '';
    public $status = 'pending';
    public $priority = 'medium';
    public $due_date = '';
    public $isEditMode = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'status' => 'required|in:pending,in_progress,completed',
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date|after_or_equal:today',
    ];

    protected $messages = [
        'title.required' => 'Task title is required.',
        'title.max' => 'Task title cannot exceed 255 characters.',
        'due_date.after_or_equal' => 'Due date must be today or a future date.',
    ];

    public function mount($taskId = null)
    {
        if ($taskId) {
            $this->isEditMode = true;
            $this->loadTask($taskId);
        }
    }

    public function loadTask($taskId)
    {
        $task = Task::where('id', $taskId)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $this->taskId = $task->id;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->status = $task->status;
        $this->priority = $task->priority;
        $this->due_date = $task->due_date?->format('Y-m-d');
    }

    public function save()
    {
        $this->validate();

        if ($this->isEditMode) {
            $task = Task::where('id', $this->taskId)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();
            
            $task->update([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'priority' => $this->priority,
                'due_date' => $this->due_date,
            ]);

            session()->flash('success', 'Task updated successfully.');
        } else {
            Task::create([
                'user_id' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'priority' => $this->priority,
                'due_date' => $this->due_date,
            ]);

            session()->flash('success', 'Task created successfully.');
        }

        return redirect()->route('tasks.index');
    }

    public function render()
    {
        return view('livewire.task-form')->layout('layouts.app');  // ← ADD THIS
    }
}