<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Registro de usuario
Route::post('register', [AuthController::class, 'register']);

// Login de usuario
Route::post('login', [AuthController::class, 'login']);

// Logout de usuario
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user/permissions', [AuthController::class, 'getPermissions']);
