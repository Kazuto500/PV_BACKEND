<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $allowedRoles = explode('|', $role);

        if (!auth()->check() || !in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Acceso no autorizado'); // O redireccionar a una página de acceso denegado
        }

        return $next($request);
    }
}
