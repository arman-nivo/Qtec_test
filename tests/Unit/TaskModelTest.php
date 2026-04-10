<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_belongs_to_user()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** @test */
    public function can_check_if_task_is_overdue()
    {
        $overdueTask = Task::factory()->create([
            'due_date' => Carbon::yesterday(),
            'status' => 'pending',
        ]);

        $futureTask = Task::factory()->create([
            'due_date' => Carbon::tomorrow(),
            'status' => 'pending',
        ]);

        $this->assertTrue($overdueTask->isOverdue());
        $this->assertFalse($futureTask->isOverdue());
    }

    /** @test */
    public function completed_task_is_not_overdue()
    {
        $task = Task::factory()->create([
            'due_date' => Carbon::yesterday(),
            'status' => 'completed',
        ]);

        $this->assertFalse($task->isOverdue());
    }

    /** @test */
    public function can_scope_tasks_by_status()
    {
        $user = User::factory()->create();
        
        Task::factory()->count(3)->create(['user_id' => $user->id, 'status' => 'pending']);
        Task::factory()->count(2)->create(['user_id' => $user->id, 'status' => 'in_progress']);
        Task::factory()->count(1)->create(['user_id' => $user->id, 'status' => 'completed']);

        $this->assertEquals(3, Task::pending()->count());
        $this->assertEquals(2, Task::inProgress()->count());
        $this->assertEquals(1, Task::completed()->count());
    }

    /** @test */
    public function can_get_overdue_tasks()
    {
        $user = User::factory()->create();
        
        Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => Carbon::yesterday(),
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'due_date' => Carbon::tomorrow(),
            'status' => 'pending',
        ]);

        $this->assertEquals(1, Task::overdue()->count());
    }

    /** @test */
    public function status_color_attribute_returns_correct_color()
    {
        $pendingTask = Task::factory()->create(['status' => 'pending']);
        $inProgressTask = Task::factory()->create(['status' => 'in_progress']);
        $completedTask = Task::factory()->create(['status' => 'completed']);

        $this->assertEquals('gray', $pendingTask->status_color);
        $this->assertEquals('blue', $inProgressTask->status_color);
        $this->assertEquals('green', $completedTask->status_color);
    }

    /** @test */
    public function priority_color_attribute_returns_correct_color()
    {
        $lowTask = Task::factory()->create(['priority' => 'low']);
        $mediumTask = Task::factory()->create(['priority' => 'medium']);
        $highTask = Task::factory()->create(['priority' => 'high']);

        $this->assertEquals('green', $lowTask->priority_color);
        $this->assertEquals('yellow', $mediumTask->priority_color);
        $this->assertEquals('red', $highTask->priority_color);
    }
}