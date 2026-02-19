<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Nota
 * 
 * Representa anotaciones de usuarios Premium sobre propiedades catastrales.
 * Las notas pueden ser privadas (solo visibles para el autor) o públicas
 * (visibles para todos los usuarios del sistema).
 * 
 * Funcionalidad exclusiva Premium:
 * - Solo usuarios con rol 'registrado' o 'admin' pueden crear notas
 * - Sistema de privacidad: privadas vs públicas
 * - Múltiples notas por propiedad
 * - Edición y eliminación solo por el autor
 * 
 * Casos de uso:
 * - Anotaciones personales sobre visitas o gestiones
 * - Recordatorios de trámites pendientes
 * - Información adicional sobre la propiedad
 * - Compartir observaciones con otros usuarios (públicas)
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único de la nota
 * @property int $usuario_id ID del usuario autor de la nota
 * @property int $propiedad_id ID de la propiedad anotada
 * @property string $texto Contenido de la nota (máx 1000 caracteres)
 * @property string $tipo Tipo de nota: 'privada' o 'publica'
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última edición
 * 
 * @property-read User $usuario Usuario autor de la nota
 * @property-read Propiedad $propiedad Propiedad asociada
 */
class Nota extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'notas';

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',
        'propiedad_id',
        'texto',
        'tipo', // 'privada' o 'publica'
    ];

    /**
     * Usuario autor de la nota
     * 
     * @return BelongsTo Instancia del usuario Premium
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Propiedad sobre la que se hizo la nota
     * 
     * @return BelongsTo Instancia de la propiedad
     */
    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }
}