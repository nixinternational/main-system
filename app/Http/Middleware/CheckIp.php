<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIp
{
    /**
     * Lista de IPs permitidos
     */
    protected $whitelist = [
        '177.200.46.206', // substitua pelo IP real da cliente
        // você pode adicionar mais IPs aqui
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Só aplica a validação no ambiente de produção
        if (app()->environment('production')) {
            $ip = $request->ip();

            if (!in_array($ip, $this->whitelist)) {
                abort(403, 'Acesso negado.');
            }
        }

        return $next($request);
    }
}
