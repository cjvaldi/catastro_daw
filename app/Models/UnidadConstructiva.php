<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnidadConstructiva extends Model
{
     protected $table = 'unidades_constructivas';
    protected $fillable = [
        'propiedad_id',
        'tipo_unidad',
        'tipologia',
        'superficie_m2',
        'localizacion_externa',
        'raw_json'
    ];

    protected $casts = [ 'raw_json'=>'array'];

    public function propiedad():BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    

}
