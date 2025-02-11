<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class MemberController extends Controller
{
    public function index()
    {
        try {
            $members = User::where('rol', 'Miembro')->get();

            return response()->json([
                'success' => true,
                'result' => 'ok',
                'message' => 'Lista de miembros obtenida con éxito',
                'version' => '1.0',
                'data' => $members
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $member = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'rol' => 'Miembro',
            ]);

            return response()->json([
                'success' => true,
                'result' => 'ok',
                'message' => 'Miembro creado con éxito',
                'version' => '1.0',
                'data' => $member
            ], Response::HTTP_CREATED);
        } catch (ValidationException $ex) {
            return $this->validationErrorResponse($ex);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    public function show(int $id )
    {
        try {
            $member = User::where('id', $id)->where('rol', 'Miembro')->first();

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'result' => 'ok',
                    'message' => 'Miembro no encontrado',
                    'version' => '1.0',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'result' => 'ok',
                'message' => 'Miembro encontrado',
                'version' => '1.0',
                'data' => $member
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $member = User::findOrFail($id);

            if ($member->rol !== 'Miembro') {
                return response()->json([
                    'success' => false,
                    'result' => 'ok',
                    'message' => 'Solo se pueden modificar miembros',
                    'version' => '1.0',
                    'data' => []
                ], Response::HTTP_FORBIDDEN);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes', 'string', 'email', Rule::unique('users', 'email')->ignore($member->id)]
            ]);

            $member->update($validated);

            return response()->json([
                'success' => true,
                'result' => 'ok',
                'message' => 'Miembro actualizado',
                'version' => '1.0',
                'data' => $member
            ], Response::HTTP_OK);
        } catch (ValidationException $ex) {
            return $this->validationErrorResponse($ex);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    public function destroy($id)
    {
        try {
            $member = User::findOrFail($id);

            if ($member->rol !== 'Miembro') {
                return response()->json([
                    'success' => false,
                    'result' => 'ok',
                    'message' => 'Solo se pueden eliminar miembros',
                    'version' => '1.0',
                    'data' => []
                ], Response::HTTP_FORBIDDEN);
            }

            $member->delete();

            return response()->json([
                'success' => true,
                'result' => 'ok',
                'message' => 'Miembro eliminado',
                'version' => '1.0',
                'data' => []
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->errorResponse($ex);
        }
    }

    // Métodos para manejar errores de validación y generales
    private function validationErrorResponse(ValidationException $ex)
    {
        return response()->json([
            'success' => false,
            'result' => 'ok',
            'message' => $ex->getMessage(),
            'version' => '1.0',
            'data' => []
        ], Response::HTTP_BAD_REQUEST);
    }

    private function errorResponse(\Exception $ex)
    {
        return response()->json([
            'success' => false,
            'result' => 'ok',
            'message' => $ex->getMessage(),
            'version' => '1.0',
            'data' => []
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
