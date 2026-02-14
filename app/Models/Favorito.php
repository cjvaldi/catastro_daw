<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorito extends Model
{
    protected $table = 'favoritos';
    protected $fillable = [
        'usuario_id',
        'propiedad_id',
        'etiqueta'
    ];
    
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class,'usuario_id');
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }
}
