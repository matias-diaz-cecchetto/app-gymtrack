<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model {
    use HasFactory;

    protected $fillable = [
        'miembro_id',
        'peso',
        'medidas',
        'fecha',
    ];

    protected $casts = [
        'medidas' => 'array',
    ];

    public function miembro() {
        return $this->belongsTo(User::class, 'miembro_id');
    }
}
