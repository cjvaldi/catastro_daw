<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // Roles del sistema
    public const ROLE_ADMIN = 'admin';
    public const ROLE_REGISTRADO = 'registrado';
    public const ROLE_VISITANTE = 'visitante';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class, 'usuario_id');
    }

    public  function notas()
    {
        return $this->hasMany(Nota::class, 'usuario_id');
    }

    // Funciones para la verificacion de los usuarios if (auth()->user()->isAdmin())

    public function isAdmin(): bool
    {
        return $this->rol === self::ROLE_ADMIN;
    }

    public function isRegistrado():bool
    {
        return $this->rol === self::ROLE_REGISTRADO;
    }

    public function isVisitante():bool
    {
        return $this->roll === self::ROLE_VISITANTE;
    }
    
}
