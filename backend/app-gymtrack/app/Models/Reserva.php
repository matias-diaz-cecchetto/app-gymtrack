<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'clase_id',
        'miembro_id',
        'estado',
    ];

    public function clase() {
        return $this->belongsTo(ClassModel::class, 'clase_id');
    }

    public function miembro() {
        return $this->belongsTo(User::class, 'miembro_id');
    }
}
