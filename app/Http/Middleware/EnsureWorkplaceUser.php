<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWorkplaceUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user
            && ! $user->is_super_admin
            && $user->workplaces()->whereNull('workplaces.archived_at')->exists(),
            Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
