<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Log de API
 * 
 * Registra todas las llamadas realizadas a la API oficial del Catastro Español
 * para fines de auditoría, monitoreo de rendimiento y detección de errores.
 * 
 * Funcionalidad:
 * - Registro automático de todas las peticiones (exitosas y fallidas)
 * - Métricas de rendimiento (duración en milisegundos)
 * - Detección y categorización de errores
 * - Trazabilidad por usuario (si está autenticado)
 * - Auditoría completa para administradores
 * 
 * Casos de uso:
 * - Monitorear disponibilidad de la API del Catastro
 * - Detectar endpoints con mayor tasa de error
 * - Analizar tiempos de respuesta
 * - Identificar patrones de uso
 * - Debugging de problemas de integración
 * 
 * Endpoints registrados:
 * - /Consulta_DNPRC: Búsqueda por referencia catastral
 * - /Consulta_DNPLOC: Búsqueda por dirección postal
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único del log
 * @property int|null $usuario_id ID del usuario (null si anónimo)
 * @property string $endpoint Endpoint consultado de la API
 * @property array|null $params_json Parámetros enviados en la petición
 * @property int $response_code Código HTTP de respuesta (200, 404, 500, etc.)
 * @property int $duration_ms Duración de la petición en milisegundos
 * @property string|null $response_json Respuesta completa de la API (JSON)
 * @property string|null $error_code Código de error si aplica
 * @property string|null $error_desc Descripción del error si aplica
 * @property \Illuminate\Support\Carbon $created_at Fecha y hora de la llamada
 * @property \Illuminate\Support\Carbon $updated_at Fecha de actualización
 * 
 * @property-read User|null $usuario Usuario que realizó la llamada (null si anónimo)
 */
class LogApi extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'logs_api';

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',      // Usuario (null para anónimos)
        'endpoint',        // Ruta de la API consultada
        'params_json',     // Parámetros de la petición
        'response_code',   // Código HTTP (200, 404, 500...)
        'duration_ms',     // Tiempo de respuesta en ms
        'response_json',   // Respuesta completa de la API
        'error_code',      // Código de error interno
        'error_desc',      // Descripción del error
    ];

    /**
     * Configuración de casting de atributos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'params_json' => 'array', // JSON → Array para análisis
    ];

    /**
     * Usuario que realizó la llamada a la API
     * 
     * Puede ser null para búsquedas de usuarios anónimos (sin login).
     * 
     * @return BelongsTo Instancia del usuario o null
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}