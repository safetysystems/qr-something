<?php

use App\Models\Equipment;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the workplace dashboard for a workplace user', function (): void {
    $user = User::factory()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'North Plant',
    ]);

    $workplace->users()->attach($user, ['role' => 'manager']);

    $this->actingAs($user)
        ->get(route('client.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Client/Dashboard')
            ->where('workplace.name', 'North Plant')
            ->where('auth.user.email', $user->email)
            ->where('auth.user.current_workplace.name', 'North Plant'));
});

it('renders workplace equipment for a workplace user', function (): void {
    $user = User::factory()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Warehouse 7',
    ]);

    $workplace->users()->attach($user, ['role' => 'inspector']);

    Equipment::query()->create([
        'uuid' => (string) Str::uuid(),
        'workplace_id' => $workplace->id,
        'name' => 'Lift A',
        'type' => 'Forklift',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('client.equipment.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Client/Equipment/Index')
            ->where('workplace.name', 'Warehouse 7')
            ->has('equipment.data', 1));
});

it('allows a workplace user to create equipment in the client portal', function (): void {
    $user = User::factory()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'South Plant',
    ]);

    $workplace->users()->attach($user->id, ['role' => 'owner']);

    $this->actingAs($user)
        ->post(route('client.equipment.store'), [
            'name' => 'Pump 9',
            'type' => 'Pump',
            'asset_code' => 'P-009',
            'serial_number' => 'SN-009',
            'manufacturer' => 'Flow Systems',
            'model' => 'FS-9',
            'location_label' => 'Zone A',
            'notes' => 'Installed by client team.',
        ])
        ->assertRedirect();

    $equipment = Equipment::query()->firstOrFail();

    expect($equipment->workplace_id)->toBe($workplace->id);
    expect($equipment->created_by)->toBe($user->id);

    $this->assertDatabaseHas('equipment', [
        'id' => $equipment->id,
        'name' => 'Pump 9',
        'workplace_id' => $workplace->id,
        'created_by' => $user->id,
    ]);
});

it('forbids super administrators from the workplace portal routes', function (): void {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('client.dashboard'))
        ->assertForbidden();
});

it('forbids authenticated users without workplace access from the workplace portal', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('client.dashboard'))
        ->assertForbidden();
});
