<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name', // Validación de rol
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', $request->role)->first()->id, // Asignar el rol
        ]);

        // Crear un token para el nuevo usuario
        $token = $user->createToken('UserApp')->plainTextToken;

        // Retornar la respuesta con el token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    // Login de usuario
    public function login(Request $request)
    {
        // Validación de datos de login
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Verificar si las credenciales son correctas
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Verifica si el usuario existe y crea el token
            $token = $user->createToken('UserApp')->plainTextToken; // Esto debería funcionar si todo está bien configurado

            // Retornar la respuesta con el token
            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        // Si no son válidas las credenciales
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }


    // Logout de usuario
    public function logout(Request $request)
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente'], 200);
    }

    public function getPermissions(Request $request)
    {
        // Obtener el usuario autenticado
        $user = $request->user();

        // Obtener el rol del usuario y los permisos asociados
        $role = $user->role;
        $permissions = $role->permissions; // Aquí estamos accediendo a los permisos del rol del usuario

        // Retornar la información del usuario, su rol y los permisos
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role->name, // Nombre del rol
            ],
            'permissions' => $permissions->pluck('name'), // Solo devolver los nombres de los permisos
        ], 200);
    }


}
