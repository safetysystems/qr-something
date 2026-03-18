<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the project tracker for super administrators', function (): void {
    $admin = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('project-tracker.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProjectTracker/Index')
            ->where('stats.total', 16));
});
