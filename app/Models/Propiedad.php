<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Propiedad extends Model
{
    protected $table = 'propiedades';
    protected $fillable = [
        'user_id',
        'referencia_catastral',
        'clase',
        'provincia_codigo',
        'municipio_codigo',
        'provincia',
        'municipio',
        'direccion_text',
        'uso',
        'superficie_m2',
        'coef_participacion',
        'antiguedad_anios',
        'raw_json'   //Permite auditoría
    ];

    protected $casts = ['raw_json' => 'array'];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class, 'provincia_codigo', 'codigo');
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class, 'municipio_codigo', 'codigo');
    }

    public function unidadesConstructivas(): HasMany
    {
        return $this->hasMany(UnidadConstructiva::class);
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }
}
