<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $currentWorkplace = $user && ! $user->is_super_admin
            ? $user->currentWorkplace()
            : null;

        return [
            ...parent::share($request),
            'app' => [
                'name' => config('app.name', 'Equipment Inspection'),
            ],
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_super_admin' => $user->is_super_admin,
                    'current_workplace' => $currentWorkplace ? [
                        'uuid' => $currentWorkplace->uuid,
                        'name' => $currentWorkplace->name,
                        'code' => $currentWorkplace->code,
                        'role' => $currentWorkplace->pivot?->role,
                    ] : null,
                ] : null,
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
            ],
            'navigation' => $user
                ? ($user->is_super_admin
                    ? [
                        'dashboard' => route('dashboard'),
                        'workplaces' => route('workplaces.index'),
                        'project_tracker' => route('project-tracker.index'),
                        'scanner' => route('scanner.index'),
                        'logout' => route('logout'),
                    ]
                    : [
                        'dashboard' => route('client.dashboard'),
                        'equipment' => route('client.equipment.index'),
                        'logout' => route('logout'),
                    ])
                : [],
        ];
    }
}
