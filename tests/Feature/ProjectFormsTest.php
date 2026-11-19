<?php

use App\Models\Contract;
use App\Models\Employee;
use App\Models\Party;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;

function admin(): User
{
    return User::factory()->create(['role' => 'admin', 'user_type' => 'admin']);
}

function contract(string $status = 'approved'): Contract
{
    $party = fn (string $type) => Party::create(['entity_type' => $type, 'name' => ucfirst($type), 'street' => 's', 'barangay' => 'b',
        'city' => 'c', 'state' => 'st', 'postal' => '1000', 'phone' => '0917']);

    return Contract::create(['contractor_id' => $party('contractor')->id, 'client_id' => $party('client')->id, 'contract_number' => 'CT-' . uniqid(),
        'title' => 'Lobby', 'total_amount' => 10000, 'start_date' => now(), 'end_date' => now()->addMonth(),
        'payment_method' => 'cash', 'payment_terms' => 'Net 30', 'status' => $status]);
}

function project(): Project
{
    $u = User::factory()->create();

    return Project::create(['project_number' => 'PRJ-' . uniqid(), 'contract_id' => contract()->id, 'name' => 'Lobby Renovation',
        'start_date' => now(), 'end_date' => now()->addMonth(), 'project_manager_id' => $u->id, 'client_representative_id' => $u->id,
        'budget' => 5000, 'status' => 'active']);
}

test('a project can be created from an approved contract and edited', function () {
    $admin = admin();
    $c = contract();

    $this->actingAs($admin)->get('/projects/create')->assertOk()->assertSee($c->contract_number);

    $this->actingAs($admin)->post('/projects', [
        'contract_id' => $c->id, 'name' => 'Lobby Renovation', 'start_date' => now()->toDateString(), 'end_date' => now()->addMonth()->toDateString(),
        'project_manager_id' => $admin->id, 'client_representative_id' => $admin->id, 'budget' => 5000,
    ])->assertRedirect();

    $project = Project::firstOrFail();
    expect($project->status)->toBe('pending'); // was 'proposed', which the DB enum rejects

    $this->actingAs($admin)->get("/projects/{$project->id}/edit")->assertOk()->assertSee('value="Lobby Renovation"', false);
    $this->actingAs($admin)->put("/projects/{$project->id}", [
        'name' => 'Lobby Renovation 2', 'status' => 'active', 'start_date' => now()->toDateString(), 'end_date' => now()->addMonth()->toDateString(),
        'project_manager_id' => $admin->id, 'client_representative_id' => $admin->id, 'budget' => 6000,
    ])->assertSessionHasNoErrors();
    expect($project->fresh()->status)->toBe('active');

    // the contract now has a project, so it's no longer offered
    $this->actingAs($admin)->get('/projects/create')->assertDontSee($c->contract_number);
});

test('project tasks: create, edit, and project progress follows the tasks', function () {
    $admin = admin();
    $project = project();
    $dates = ['start_date' => now()->toDateString(), 'due_date' => now()->addWeek()->toDateString()];

    $this->actingAs($admin)->get("/projects/{$project->id}/tasks/create")->assertOk();
    $this->actingAs($admin)->post("/projects/{$project->id}/tasks", ['name' => 'Paint', 'priority' => 'urgent'] + $dates)->assertRedirect();
    $task = ProjectTask::firstOrFail();

    $this->actingAs($admin)->get("/projects/{$project->id}/tasks/{$task->id}/edit")->assertOk()->assertSee('value="Paint"', false);
    $this->actingAs($admin)->put("/projects/{$project->id}/tasks/{$task->id}", ['name' => 'Paint', 'priority' => 'high', 'status' => 'in_progress', 'progress' => 60] + $dates)
        ->assertSessionHasNoErrors();

    expect($project->fresh()->progress)->toBe(60);
});

test('project timeline: tasks save with real columns and progress syncs', function () {
    $admin = admin();
    $project = project();
    $dates = ['start_date' => now()->toDateString(), 'due_date' => now()->addWeek()->toDateString()];

    $this->actingAs($admin)->get('/project-timeline/create')->assertOk()->assertSee('Lobby Renovation');
    $this->actingAs($admin)->post('/project-timeline', ['project_id' => $project->id, 'name' => 'Tile floor', 'priority' => 'medium'] + $dates)
        ->assertRedirect(route('project-timeline.index'));

    $task = ProjectTask::where('name', 'Tile floor')->firstOrFail();
    expect($task->project_id)->toBe($project->id)->and($task->contract_id)->toBe($project->contract_id);

    $this->actingAs($admin)->get("/project-timeline/{$task->id}")->assertOk()->assertSee('Tile floor');
    // the calendar builds events from tasks (used to read non-existent title/end_date columns)
    $this->actingAs($admin)->get('/project-timeline')->assertOk();
    $this->actingAs($admin)->get("/project-timeline/{$task->id}/edit")->assertOk();
    $this->actingAs($admin)->put("/project-timeline/{$task->id}/progress", ['progress' => 100]);

    expect($task->fresh()->status)->toBe('completed')->and($project->fresh()->progress)->toBe(100);
});

test('employee form: contractor fields are required only for contractors, edit keeps data', function () {
    $admin = admin();
    $base = ['first_name' => 'Ana', 'last_name' => 'Cruz', 'email' => 'ana@x.test', 'username' => 'ana', 'password' => 'Secret123!', 'password_confirmation' => 'Secret123!'];

    $this->actingAs($admin)->get('/information-management/create')->assertOk();
    $this->actingAs($admin)->post('/information-management', $base + ['role' => 'contractor'])->assertSessionHasErrors(['phone', 'street']);
    $this->actingAs($admin)->post('/information-management', $base + ['role' => 'procurement'])->assertSessionHasNoErrors();

    $employee = Employee::firstOrFail();
    $this->actingAs($admin)->get("/information-management/{$employee->id}/edit")->assertOk()->assertSee('value="Ana"', false);
    $this->actingAs($admin)->put("/information-management/{$employee->id}", ['first_name' => 'Ana', 'last_name' => 'Reyes', 'email' => 'ana@x.test', 'username' => 'ana', 'role' => 'finance'])
        ->assertSessionHasNoErrors();
    expect($employee->fresh()->last_name)->toBe('Reyes')->and($employee->user->fresh()->role)->toBe('finance');
});
