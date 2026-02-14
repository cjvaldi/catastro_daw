<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Propiedad extends Model
{
    protected $table = 'propiedades';
    protected $fillable = [
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
        'domicilio_tributario',
        'raw_json'
    ];

    protected $casts = ['raw_json'=>'array'];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class,'provincia_codigo','codigo');
    }

    public function municipio():BelongsTo
    {
        return $this->belonsTo(Municipio::class,'municipio_codigo','codigo');
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(UnidadConstructiva::class);
    }

    public function favoritos():HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }
}
