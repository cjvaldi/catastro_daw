<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Municipio
 * 
 * Representa los municipios españoles según el sistema de codificación del INE
 * (Instituto Nacional de Estadística). Actúa como tabla maestra de referencia
 * para la localización geográfica de propiedades.
 * 
 * Características:
 * - Utiliza código INE de 5 dígitos como clave primaria (no autoincremental)
 * - Código compuesto: 2 dígitos de provincia + 3 dígitos de municipio
 * - Más de 8,000 municipios en España
 * - Tabla de solo lectura (no tiene timestamps)
 * - Creada automáticamente mediante firstOrCreate si no existe
 * 
 * Ejemplos de códigos:
 * - 28079: Madrid (provincia 28 + municipio 079)
 * - 08019: Barcelona (provincia 08 + municipio 019)
 * - 41091: Sevilla (provincia 41 + municipio 091)
 * - 46250: Valencia (provincia 46 + municipio 250)
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property string $codigo Código de municipio INE (5 dígitos) - PK
 * @property string $nombre Nombre oficial del municipio
 * @property string $provincia_codigo Código de provincia (FK a provincias)
 * 
 * @property-read Provincia $provincia Provincia a la que pertenece
 * @property-read \Illuminate\Database\Eloquent\Collection<Propiedad> $propiedades
 */
class Municipio extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'municipios';

    /**
     * Clave primaria personalizada (código INE en lugar de id autoincremental)
     * 
     * @var string
     */
    protected $primaryKey = 'codigo';

    /**
     * Indica que la clave primaria NO es autoincremental
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipo de dato de la clave primaria
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Desactiva timestamps (created_at, updated_at)
     * 
     * Tabla maestra de solo lectura, no requiere auditoría de cambios.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atributos asignables en masa
     * 
     * @var array<string>
     */
    protected $fillable = [
        'codigo',           // Código INE de 5 dígitos (provincia + municipio)
        'nombre',           // Nombre oficial del municipio
        'provincia_codigo', // Código de la provincia (FK)
    ];

    /**
     * Provincia a la que pertenece el municipio
     * 
     * Relación muchos a uno: Múltiples municipios pertenecen a una provincia.
     * 
     * @return BelongsTo Instancia de la provincia
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'provincia_codigo', 'codigo');
    }

    /**
     * Propiedades ubicadas en este municipio
     * 
     * Relación uno a muchos: Un municipio puede tener múltiples propiedades
     * registradas por usuarios del sistema.
     * 
     * @return HasMany Colección de propiedades del municipio
     */
    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class, 'municipio_codigo', 'codigo');
    }
}