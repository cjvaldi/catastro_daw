<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Búsqueda
 * 
 * Registra el historial completo de búsquedas realizadas por usuarios autenticados.
 * Almacena tanto búsquedas por referencia catastral como búsquedas por dirección,
 * permitiendo al usuario revisar y repetir consultas anteriores.
 * 
 * Funcionalidad:
 * - Registro automático de todas las búsquedas exitosas
 * - Diferenciación entre búsqueda por referencia y por dirección
 * - Contador de resultados obtenidos
 * - Botón "Repetir búsqueda" en el historial
 * - Paginación de 20 búsquedas por página
 * 
 * Tipos de búsqueda registrados:
 * - Referencia: Búsqueda por código catastral (14-20 caracteres)
 * - Dirección: Búsqueda por localización postal (Premium)
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único de la búsqueda
 * @property int $usuario_id ID del usuario que realizó la búsqueda
 * @property string $query_text Texto de la búsqueda (referencia o dirección formateada)
 * @property string|null $referencia_busqueda Referencia catastral si aplica
 * @property array|null $params_json Parámetros de búsqueda en formato JSON
 * @property int $result_count Número de resultados obtenidos
 * @property \Illuminate\Support\Carbon $created_at Fecha y hora de la búsqueda
 * @property \Illuminate\Support\Carbon $updated_at Fecha de actualización
 * 
 * @property-read User $usuario Usuario que realizó la búsqueda
 */
class Busqueda extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'busquedas';

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',
        'query_text',          // Texto descriptivo de la búsqueda
        'referencia_busqueda', // Referencia catastral (si aplica)
        'params_json',         // Parámetros completos para repetir búsqueda
        'result_count',        // Número de resultados encontrados
    ];

    /**
     * Configuración de casting de atributos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'params_json' => 'array', // JSON → Array para fácil acceso
    ];

    /**
     * Usuario que realizó la búsqueda
     * 
     * @return BelongsTo Instancia del usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}