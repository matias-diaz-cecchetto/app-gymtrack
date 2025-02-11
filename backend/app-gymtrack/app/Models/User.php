<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = ['Administrador', 'Miembro', 'Entrenador'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol'
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

    /**
     * Relación con las clases que entrena (solo si es 'Entrenador')
     */
    public function clasesEntrenadas() {
        return $this->hasMany(Clase::class, 'entrenador_id');
    }

    /**
     * Relación con las reservas de clases (solo si es 'Miembro')
     */
    public function reservas() {
        return $this->hasMany(Reserva::class, 'miembro_id');
    }

    /**
     * Relación con los progresos del usuario (Miembro o Entrenador que registra progreso)
     */
    public function progresos() {
        return $this->hasMany(Progress::class, 'miembro_id');
    }
}
