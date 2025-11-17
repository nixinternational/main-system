<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckPermission
{

    public function handle(Request $request, Closure $next, string $permissao)
    {
        $user = Auth::user();
        if ($user === null) {
            if ($request->getRequestUri() === '/painel/login') {
                return $next($request);
            }
            abort(401, 'Login Expirado!');
        }

        if ($user->isSuperUser()) {
            return $next($request);
        }

        $permissoes = array_filter(array_map('trim', explode('|', $permissao)));

        foreach ($permissoes as $perm) {
            if ($user->hasPermission($perm) || $user->hasRole($perm)) {
                return $next($request);
            }
        }

        abort(Response::HTTP_FORBIDDEN, 'Você não tem permissão para acessar esta página!');

    }
}
