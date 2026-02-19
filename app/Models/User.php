<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Usuario del sistema CatastroApp
 * 
 * Gestiona la autenticación, autorización y datos de usuarios.
 * Implementa un sistema de roles jerárquico con tres niveles de acceso:
 * 
 * Roles del sistema:
 * - Visitante: Usuario registrado gratuito con funciones básicas
 * - Registrado (Premium): Usuario con acceso a funcionalidades avanzadas
 * - Admin: Administrador con control total del sistema
 * 
 * Funcionalidades por rol:
 * 
 * VISITANTE:
 * - Búsqueda por referencia catastral
 * - Guardar propiedades
 * - Ver historial de búsquedas
 * - Imprimir fichas
 * 
 * PREMIUM (REGISTRADO):
 * - Todo lo de Visitante +
 * - Búsqueda por dirección
 * - Sistema de favoritos
 * - Notas privadas/públicas
 * - Filtros avanzados
 * 
 * ADMIN:
 * - Todo lo de Premium +
 * - Dashboard con estadísticas
 * - Gestión de usuarios
 * - Ver logs de API
 * - Cambiar roles
 * - Activar/desactivar usuarios
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único del usuario
 * @property string $name Nombre completo del usuario
 * @property string $email Correo electrónico (único)
 * @property string $password Contraseña hasheada con Bcrypt
 * @property string $rol Rol del usuario (visitante|registrado|admin)
 * @property bool $activo Estado de la cuenta (true=activa, false=desactivada)
 * @property \Illuminate\Support\Carbon|null $ultimo_acceso Fecha del último login
 * @property \Illuminate\Support\Carbon|null $email_verified_at Fecha de verificación de email
 * @property string|null $remember_token Token para "recordarme"
 * @property \Illuminate\Support\Carbon $created_at Fecha de registro
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Propiedad> $propiedades
 * @property-read \Illuminate\Database\Eloquent\Collection<Favorito> $favoritos
 * @property-read \Illuminate\Database\Eloquent\Collection<Nota> $notas
 * @property-read \Illuminate\Database\Eloquent\Collection<Busqueda> $busquedas
 * @property-read \Illuminate\Database\Eloquent\Collection<LogApi> $logsApi
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Constantes de roles del sistema
     * 
     * Definen los tres roles disponibles en la aplicación.
     * Usar estas constantes en lugar de strings hardcodeados previene errores.
     * 
     * @var string
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_REGISTRADO = 'registrado'; // Premium
    public const ROLE_VISITANTE = 'visitante';

    /**
     * Atributos asignables en masa (mass assignment)
     * 
     * Lista de campos que pueden ser rellenados mediante User::create()
     * o $user->fill(). Protege contra asignación masiva no autorizada.
     * 
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
        'ultimo_acceso',
    ];

    /**
     * Atributos ocultos en serialización JSON
     * 
     * Estos campos no se incluyen cuando el modelo se convierte a JSON
     * (útil en APIs y responses). Protege información sensible.
     * 
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Configuración de casting de atributos
     * 
     * Define cómo deben ser convertidos los atributos al acceder a ellos:
     * - Fechas → Carbon instances (facilita manipulación de fechas)
     * - Boolean → true/false en lugar de 0/1
     * - Password → Hasheado automático con Bcrypt
     * 
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultimo_acceso'     => 'datetime',
            'password'          => 'hashed',
            'activo'            => 'boolean',
        ];
    }

    // ============================================================
    // RELACIONES ELOQUENT
    // ============================================================

    /**
     * Propiedades guardadas por el usuario
     * 
     * Relación uno a muchos: Un usuario puede guardar múltiples propiedades.
     * Las propiedades se vinculan mediante la clave foránea 'user_id'.
     * 
     * @return HasMany Colección de propiedades del usuario
     */
    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class);
    }

    /**
     * Favoritos marcados por el usuario (solo Premium)
     * 
     * Relación uno a muchos: Un usuario puede marcar múltiples propiedades
     * como favoritas. Tabla intermedia: 'favoritos'.
     * 
     * @return HasMany Colección de favoritos del usuario
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class, 'usuario_id');
    }

    /**
     * Notas creadas por el usuario (solo Premium)
     * 
     * Relación uno a muchos: Un usuario puede crear múltiples notas
     * en diferentes propiedades. Las notas pueden ser privadas o públicas.
     * 
     * @return HasMany Colección de notas del usuario
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class, 'usuario_id');
    }

    /**
     * Historial de búsquedas del usuario
     * 
     * Relación uno a muchos: Cada búsqueda realizada queda registrada.
     * Incluye búsquedas por referencia y por dirección.
     * 
     * @return HasMany Colección de búsquedas realizadas
     */
    public function busquedas(): HasMany
    {
        return $this->hasMany(Busqueda::class, 'usuario_id');
    }

    /**
     * Logs de llamadas a la API del Catastro realizadas por el usuario
     * 
     * Relación uno a muchos: Registro de auditoría de todas las consultas
     * a la API externa. Incluye métricas de rendimiento y detección de errores.
     * 
     * @return HasMany Colección de logs de API
     */
    public function logsApi(): HasMany
    {
        return $this->hasMany(LogApi::class, 'usuario_id');
    }

    // ============================================================
    // MÉTODOS DE VERIFICACIÓN DE ROLES
    // ============================================================

    /**
     * Verifica si el usuario es Administrador
     * 
     * Los administradores tienen acceso completo al sistema, incluyendo
     * panel de administración, gestión de usuarios y visualización de logs.
     * 
     * @return bool True si el usuario es admin, false en caso contrario
     * 
     * @example
     * if (auth()->user()->isAdmin()) {
     *     // Mostrar panel de administración
     * }
     */
    public function isAdmin(): bool
    {
        return $this->rol === self::ROLE_ADMIN;
    }

    /**
     * Verifica si el usuario es Registrado (Premium)
     * 
     * Los usuarios registrados tienen acceso a funcionalidades avanzadas:
     * búsqueda por dirección, favoritos, y sistema de notas.
     * 
     * @return bool True si el usuario es Premium, false en caso contrario
     * 
     * @example
     * if (auth()->user()->isRegistrado()) {
     *     // Mostrar formulario de búsqueda por dirección
     * }
     */
    public function isRegistrado(): bool
    {
        return $this->rol === self::ROLE_REGISTRADO;
    }

    /**
     * Verifica si el usuario es Visitante (gratuito)
     * 
     * Los visitantes tienen acceso a funcionalidades básicas:
     * búsqueda por referencia, guardar propiedades e historial.
     * 
     * @return bool True si el usuario es Visitante, false en caso contrario
     */
    public function isVisitante(): bool
    {
        return $this->rol === self::ROLE_VISITANTE;
    }

    /**
     * Verifica si el usuario tiene uno o más roles específicos
     * 
     * Método flexible que acepta un rol individual o un array de roles.
     * Útil en middleware y gates para verificar múltiples roles a la vez.
     * Implementa operador OR: retorna true si tiene AL MENOS UNO de los roles.
     * 
     * @param string|array<string> $roles Rol único o array de roles permitidos
     * 
     * @return bool True si el usuario tiene alguno de los roles especificados
     * 
     * @example
     * // Verificar un solo rol
     * if ($user->hasRole('admin')) { }
     * 
     * @example
     * // Verificar múltiples roles (OR)
     * if ($user->hasRole(['registrado', 'admin'])) {
     *     // Usuario es Premium O Admin
     * }
     */
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->rol, (array) $roles);
    }

    /**
     * Verifica si el usuario tiene acceso a funcionalidades Premium
     * 
     * Método de conveniencia que retorna true para usuarios Registrado Y Admin.
     * Los administradores también tienen acceso Premium además de sus
     * funciones administrativas.
     * 
     * Útil para mostrar/ocultar elementos en vistas y proteger funciones:
     * - Búsqueda por dirección
     * - Sistema de favoritos
     * - Creación de notas
     * - Filtros avanzados
     * 
     * @return bool True si el usuario tiene acceso Premium, false en caso contrario
     * 
     * @example
     * @if(auth()->user()->isPremium())
     *     <button>⭐ Añadir a Favoritos</button>
     * @else
     *     <button disabled>🔒 Premium</button>
     * @endif
     */
    public function isPremium(): bool
    {
        return in_array($this->rol, [self::ROLE_REGISTRADO, self::ROLE_ADMIN]);
    }
}