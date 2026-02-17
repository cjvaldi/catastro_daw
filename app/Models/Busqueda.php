<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Busqueda extends Model
{
    protected $table = 'busquedas';

    protected $fillable = [
        'usuario_id',
        'query_text',
        'referencia_busqueda',
        'params_json',
        'result_count',
    ];

    protected $casts = [
        'params_json' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}