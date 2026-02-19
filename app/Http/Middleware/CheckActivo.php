<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de verificación de estado activo del usuario
 * 
 * Protege la aplicación contra acceso de usuarios desactivados por el administrador.
 * Este middleware se ejecuta en TODAS las rutas protegidas por autenticación para
 * garantizar que solo usuarios activos puedan acceder al sistema.
 * 
 * Funcionalidad:
 * - Verifica el campo 'activo' del usuario autenticado
 * - Si está desactivado (activo = false): Cierra sesión y redirige a login
 * - Si está activo: Permite el acceso normal
 * 
 * Casos de uso:
 * - Suspensión temporal de usuarios por mal uso
 * - Bloqueo de cuentas sospechosas
 * - Gestión administrativa de acceso sin eliminar datos
 * 
 * Flujo de seguridad:
 * 1. Usuario desactivado intenta acceder
 * 2. Middleware detecta estado inactivo
 * 3. Cierra sesión automáticamente (logout)
 * 4. Invalida sesión actual
 * 5. Regenera token CSRF (prevención de ataques)
 * 6. Redirige a login con mensaje de error
 * 
 * Uso en routes/web.php:
 * Route::middleware(['auth', 'activo'])->group(function () {
 *     // Todas estas rutas verifican que el usuario esté activo
 * });
 * 
 * @package App\Http\Middleware
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @see \App\Models\User::$activo Campo booleano que controla el estado
 * @see \App\Http\Controllers\Admin\AdminController::toggleActivo() Método admin para cambiar estado
 */
class CheckActivo
{
    /**
     * Maneja una solicitud HTTP y verifica el estado activo del usuario
     * 
     * Este método se ejecuta en cada petición a rutas protegidas.
     * Realiza una verificación de seguridad para garantizar que usuarios
     * desactivados no puedan mantener sesiones activas.
     * 
     * Proceso de verificación:
     * 1. Verifica si hay un usuario autenticado
     * 2. Verifica si el campo 'activo' es false
     * 3. Si está desactivado:
     *    a. Cierra la sesión actual (auth()->logout())
     *    b. Invalida la sesión (previene reutilización)
     *    c. Regenera token CSRF (seguridad adicional)
     *    d. Redirige a login con mensaje explicativo
     * 4. Si está activo o no hay usuario: Continúa normalmente
     * 
     * IMPORTANTE: Este middleware debe ejecutarse DESPUÉS del middleware 'auth'
     * pero ANTES de verificar roles, para asegurar que usuarios inactivos no
     * puedan acceder bajo ninguna circunstancia.
     * 
     * @param Request $request Solicitud HTTP entrante
     * @param Closure $next Siguiente middleware o controlador en la cadena
     * 
     * @return Response Respuesta HTTP (redirect o next)
     * 
     * @example
     * // Usuario desactivado intenta acceder a /dashboard:
     * // 1. auth() verifica que está autenticado ✓
     * // 2. CheckActivo detecta activo=false ✗
     * // 3. Cierra sesión y redirige a /login con mensaje de error
     * 
     * @example
     * // En routes/web.php:
     * Route::middleware(['auth', 'activo'])->group(function () {
     *     Route::get('/dashboard', [DashboardController::class, 'index']);
     *     Route::get('/propiedades', [PropiedadController::class, 'index']);
     * });
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si existe un usuario autenticado Y si está desactivado
        if (auth()->check() && !auth()->user()->activo) {
            
            // Usuario desactivado detectado → Cerrar sesión completa
            
            // 1. Cerrar sesión del usuario
            auth()->logout();
            
            // 2. Invalidar datos de sesión actual (por seguridad)
            $request->session()->invalidate();
            
            // 3. Regenerar token CSRF para prevenir ataques de sesión
            $request->session()->regenerateToken();

            // 4. Redirigir a login con mensaje informativo
            return redirect()->route('login')
                ->with('error', 'Tu cuenta ha sido desactivada. Contacta con el administrador.');
        }

        // Usuario activo o no autenticado → Permitir continuar
        return $next($request);
    }
}