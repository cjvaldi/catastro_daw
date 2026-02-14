<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Provincia extends Model
{
    protected $table = 'provincias';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keytype='string';
    protected $fillable =['codigo', 'nombre'];

    public function municipios()
    {
        return $this->hasMany(Municipio::class,'provincia_codigo','codigo');
    }

    public function propiedades()
    {
        return $this->hasMany(Propiedad::class,'provincia_codigo','codigo');
    }
}