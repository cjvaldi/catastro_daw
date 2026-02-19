<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Propiedad Catastral
 * 
 * Representa un inmueble registrado en el Catastro Español y guardado por un usuario.
 * Almacena toda la información catastral obtenida de la API oficial del Catastro,
 * incluyendo datos de localización, características físicas y documentación completa.
 * 
 * Características principales:
 * - Vinculación a usuario propietario (quien guardó la propiedad)
 * - Relación con provincia y municipio (tablas maestras)
 * - Unidades constructivas (subdivisiones del inmueble)
 * - Sistema de favoritos (solo Premium)
 * - Sistema de notas públicas/privadas (solo Premium)
 * - Almacenamiento de JSON raw para auditoría y recuperación
 * 
 * Estructura de datos:
 * - Identificación: Referencia catastral única de 14-20 caracteres
 * - Localización: Provincia, municipio, dirección completa
 * - Características: Uso, superficie, antigüedad, coeficiente participación
 * - Auditoría: JSON completo de la respuesta original de la API
 * 
 * @package App\Models
 * @author Cristian Valdivieso
 * @version 1.0
 * 
 * @property int $id Identificador único de la propiedad
 * @property int $user_id ID del usuario que guardó la propiedad
 * @property string $referencia_catastral Referencia catastral única (14-20 caracteres)
 * @property string|null $clase Clase catastral (UR=Urbana, RU=Rústica)
 * @property string|null $provincia_codigo Código de provincia (2 dígitos)
 * @property string|null $municipio_codigo Código de municipio (5 dígitos: provincia + municipio)
 * @property string|null $provincia Nombre de la provincia
 * @property string|null $municipio Nombre del municipio
 * @property string|null $direccion_text Dirección completa formateada
 * @property string|null $tipo_via Tipo de vía (CL, AV, PS, etc.)
 * @property string|null $nombre_via Nombre de la vía sin tipo
 * @property string|null $numero Número de policía
 * @property string|null $bloque Bloque o portal (si aplica)
 * @property string|null $escalera Escalera (si aplica)
 * @property string|null $planta Número de planta (PB, 01, 02, etc.)
 * @property string|null $puerta Puerta o letra
 * @property string|null $codigo_postal Código postal de 5 dígitos
 * @property string|null $distrito_municipal Distrito dentro del municipio
 * @property string|null $uso Uso catastral (Residencial, Oficinas, Industrial, etc.)
 * @property float|null $superficie_m2 Superficie construida en metros cuadrados
 * @property float|null $coef_participacion Coeficiente de participación en elementos comunes
 * @property int|null $antiguedad_anios Antigüedad del inmueble en años
 * @property array|null $raw_json JSON completo de respuesta API (para auditoría)
 * @property \Illuminate\Support\Carbon $created_at Fecha de guardado
 * @property \Illuminate\Support\Carbon $updated_at Fecha de última actualización
 * 
 * @property-read User $user Usuario propietario del registro
 * @property-read Provincia $provincia Provincia (relación maestro)
 * @property-read Municipio $municipio Municipio (relación maestro)
 * @property-read \Illuminate\Database\Eloquent\Collection<UnidadConstructiva> $unidadesConstructivas
 * @property-read \Illuminate\Database\Eloquent\Collection<Favorito> $favoritos
 * @property-read \Illuminate\Database\Eloquent\Collection<Nota> $notas
 */
class Propiedad extends Model
{
    /**
     * Nombre de la tabla en la base de datos
     * 
     * @var string
     */
    protected $table = 'propiedades';

