<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Unidad Constructiva
 * 
 * Representa las subdivisiones funcionales de un inmueble según la clasificación
 * del Catastro Español. Una propiedad puede estar compuesta por múltiples
 * unidades constructivas, cada una con su tipología y superficie.
 * 
 * Tipos de unidades comunes:
 * - VIVIENDA: Parte residencial principal del inmueble
 * - ELEMENTOS COMUNES: Zonas compartidas del edificio (portal, escaleras, etc.)
 * - LOCAL: Locales comerciales asociados al inmueble
 * - GARAJE: Plazas de aparcamiento o cocheras
 * - TRASTERO: Espacios de almacenamiento anexos
 * - PISCINA: Instalaciones deportivas
 * 
 * Cada unidad tiene su propia superficie que suma al total del inmueble.
 * El Catastro desglosa así para cálculos de valoración y tributación.
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único de la unidad constructiva
 * @property int $propiedad_id ID de la propiedad a la que pertenece
 * @property string|null $tipo_unidad Tipo de unidad (VIVIENDA, LOCAL, GARAJE, etc.)
 * @property string|null $tipologia Tipología constructiva (VIVIENDA COLECTIVA, etc.)
 * @property float|null $superficie_m2 Superficie de esta unidad en m²
 * @property string|null $localizacion_externa Localización externa (escalera, planta)
 * @property array|null $raw_json Datos completos de la API para auditoría
 * @property \Illuminate\Support\Carbon $created_at Fecha de creación
 * @property \Illuminate\Support\Carbon $updated_at Fecha de actualización
 * 
 * @property-read Propiedad $propiedad Propiedad a la que pertenece
 */
class UnidadConstructiva extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'unidades_constructivas';

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'propiedad_id',
        'tipo_unidad',         // VIVIENDA, LOCAL, GARAJE, etc.
        'tipologia',           // Tipología constructiva detallada
        'superficie_m2',       // Superficie de esta unidad específica
        'localizacion_externa', // Ubicación (escalera, planta)
        'raw_json',            // JSON completo de la API
    ];

    /**
     * Configuración de casting de atributos
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'raw_json' => 'array', // JSON → Array
    ];

    /**
     * Propiedad a la que pertenece esta unidad constructiva
     * 
     * Relación muchos a uno: Múltiples unidades pertenecen a una propiedad.
     * 
     * @return BelongsTo Instancia de la propiedad principal
     */
    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }
}