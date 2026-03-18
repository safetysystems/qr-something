<?php

use App\Models\User;
use App\Models\Workplace;
use Inertia\Testing\AssertableInertia as Assert;

it('displays the registration screen', function (): void {
    $this->get(route('register'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Register'));
});

it('registers a workplace owner account and provisions the workplace', function (): void {
    $response = $this->post(route('register.store'), [
        'name' => 'Ava Stone',
        'email' => 'ava@example.com',
        'workplace_name' => 'North Plant',
        'password' => 'StrongPass!123',
        'password_confirmation' => 'StrongPass!123',
    ]);

    $response->assertRedirect(route('client.dashboard'));

    $user = User::query()->where('email', 'ava@example.com')->firstOrFail();
    $workplace = Workplace::query()->firstOrFail();

    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('workplaces', [
        'id' => $workplace->id,
        'name' => 'North Plant',
        'code' => 'north-plant',
        'primary_contact_name' => 'Ava Stone',
        'primary_contact_email' => 'ava@example.com',
    ]);
    $this->assertDatabaseHas('workplace_user', [
        'user_id' => $user->id,
        'workplace_id' => $workplace->id,
        'role' => 'owner',
    ]);
});
