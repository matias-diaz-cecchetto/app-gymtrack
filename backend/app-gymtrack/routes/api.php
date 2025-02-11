<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

});
