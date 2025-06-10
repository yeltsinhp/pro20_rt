<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
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
            'role_id' => 'required|integer|exists:roles,id', // Validación del rol por ID
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Obtener el rol por ID
        $role = Role::find($request->role_id); // Buscar el rol por ID

        if (!$role) {
            return response()->json(['error' => 'Rol no válido'], 400);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id, // Asignar el rol por ID
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

            // Iniciar sesión y crear la cookie
            $token = $user->createToken('UserApp')->plainTextToken;

            // Almacenar el token en una cookie (esto es para asegurar que se pueda usar en el frontend)
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Si no son válidas las credenciales
        return response()->json(['error' => 'Credenciales inválidas'], 401);
    }


    // Logout de usuario
    public function logout(Request $request)
    {
        // Revocar el token actual
        $request->user()->currentAccessToken()->delete();

        // Eliminar la cookie de sesión
        Cookie::queue(Cookie::forget('role')); // Esto eliminará la cookie
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

    public function getRole()
    {
        $roles = Role::with('permissions')->get(); // Obtener todos los roles con sus permisos

        return response()->json($roles); // Devolver los roles y permisos en formato JSON
    }


}
