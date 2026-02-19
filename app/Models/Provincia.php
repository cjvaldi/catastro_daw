<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Provincia
 * 
 * Representa las provincias españolas según el sistema de codificación del INE
 * (Instituto Nacional de Estadística). Actúa como tabla maestra de referencia
 * para la localización geográfica de propiedades.
 * 
 * Características:
 * - Utiliza código INE de 2 dígitos como clave primaria (no autoincremental)
 * - 52 provincias en total (50 + Ceuta + Melilla)
 * - Tabla de solo lectura (no tiene timestamps)
 * - Creada automáticamente mediante firstOrCreate si no existe
 * 
 * Ejemplos de códigos:
 * - 28: Madrid
 * - 08: Barcelona
 * - 41: Sevilla
 * - 46: Valencia
 * - 29: Málaga
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property string $codigo Código de provincia INE (2 dígitos) - PK
 * @property string $nombre Nombre oficial de la provincia
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<Municipio> $municipios
 * @property-read \Illuminate\Database\Eloquent\Collection<Propiedad> $propiedades
 */
class Provincia extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'provincias';

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
        'codigo', // Código INE de 2 dígitos
        'nombre', // Nombre oficial de la provincia
    ];

    /**
     * Municipios pertenecientes a esta provincia
     * 
     * Relación uno a muchos: Una provincia tiene múltiples municipios.
     * 
     * @return HasMany Colección de municipios de la provincia
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class, 'provincia_codigo', 'codigo');
    }

    /**
     * Propiedades ubicadas en esta provincia
     * 
     * Relación uno a muchos: Una provincia puede tener múltiples propiedades
     * registradas por usuarios del sistema.
     * 
     * @return HasMany Colección de propiedades de la provincia
     */
    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class, 'provincia_codigo', 'codigo');
    }
}