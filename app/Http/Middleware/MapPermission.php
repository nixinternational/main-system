<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MapPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $context): Response
    {
        $map = config("permissions.$context", []);
        if (empty($map)) {
            return $next($request);
        }

        $action = $request->route()?->getActionMethod();
        $slug = $map[$action] ?? null;

        if (!$slug) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user || !$user->hasPermission($slug)) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }

        return $next($request);
    }
}