    /**
     * Atributos asignables en masa (mass assignment)
     * 
     * Lista completa de campos que pueden ser rellenados mediante create() o fill().
     * Incluye todos los datos extraídos de la API del Catastro más metadatos.
     * 
     * Campos clave:
     * - user_id: Vincula la propiedad al usuario que la guardó
     * - referencia_catastral: Identificador único del Catastro
     * - raw_json: Respuesta completa de la API para auditoría y recuperación
     * 
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'referencia_catastral',
        'clase',
        'provincia_codigo',
        'municipio_codigo',
        'provincia',
        'municipio',
        'direccion_text',
        'tipo_via',
        'nombre_via',
        'numero',
        'bloque',
        'escalera',
        'planta',
        'puerta',
        'codigo_postal',
        'distrito_municipal',
        'uso',
        'superficie_m2',
        'coef_participacion',
        'antiguedad_anios',
        'raw_json', // Permite auditoría y recuperación de datos completos
    ];

    /**
     * Configuración de casting de atributos
     * 
     * Conversiones automáticas al acceder a los atributos:
     * - raw_json: JSON string → Array PHP (facilita manipulación)
     * 
     * @var array<string, string>
     */
    protected $casts = [
        'raw_json' => 'array',
    ];

    // ============================================================
    // RELACIONES ELOQUENT
    // ============================================================

    /**
     * Usuario que guardó la propiedad
     * 
     * Relación muchos a uno: Múltiples propiedades pertenecen a un usuario.
     * Permite identificar quién guardó cada propiedad en el sistema.
     * 
     * @return BelongsTo Instancia del usuario propietario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Provincia donde se ubica la propiedad
     * 
     * Relación muchos a uno: Múltiples propiedades pertenecen a una provincia.
     * La relación se establece mediante el código de provincia (2 dígitos).
     * 
     * NOTA: Utiliza 'codigo' como clave foránea en lugar de 'id' para
     * coincidir con el sistema de codificación oficial del INE.
     * 
     * @return BelongsTo Instancia de la provincia
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'provincia_codigo', 'codigo');
    }

    /**
     * Municipio donde se ubica la propiedad
     * 
     * Relación muchos a uno: Múltiples propiedades pertenecen a un municipio.
     * La relación se establece mediante el código de municipio (5 dígitos:
     * código de provincia + código de municipio).
     * 
     * NOTA: Utiliza 'codigo' como clave foránea para coincidir con el
     * sistema de codificación oficial del INE (Instituto Nacional de Estadística).
     * 
     * @return BelongsTo Instancia del municipio
     */
    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_codigo', 'codigo');
    }

    /**
     * Unidades constructivas que componen la propiedad
     * 
     * Relación uno a muchos: Una propiedad puede tener múltiples unidades
     * constructivas (vivienda principal, elementos comunes, garaje, trastero, etc.).
     * 
     * Las unidades constructivas representan las subdivisiones funcionales
     * del inmueble según el Catastro, cada una con su superficie y tipología.
     * 
     * Ejemplos de unidades:
     * - VIVIENDA: Parte residencial principal
     * - ELEMENTOS COMUNES: Zonas compartidas del edificio
     * - LOCAL: Locales comerciales asociados
     * - GARAJE: Plazas de aparcamiento
     * - TRASTERO: Espacios de almacenamiento
     * 
     * @return HasMany Colección de unidades constructivas
     */
    public function unidadesConstructivas(): HasMany
    {
        return $this->hasMany(UnidadConstructiva::class);
    }

    /**
     * Marcadores de favorito de la propiedad (funcionalidad Premium)
     * 
     * Relación uno a muchos: Una propiedad puede ser marcada como favorita
     * por múltiples usuarios Premium. Cada registro en 'favoritos' representa
     * un usuario que marcó esta propiedad.
     * 
     * Uso típico: Verificar si el usuario actual tiene marcada como favorita:
     * $propiedad->favoritos()->where('usuario_id', auth()->id())->exists()
     * 
     * @return HasMany Colección de favoritos (tabla intermedia)
     */
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    /**
     * Notas asociadas a la propiedad (funcionalidad Premium)
     * 
     * Relación uno a muchos: Una propiedad puede tener múltiples notas
     * de diferentes usuarios Premium. Las notas pueden ser:
     * - Privadas: Solo visibles para quien las creó
     * - Públicas: Visibles para todos los usuarios
     * 
     * Casos de uso:
     * - Anotaciones personales sobre la propiedad
     * - Recordatorios de visitas o gestiones
     * - Información adicional para compartir
     * 
     * @return HasMany Colección de notas de la propiedad
     */
    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }
}