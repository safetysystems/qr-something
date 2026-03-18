<?php

use App\Models\Equipment;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Support\Str;

it('creates a workplace through the admin endpoint', function (): void {
    $admin = User::factory()->superAdmin()->create();

    $response = $this->actingAs($admin)->postJson(route('workplaces.store'), [
        'name' => 'North Plant',
        'code' => 'north-plant',
        'primary_contact_name' => 'Ava Stone',
        'primary_contact_email' => 'ava@example.com',
        'primary_contact_phone' => '+63 900 000 0000',
        'address_line_1' => 'Warehouse District',
        'city' => 'Manila',
        'country' => 'Philippines',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'North Plant')
        ->assertJsonPath('data.primary_contact.email', 'ava@example.com');

    $this->assertDatabaseHas('workplaces', [
        'name' => 'North Plant',
        'code' => 'north-plant',
    ]);
});

it('creates equipment under a workplace and exposes its uuid route', function (): void {
    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'South Depot',
    ]);

    $response = $this->actingAs($admin)->postJson(route('workplaces.equipment.store', $workplace), [
        'name' => 'Forklift A',
        'type' => 'Forklift',
        'asset_code' => 'FL-A',
        'serial_number' => 'SER-1001',
        'manufacturer' => 'LiftWorks',
        'model' => 'LX-4',
        'location_label' => 'Bay 4',
        'notes' => 'Ready for first inspection cycle.',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Forklift A')
        ->assertJsonPath('data.workplace.name', 'South Depot');

    $equipment = Equipment::query()->firstOrFail();

    $this->actingAs($admin)
        ->getJson(route('equipment.show', $equipment))
        ->assertOk()
        ->assertJsonPath('data.uuid', $equipment->uuid)
        ->assertJsonPath('data.workplace.name', 'South Depot');
});

it('archives workplace equipment when a workplace is archived', function (): void {
    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Archive Test Site',
    ]);

    $equipment = Equipment::query()->create([
        'uuid' => (string) Str::uuid(),
        'workplace_id' => $workplace->id,
        'name' => 'Pump 7',
        'type' => 'Pump',
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->deleteJson(route('workplaces.destroy', $workplace))
        ->assertNoContent();

    expect($workplace->fresh()->archived_at)->not->toBeNull();
    expect($equipment->fresh()->archived_at)->not->toBeNull();
});

it('forbids non administrators from creating workplaces', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('workplaces.store'), [
            'name' => 'Blocked Site',
        ])
        ->assertForbidden();
});
