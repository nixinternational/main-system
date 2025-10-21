<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckIp
{
    /**
     * Lista de IPs e redes permitidas
     */
    protected $whitelist = [
        '177.200.46.206', // IP específico,
        '179.95.91.8',
        '127.0.0.1',      // localhost
        '::1',            // IPv6 localhost
        '192.168.0.0/16', // Rede local 192.168.x.x
        '10.0.0.0/8',     // Rede local 10.x.x.x
        '172.16.0.0/12',  // Rede local 172.16.x.x - 172.31.x.x
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Permite acesso total em ambiente local/desenvolvimento
        if (app()->environment('local', 'development')) {
            return $next($request);
        }

        $clientIp = $request->ip();
        
        // Verifica se o IP está na whitelist
        if (!$this->isIpAllowed($clientIp)) {
            Log::warning("Tentativa de acesso não autorizado do IP: " . $clientIp);
            abort(403, 'Acesso negado. IP não autorizado: ' . $clientIp);
        }

        return $next($request);
    }

    /**
     * Verifica se o IP está na lista de permitidos
     */
    protected function isIpAllowed($ip): bool
    {
        foreach ($this->whitelist as $allowed) {
            if ($this->checkIp($ip, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o IP corresponde a um IP ou rede específica
     */
    protected function checkIp($ip, $range): bool
    {
        // Se for um IP exato
        if ($ip === $range) {
            return true;
        }

        // Se for uma rede no formato CIDR (ex: 192.168.0.0/16)
        if (strpos($range, '/') !== false) {
            list($subnet, $bits) = explode('/', $range);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            $subnet &= $mask;
            
            return ($ip & $mask) == $subnet;
        }

        return false;
    }
}