<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public function login(LoginRequest $request): void
    {
        $request->authenticate();
        $this->establishSession($request, $request->user());
    }

    public function loginUser(Request $request, User $user): void
    {
        Auth::login($user);

        $this->establishSession($request, $user);
    }

    public function logout(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function establishSession(Request $request, ?User $user): void
    {
        $request->session()->regenerate();

        $user?->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();
    }
}
