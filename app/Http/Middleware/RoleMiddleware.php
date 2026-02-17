<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // $rolesArray = explode(',',$roles);

        if (!in_array(auth()->user()->rol, $rolesArray)) {
            abort(403, 'No tienes permisos para acceder a esta sección');
        }

        return $next($request);
    }
}
