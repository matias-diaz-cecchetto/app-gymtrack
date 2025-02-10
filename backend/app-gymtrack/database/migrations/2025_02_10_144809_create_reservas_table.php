<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clase_id')->constrained('clases')->onDelete('cascade');
            $table->foreignId('miembro_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['Pendiente', 'Confirmada', 'Cancelada'])->default('Pendiente');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('reservas');
    }
};
