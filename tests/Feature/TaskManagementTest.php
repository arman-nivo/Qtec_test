<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use Livewire\Livewire;
use App\Livewire\TaskList;
use App\Livewire\TaskForm;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_tasks_page()
    {
        $response = $this->actingAs($this->user)->get(route('tasks.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_tasks_page()
    {
        $response = $this->get(route('tasks.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_create_a_task()
    {
        Livewire::actingAs($this->user)
            ->test(TaskForm::class)
            ->set('title', 'New Test Task')
            ->set('description', 'This is a test task description')
            ->set('status', 'pending')
            ->set('priority', 'high')
            ->set('due_date', now()->addDays(7)->format('Y-m-d'))
            ->call('save')
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Test Task',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function task_title_is_required()
    {
        Livewire::actingAs($this->user)
            ->test(TaskForm::class)
            ->set('title', '')
            ->call('save')
            ->assertHasErrors(['title' => 'required']);
    }

    /** @test */
    public function user_can_update_a_task()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Original Title',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskForm::class, ['taskId' => $task->id])
            ->set('title', 'Updated Title')
            ->call('save')
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function user_can_delete_their_own_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('deleteTask', $task->id);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('deleteTask', $task->id);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_can_update_task_status()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->call('updateStatus', $task->id, 'completed');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function user_can_filter_tasks_by_status()
    {
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);
        Task::factory()->create(['user_id' => $this->user->id, 'status' => 'completed']);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->set('statusFilter', 'pending')
            ->assertSee('pending')
            ->assertViewHas('tasks', function ($tasks) {
                return $tasks->every(fn($task) => $task->status === 'pending');
            });
    }

    /** @test */
    public function user_can_search_tasks()
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Unique Task Title',
        ]);
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Another Task',
        ]);

        Livewire::actingAs($this->user)
            ->test(TaskList::class)
            ->set('search', 'Unique')
            ->assertSee('Unique Task Title')
            ->assertDontSee('Another Task');
    }

    /** @test */
    public function due_date_must_be_future_date()
    {
        Livewire::actingAs($this->user)
            ->test(TaskForm::class)
            ->set('title', 'Test Task')
            ->set('due_date', now()->subDays(1)->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['due_date']);
    }
}