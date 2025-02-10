<?php

namespace App\Http\Controllers;

use App\Models\JsonResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'rol' => 'required|string|in:' . implode(',', User::ROLES),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'], // Asignamos el rol validado
        ]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Ocurrió un error durante el registro',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where("email", "=", $request->email)->first();

            if (isset($user)) {
                if (Hash::check($request->password, $user->password)) {

                    $token = $user->createToken("auth_token")->plainTextToken;

                    return response()->json([
                        'success' => true,
                        'result' => 'ok',
                        'message' => 'Login exitoso',
                        'version' => '1.0',
                        'data' => [
                            "user" => $user,
                            "access_token" => $token,
                            "token_type" => "Bearer",
                        ]
                    ], Response::HTTP_OK);
                } else {
                    return response()->json([
                        'success' => false,
                        'result' => 'ok',
                        'message' => 'Password incorrecto',
                        'version' => '1.0',
                        'data' => []
                    ], Response::HTTP_NOT_FOUND);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'result' => 'ok',
                    'message' => 'Email incorrecto',
                    'version' => '1.0',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (ValidationException $ex) {
            return response()->json([
                'success' => false,
                'result' => 'ok',
                'message' => $ex->getMessage(),
                'version' => '1.0',
                'data' => []
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'result' => 'ok',
                'message' => $ex->getMessage(),
                'version' => '1.0',
                'data' => []
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function logout()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay usuario autenticado o el token no es válido'
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function unregister(Request $request)
    {
        try {
            $user = $request->user();

            $user->tokens()->delete();
            $user->delete();

            return response()->json([
                'message' => 'Account deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during account deletion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
