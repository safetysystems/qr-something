<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PublicSiteController extends Controller
{
    public function __invoke(): Response
    {
        $user = request()->user();

        return Inertia::render('Public/Home', [
            'links' => [
                'home' => route('home'),
                'login' => $user ? null : route('login'),
                'register' => $user ? null : route('register'),
                'portal' => $user
                    ? route($user->is_super_admin ? 'dashboard' : 'client.dashboard')
                    : null,
            ],
        ]);
    }
}
