<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService)
    {
    }

    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'links' => [
                'home' => route('home'),
                'login' => route('login'),
                'register' => route('register'),
            ],
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $this->authenticationService->login($request);

        return redirect()->intended(
            $request->user()?->is_super_admin
                ? route('dashboard')
                : route('client.dashboard')
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->authenticationService->logout($request);

        return redirect()->route('login');
    }
}
