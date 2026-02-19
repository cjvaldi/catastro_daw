<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de control de acceso basado en roles
 * 
 * Protege rutas según los roles de usuario definidos en el sistema.
 * Verifica que el usuario autenticado tenga al menos uno de los roles
 * requeridos para acceder a un recurso específico.
 * 
 * Sistema de roles implementado:
 * - 'visitante': Usuario registrado gratuito con funciones básicas
 * - 'registrado': Usuario Premium con funciones avanzadas
 * - 'admin': Administrador con acceso total al sistema
 * 
 * Uso en rutas:
 * Route::middleware(['auth', 'role:registrado,admin'])->group(function () {
 *     // Rutas accesibles solo por Premium y Admin
 * });
 * 
 * @package App\Http\Middleware
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @see \App\Models\User::hasRole() Método que verifica los roles
 */
class RoleMiddleware
{
    /**
     * Maneja una solicitud HTTP y verifica permisos de rol
     * 
     * Flujo de verificación:
     * 1. Verifica que el usuario esté autenticado
     *    - Si no → Redirige a login
     * 
     * 2. Verifica que el usuario tenga al menos uno de los roles requeridos
     *    - Si no → Retorna error 403 Forbidden
     * 
     * 3. Si todo es correcto → Permite el acceso
     * 
     * El middleware acepta múltiples roles separados por coma, permitiendo
     * acceso si el usuario tiene AL MENOS UNO de ellos (operador OR).
     * 
     * @param Request $request Solicitud HTTP entrante
     * @param Closure $next Siguiente middleware o controlador en la cadena
     * @param string ...$roles Roles permitidos (variadic parameter: acepta 1 o más)
     * 
     * @return Response Respuesta HTTP (redirect, abort, o next)
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Error 403 si no tiene permisos
     * 
     * @example
     * // En routes/web.php:
     * 
     * // Ruta accesible solo por Premium y Admin
     * Route::middleware(['auth', 'role:registrado,admin'])->group(function () {
     *     Route::get('/favoritos', [PropiedadController::class, 'favoritos']);
     * });
     * 
     * // Ruta accesible SOLO por Admin
     * Route::middleware(['auth', 'role:admin'])->group(function () {
     *     Route::get('/admin/usuarios', [AdminController::class, 'usuarios']);
     * });
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            // Usuario no autenticado → Redirigir a login
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }

        // Verificar que el usuario tenga al menos uno de los roles requeridos
        if (!auth()->user()->hasRole($roles)) {
            // Usuario autenticado pero sin permisos suficientes → Error 403
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        // Usuario autenticado y con rol válido → Permitir acceso
        return $next($request);
    }
}