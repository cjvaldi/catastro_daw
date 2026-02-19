<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Propiedad;
use App\Models\Busqueda;
use App\Models\LogApi;

/**
 * Controlador del panel de administración
 * 
 * Gestiona todas las funcionalidades administrativas del sistema:
 * - Dashboard con estadísticas del sistema
 * - Gestión de usuarios (roles, activación/desactivación)
 * - Visualización de logs de API para auditoría
 * 
 * Acceso restringido exclusivamente a usuarios con rol 'admin'.
 * Protegido por middleware: ['auth', 'activo', 'role:admin']
 * 
 * @package App\Http\Controllers\Admin
 * @author Cristian Valdivieso
 * @version 1.0
 */
class AdminController extends Controller
{
    /**
     * Muestra el dashboard administrativo con estadísticas del sistema
     * 
     * Proporciona una vista general del estado del sistema con métricas clave:
     * - Total de usuarios registrados en la plataforma
     * - Usuarios con rol Premium (registrado) activos
     * - Número de propiedades guardadas por todos los usuarios
     * - Total de búsquedas realizadas en el sistema
     * 
     * Todas las estadísticas se calculan en tiempo real mediante consultas
     * a la base de datos.
     * 
     * @return \Illuminate\View\View Vista del dashboard con array de estadísticas
     * 
     * @example
     * // Retorna:
     * // [
     * //   'total_usuarios' => 150,
     * //   'usuarios_premium' => 45,
     * //   'propiedades_guardadas' => 320,
     * //   'busquedas_realizadas' => 1250
     * // ]
     */
    public function dashboard()
    {
        // Calcular métricas del sistema
        $stats = [
            'total_usuarios'        => User::count(),
            'usuarios_premium'      => User::where('rol', 'registrado')->count(),
            'propiedades_guardadas' => Propiedad::count(),
            'busquedas_realizadas'  => Busqueda::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Lista todos los usuarios del sistema con paginación
     * 
     * Muestra un listado completo de usuarios registrados ordenados por
     * fecha de creación (más recientes primero). Incluye información de:
     * - Datos básicos (nombre, email)
     * - Rol actual (visitante, registrado, admin)
     * - Estado de activación
     * - Fecha de registro
     * 
     * La paginación de 20 elementos optimiza el rendimiento y la experiencia
     * de usuario en sistemas con muchos usuarios.
     * 
     * @return \Illuminate\View\View Vista con listado paginado de usuarios
     */
    public function usuarios()
    {
        // Obtener todos los usuarios ordenados por más recientes
        $usuarios = User::latest()->paginate(20);
        
        return view('admin.usuarios', compact('usuarios'));
    }

    /**
     * Cambia el rol de un usuario entre Visitante y Premium (toggle)
     * 
     * Permite al administrador promover usuarios de Visitante a Premium
     * o degradarlos de Premium a Visitante. Los usuarios con rol 'admin'
     * NO pueden ser modificados (protección implementada en la vista).
     * 
     * Roles disponibles:
     * - 'visitante': Usuario gratuito con funciones básicas
     * - 'registrado': Usuario Premium con funciones avanzadas
     * 
     * NOTA: Los administradores no pueden cambiar el rol de otros administradores
     * por seguridad del sistema.
     * 
     * @param User $user Usuario a modificar (route model binding)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige con mensaje de confirmación
     */
    public function cambiarRol(User $user)
    {
        // Toggle: Si es visitante → Premium, si es Premium → Visitante
        $nuevoRol = $user->rol === 'visitante' ? 'registrado' : 'visitante';
        
        // Actualizar rol en la base de datos
        $user->update(['rol' => $nuevoRol]);

        // Mensaje descriptivo del cambio
        $rolDescriptivo = $nuevoRol === 'registrado' ? 'Premium' : 'Visitante';

        return back()->with('success', "Rol actualizado a: {$rolDescriptivo}");
    }

    /**
     * Activa o desactiva un usuario (toggle)
     * 
     * Permite al administrador bloquear temporalmente el acceso de un usuario
     * sin eliminar su cuenta. Los usuarios desactivados:
     * - No pueden iniciar sesión
     * - Son redirigidos automáticamente por el middleware CheckActivo
     * - Conservan todos sus datos (propiedades, favoritos, notas)
     * 
     * Casos de uso:
     * - Suspensión temporal por mal uso del sistema
     * - Gestión de usuarios problemáticos
     * - Cuentas en revisión
     * 
     * La reactivación restaura el acceso completo inmediatamente.
     * 
     * @param User $user Usuario a activar/desactivar (route model binding)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige con mensaje del estado actual
     */
    public function toggleActivo(User $user)
    {
        // Invertir estado actual: activo ↔ inactivo
        $user->update(['activo' => !$user->activo]);

        // Mensaje descriptivo del nuevo estado
        $estado = $user->activo ? 'activado' : 'desactivado';
        
        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    /**
     * Muestra los logs de llamadas a la API del Catastro
     * 
     * Proporciona una vista de auditoría completa de todas las interacciones
     * con la API externa del Catastro, incluyendo:
     * - Usuario que realizó la consulta (si está autenticado)
     * - Endpoint consultado (Consulta_DNPRC o Consulta_DNPLOC)
     * - Parámetros enviados en la petición
     * - Código de respuesta HTTP (200, 404, 500, etc.)
     * - Duración de la llamada en milisegundos
     * - Errores detectados (si los hay)
     * 
     * Ordenado de más reciente a más antiguo, con paginación de 50 elementos.
     * 
     * Usos:
     * - Monitoreo de rendimiento de la API
     * - Detección de patrones de error
     * - Auditoría de uso del sistema
     * - Análisis de consultas más frecuentes
     * 
     * @return \Illuminate\View\View Vista con logs paginados y relación usuario cargada
     */
    public function logs()
    {
        // Obtener logs con relación de usuario (eager loading para optimización)
        $logs = LogApi::with('usuario')
            ->latest()
            ->paginate(50);

        return view('admin.logs', compact('logs'));
    }
}