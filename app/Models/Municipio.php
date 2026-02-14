<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $primaryKey='codigo';
    public $incrementing = false;
    protected $keyType='string';
    public $timestamps=false;

    protected $fillable=['codigo', 'nombre', 'provincia_codigo'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class,'provincia_codigo','codigo');
    }

    public function propiedad()
    {
        return $this->hasMany(Propiedad::class,'municipio_codigo','codigo');
    }
}
