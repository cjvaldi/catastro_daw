<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogApi extends Model
{
    protected $table = 'logs_api';

    protected $fillable = [
        'usuario_id',
        'endpoint',
        'params_json',
        'response_code',
        'duration_ms',
        'response_json',
        'error_code',
        'error_desc',
    ];

    protected $casts = [
        'params_json' => 'array',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}