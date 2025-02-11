<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;

// Ruta de prueba de funcionamiento
Route::get('/', function(){
    return response()->json([
        'status' => 200,
        'message' => 'App Gymtrack online'
    ]);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/autenticado', function(){
        return response()->json([
            'status' => 200,
            'message' => 'User Authenticated'
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // Solo administradores pueden acceder a estas rutas
    Route::middleware('role:Administrador')->group(function () {
        Route::get('/members', [MemberController::class, 'index']);
        Route::get('/members/{id}', [MemberController::class, 'show']);
        Route::post('/members', [MemberController::class, 'store']);
        Route::put('/members/{id}', [MemberController::class, 'update']);
        Route::delete('/members/{id}', [MemberController::class, 'destroy']);
    });
});
