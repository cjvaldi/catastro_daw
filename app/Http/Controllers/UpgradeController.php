<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * Controlador de actualización a cuenta Premium
 * 
 * Gestiona el proceso de upgrade gratuito de usuarios Visitante a Premium.
 * En este proyecto académico, el upgrade es gratuito e instantáneo para
 * facilitar las pruebas y demostración de funcionalidades.
 * 
 * En un entorno de producción real, este controlador incluiría:
 * - Integración con pasarela de pago (Stripe, PayPal)
 * - Verificación de transacciones
 * - Gestión de suscripciones
 * - Renovaciones automáticas
 * 
 * Funcionalidades Premium desbloqueadas:
 * - Búsqueda por dirección postal
 * - Sistema de favoritos
 * - Notas privadas y públicas en propiedades
 * - Filtro avanzado "Solo Favoritas"
 * 
 * @package App\Http\Controllers
 * @author Cristian Valdivieso
 * @version 1.0
 */
class UpgradeController extends Controller
{
    /**
     * Muestra la página informativa de upgrade a Premium
     * 
     * Presenta al usuario las ventajas y funcionalidades adicionales
     * que obtendrá al actualizar su cuenta a Premium. Si el usuario
     * ya tiene acceso Premium (rol 'registrado' o 'admin'), se le
     * redirige al dashboard con un mensaje informativo.
     * 
     * Validaciones:
     * - Usuario ya Premium → Redirige a dashboard (evita upgrade duplicado)
     * - Usuario Visitante → Muestra página de upgrade
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     *         Vista de upgrade o redirect si ya es Premium
     * 
     * @example
     * // Usuario Visitante accede a /upgrade
     * // → Ve página con beneficios Premium y botón "Actualizar"
     * 
     * @example
     * // Usuario Premium accede a /upgrade
     * // → Redirige a /dashboard con mensaje "Ya tienes acceso Premium"
     */
    public function show()
    {
        // Verificar si el usuario ya tiene acceso Premium
        if (auth()->user()->isPremium()) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tienes acceso Premium.');
        }

        // Mostrar página de upgrade con beneficios
        return view('upgrade.show');
    }

    /**
     * Procesa el upgrade de usuario Visitante a Premium
     * 
     * Actualiza el rol del usuario autenticado de 'visitante' a 'registrado'
     * (Premium) de forma gratuita e instantánea. Este es un upgrade académico
     * sin procesamiento de pagos.
     * 
     * Proceso:
     * 1. Obtiene el usuario autenticado
     * 2. Actualiza el campo 'rol' a User::ROLE_REGISTRADO
     * 3. Persiste el cambio en la base de datos
     * 4. Redirige al dashboard con mensaje de bienvenida
     * 
     * Cambios efectivos inmediatos:
     * - Acceso a búsqueda por dirección
     * - Habilitación de sistema de favoritos
     * - Acceso a sistema de notas
     * - Filtros avanzados en listados
     * 
     * NOTA: En producción real, este método incluiría:
     * - Validación de pago recibido
     * - Creación de registro de transacción
     * - Envío de email de confirmación
     * - Actualización de fecha de suscripción
     * 
     * @param Request $request Solicitud HTTP (actualmente sin parámetros necesarios)
     * 
     * @return \Illuminate\Http\RedirectResponse Redirige a dashboard con mensaje de éxito
     * 
     * @example
     * // Usuario Visitante hace clic en "Actualizar a Premium"
     * // POST /upgrade
     * // → Rol cambia a 'registrado'
     * // → Redirige a dashboard: "¡Bienvenido a Premium!"
     */
    public function upgrade(Request $request)
    {
        // Obtener usuario autenticado actual
        $user = Auth::user();

        // Actualizar rol a Premium (registrado)
        $user->update([
            'rol' => User::ROLE_REGISTRADO,
        ]);

        // Redirigir con mensaje de bienvenida
        return redirect()->route('dashboard')
            ->with('success', '¡Bienvenido a Premium! Ya tienes acceso a todas las funcionalidades.');
    }
}