<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (
            !$user ||
            !$user->activo ||
            !$user->tieneRol(...$roles)
        ) {
            abort(403, 'No tienes permiso para realizar esta operación.');
        }

        return $next($request);
    }
}
