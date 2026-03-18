<?php

use App\Models\User;
use App\Models\Workplace;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Str;

it('displays the login screen', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

it('authenticates a super administrator', function (): void {
    $user = User::factory()->superAdmin()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);

    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('authenticates a workplace client user', function (): void {
    $user = User::factory()->create();
    $workplace = Workplace::query()->create([
        'uuid' => (string) Str::uuid(),
        'name' => 'Client Site',
    ]);

    $workplace->users()->attach($user, ['role' => 'manager']);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('client.dashboard'));

    $this->assertAuthenticatedAs($user);

    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('rejects accounts without platform access', function (): void {
    $user = User::factory()->create();

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('requires authentication for the dashboard', function (): void {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('forbids non administrators from the dashboard', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertForbidden();
});

it('renders the dashboard as an inertia page for administrators', function (): void {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Dashboard/Index'));
});
