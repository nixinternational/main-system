<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $temPermissaoAdmin = $user?->hasPermission('admin');

        if (!$user || (!$user->isSuperUser() && !$temPermissaoAdmin)) {
            abort(403, 'Você não tem permissão para gerenciar usuários.');
        }

        return $next($request);
    }
}

