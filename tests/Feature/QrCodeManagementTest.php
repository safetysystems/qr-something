<?php

use App\Models\Equipment;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

it('generates and streams a stored qr code for equipment', function (): void {
    Storage::fake('local');

    $admin = User::factory()->superAdmin()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'QR Test Site',
    ]);

    $this->actingAs($admin)->postJson(route('workplaces.equipment.store', $workplace), [
        'name' => 'Valve 2',
        'type' => 'Valve',
        'asset_code' => 'VAL-2',
    ])->assertCreated();

    $equipment = Equipment::query()->firstOrFail()->fresh();

    expect($equipment->qr_code_path)->not->toBeNull();
    expect($equipment->qr_code_generated_at)->not->toBeNull();

    Storage::disk('local')->assertExists($equipment->qr_code_path);

    $response = $this->actingAs($admin)->get(route('equipment.qr-code.show', $equipment));

    $response->assertOk();

    expect($response->headers->get('content-type'))->toContain('image/svg+xml');
    expect($response->getContent())->toContain('<svg');
    expect($response->getContent())->toBe(Storage::disk('local')->get($equipment->qr_code_path));
});

it('renders the scanner as an inertia page for administrators', function (): void {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('scanner.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Scan/Index'));
});
