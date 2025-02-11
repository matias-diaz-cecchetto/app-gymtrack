<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    // Listar clases disponibles
    public function index()
    {
        $clases = Clase::with('entrenador')->get();

        return response()->json([
            'success' => true,
            'message' => 'Listado de clases',
            'data' => $clases
        ]);
    }

    // Crear una nueva clase (solo Administradores)
    public function store(Request $request)
    {
        // Validar que el administrador asigne solo un entrenador
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'horario' => 'required|date_format:Y-m-d H:i:s',
            'entrenador_id' => 'required|exists:users,id|role:Entrenador', // Asegurarse de que el entrenador sea un usuario de rol 'Entrenador'
        ]);

        // Crear la clase
        $clase = Clase::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Clase creada con éxito',
            'data' => $clase
        ], 201);
    }

    // Actualizar una clase (solo Administradores)
    public function update(Request $request, $id)
    {
        $clase = Clase::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'horario' => 'sometimes|date_format:Y-m-d H:i:s',
            'entrenador_id' => 'sometimes|exists:users,id|role:Entrenador', // Asegurarse de que el entrenador sea un usuario de rol 'Entrenador'
        ]);

        $clase->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Clase actualizada',
            'data' => $clase
        ]);
    }

    // Reservar una clase (Solo Miembros)
    public function reserve($id)
    {
        $user = Auth::user();

        // Verificar si el usuario es un Miembro
        if ($user->rol !== 'Miembro') {
            return response()->json([
                'success' => false,
                'message' => 'Solo los miembros pueden realizar reservas',
            ], 400);
        }

        $clase = Clase::findOrFail($id);

        // Verificar si el usuario ya reservó esta clase
        if ($user->reservas->where('class_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una reserva para esta clase',
            ], 400);
        }

        // Registrar la reserva
        Reserva::create([
            'miembro_id' => $user->id,
            'class_id' => $clase->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reserva realizada con éxito'
        ]);
    }
}
