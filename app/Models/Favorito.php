<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Favorito
 * 
 * Representa la relación entre un usuario Premium y una propiedad marcada como favorita.
 * Funciona como tabla pivote entre usuarios y propiedades, permitiendo a usuarios
 * Premium marcar propiedades de interés para acceso rápido.
 * 
 * Funcionalidad exclusiva Premium:
 * - Solo usuarios con rol 'registrado' o 'admin' pueden crear favoritos
 * - Permite organizar propiedades de interés personal
 * - Filtrado rápido: "Mostrar solo favoritas"
 * - Sistema toggle: añadir/quitar favoritos fácilmente
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único del favorito
 * @property int $usuario_id ID del usuario que marcó como favorito
 * @property int $propiedad_id ID de la propiedad marcada
 * @property string|null $etiqueta Etiqueta personalizada (uso futuro)
 * @property \Illuminate\Support\Carbon $created_at Fecha de marcado
 * @property \Illuminate\Support\Carbon $updated_at Fecha de actualización
 * 
 * @property-read User $usuario Usuario propietario del favorito
 * @property-read Propiedad $propiedad Propiedad marcada como favorita
 */
class Favorito extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'favoritos';

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'usuario_id',
        'propiedad_id',
        'etiqueta', // Campo reservado para funcionalidad futura (categorías)
    ];

    /**
     * Usuario que marcó la propiedad como favorita
     * 
     * @return BelongsTo Instancia del usuario Premium
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Propiedad marcada como favorita
     * 
     * @return BelongsTo Instancia de la propiedad
     */
    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }
}