<?php

use App\Models\User;
use App\Models\Workplace;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Str;

test('the public website renders for guests', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Public/Home'));
});

test('the public website remains accessible to super administrators', function () {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('links.portal', route('dashboard')));
});

test('the public website remains accessible to workplace clients', function () {
    $user = User::factory()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'North Plant',
    ]);

    $workplace->users()->attach($user->id, ['role' => 'owner']);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Public/Home')
            ->where('links.portal', route('client.dashboard')));
});
