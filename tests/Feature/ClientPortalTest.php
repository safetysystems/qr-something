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
