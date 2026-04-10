<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $priorityFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'priorityFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        if ($task->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $task->delete();
        session()->flash('success', 'Task deleted successfully.');
    }

    public function updateStatus($taskId, $newStatus)
    {
        $task = Task::findOrFail($taskId);
        
        if ($task->user_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $task->update(['status' => $newStatus]);
        session()->flash('success', 'Task status updated successfully.');
    }

    public function render()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter !== 'all', function($query) {
                $query->where('priority', $this->priorityFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $stats = [
            'total' => Task::where('user_id', Auth::id())->count(),
            'pending' => Task::where('user_id', Auth::id())->pending()->count(),
            'in_progress' => Task::where('user_id', Auth::id())->inProgress()->count(),
            'completed' => Task::where('user_id', Auth::id())->completed()->count(),
            'overdue' => Task::where('user_id', Auth::id())->overdue()->count(),
        ];

        return view('livewire.task-list', [
            'tasks' => $tasks,
            'stats' => $stats,
        ])->layout('layouts.app');  // ← ADD THIS
    }
}