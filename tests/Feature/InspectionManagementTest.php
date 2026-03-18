<?php

use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionTemplate;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Support\Str;

it('creates an inspection template with checklist items', function (): void {
    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'North Yard',
    ]);

    $response = $this->actingAs($admin)->postJson(route('workplaces.inspection-templates.store', $workplace), [
        'name' => 'Daily Safety Check',
        'description' => 'Used before equipment enters the active shift.',
        'items' => [
            [
                'label' => 'Visual exterior condition',
                'instructions' => 'Look for obvious damage before use.',
                'is_required' => true,
            ],
            [
                'label' => 'Controls respond correctly',
                'instructions' => null,
                'is_required' => true,
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Daily Safety Check')
        ->assertJsonPath('data.items.0.label', 'Visual exterior condition')
        ->assertJsonPath('data.items.1.label', 'Controls respond correctly');

    $template = InspectionTemplate::query()->firstOrFail();

    expect($template->items()->count())->toBe(2);
});

it('creates an inspection and marks it failed when any checklist item fails', function (): void {
    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'South Yard',
    ]);

    $equipment = Equipment::query()->create([
        'uuid' => (string) Str::uuid(),
        'workplace_id' => $workplace->id,
        'name' => 'Lift B',
        'type' => 'Forklift',
        'created_by' => $admin->id,
    ]);

    $template = $workplace->inspectionTemplates()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Pre-Use Inspection',
        'description' => 'Run before the shift starts.',
        'created_by' => $admin->id,
    ]);

    $firstItem = $template->items()->create([
        'label' => 'Horn working',
        'sort_order' => 1,
        'is_required' => true,
    ]);

    $secondItem = $template->items()->create([
        'label' => 'Brakes responsive',
        'sort_order' => 2,
        'is_required' => true,
    ]);

    $response = $this->actingAs($admin)->postJson(route('equipment.inspections.store', $equipment), [
        'inspection_template_uuid' => $template->uuid,
        'inspected_at' => now()->toDateTimeString(),
        'notes' => 'Brake issue found during startup.',
        'responses' => [
            [
                'inspection_template_item_id' => $firstItem->id,
                'response' => 'pass',
                'notes' => null,
            ],
            [
                'inspection_template_item_id' => $secondItem->id,
                'response' => 'fail',
                'notes' => 'Pedal travel is inconsistent.',
            ],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.status', 'failed')
        ->assertJsonPath('data.template.name', 'Pre-Use Inspection')
        ->assertJsonPath('data.responses.1.response', 'fail');

    $inspection = Inspection::query()->firstOrFail();

    $this->assertDatabaseHas('inspections', [
        'id' => $inspection->id,
        'status' => 'failed',
        'equipment_id' => $equipment->id,
        'workplace_id' => $workplace->id,
    ]);

    expect($inspection->responses()->count())->toBe(2);
});

it('prevents editing a template once inspections have been submitted against it', function (): void {
    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Locked Template Site',
    ]);

    $equipment = Equipment::query()->create([
        'uuid' => (string) Str::uuid(),
        'workplace_id' => $workplace->id,
        'name' => 'Pump 12',
        'type' => 'Pump',
        'created_by' => $admin->id,
    ]);

    $template = $workplace->inspectionTemplates()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Weekly Pump Check',
        'created_by' => $admin->id,
    ]);

    $item = $template->items()->create([
        'label' => 'Pressure level',
        'sort_order' => 1,
        'is_required' => true,
    ]);

    $inspection = Inspection::query()->create([
        'uuid' => (string) Str::uuid(),
        'workplace_id' => $workplace->id,
        'equipment_id' => $equipment->id,
        'inspection_template_id' => $template->id,
        'user_id' => $admin->id,
        'status' => 'passed',
        'inspected_at' => now(),
    ]);

    $inspection->responses()->create([
        'inspection_template_item_id' => $item->id,
        'response' => 'pass',
        'notes' => null,
    ]);

    $response = $this->actingAs($admin)->putJson(route('inspection-templates.update', $template), [
        'name' => 'Updated Pump Check',
        'description' => 'Attempted edit after use.',
        'items' => [
            [
                'label' => 'Updated item',
                'instructions' => null,
                'is_required' => true,
            ],
        ],
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);

    $this->assertDatabaseHas('inspection_templates', [
        'id' => $template->id,
        'name' => 'Weekly Pump Check',
    ]);

    expect($template->fresh()->items()->count())->toBe(1);
    expect($inspection->fresh()->responses()->count())->toBe(1);
});
