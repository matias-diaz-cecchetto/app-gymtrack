<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clase extends Model {
    use HasFactory;

    protected $table = 'clases';

    protected $fillable = [
        'nombre',
        'horario',
        'entrenador_id',
    ];

    public function entrenador() {
        return $this->belongsTo(User::class, 'entrenador_id');
    }

    public function reservas() {
        return $this->hasMany(Reserva::class, 'clase_id');
    }
}
